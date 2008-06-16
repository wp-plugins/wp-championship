<?php

// 
// function to show an admin message on an admin page
//
function admin_message($msg) {
  echo "<div class='updated'><p><strong>";
  echo $msg;
  echo "</strong></p></div>\n";
}


//
// return an html form field selector for num groups with id
//
function get_group_selector($num,$id,$sel=-1) 
{ 
  $groupstr="ABCDEFGHIJKLM";
  $out = "";
  $out .= '<select name="'.$id.'" id="'.$id.'" class="postform">'."\n";
  // build group selection box 
  for ($i = 0; $i < $num; $i++) {
    $charone=substr($groupstr,$i,1);
    $out .= '<option value="'.$charone.'"';
    if ( $charone == $sel )
      $out .= ' selected';
    $out .= '>'.$charone.'</option>';
  }
  $out .= '</select>';
  return $out;
}

//
// returns an html form field selector for num places with id
//
function get_place_selector($num,$id,$sel=-1) 
{
  $out = "";
  $out .= '<select name="'.$id.'" id="'.$id.'" class="postform">'."\n";
  // build group selection box 
  for ($i = 1; $i <= $num; $i++) {
    $out .= '<option value="'.$i.'"';
    if ( $i == $sel )
      $out .= ' selected';
    $out .= '>'.$i.'</option>';
  }
  $out .= '</select>';
  return $out;
}

//
// returns an html form field selector for the quarterfinal with id
//
function get_quarterfinal_selector($id,$sel=-1) 
{
  $out = "";
  $out .= '<select name="'.$id.'" id="'.$id.'" class="postform">'."\n";
  // build group selection box 
  for ($i = 1; $i <= 8; $i++) {
    $out .= '<option value="'. $i . '"';
    if ( $i == $sel )
      $out .= ' selected';
    $out .= '>'. __("Winner Ro16, Match #"). $i.'</option>';
  }
  $out .= '</select>';
  return $out;
}


//
// returns an html form field selector for the semifinal with id
//
function get_semifinal_selector($id,$sel=-1) 
{
  $out = "";
  $out .= '<select name="'.$id.'" id="'.$id.'" class="postform">'."\n";
  // build group selection box 
  for ($i = 1; $i <= 4; $i++) {
    $out .= '<option value="'. $i . '"';
    if ( $i == $sel )
      $out .= ' selected';
    $out .= '>'. __("Winner QF, Match #"). $i.'</option>';
  }
  $out .= '</select>';
  return $out;
}


//
// calculates the points for each user and match and stores it in cs_tipp
//
function calc_points($new=false) 
{
  include ("globals.php");
  global $wpdb;
  
  $wpdb->show_errors(true);
  
  // for testing
  $new=true;

  // alles zuruekcsetzen
  if ($new) {
    $sql = "update $cs_tipp set points=-1 where points <>-1";
    $res = $wpdb->query($sql);
  }
  
  // punktevorgaben lesen
 $cs_pts_tipp= get_option("cs_pts_tipp");           // korrekter tipp
 $cs_pts_tendency=get_option("cs_pts_tendency");    // tendenz
 $cs_pts_supertipp=get_option("cs_pts_supertipp");  // tendenz und tordifferenz
 $cs_pts_champ=get_option("cs_pts_champ");          // championtipp

 // genauer treffer
 // sql vor mysql 5.0
 //$sql= "update $cs_tipp  b set points=5 where mid in ( select a.mid from $cs_match a where a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and a.result1=b.result1 and a.result2=b.result2) and b.points = -1;";
 // sql for mysql 4.x
 $sql = "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1=b.result1 and a.result2=b.result2 and a.result1 <> -1 and a.result2 <> -1 set b.points= $cs_pts_tipp where b.points  =-1 and b.result1>-1 and b.result2>-1;";
 $res = $wpdb->query($sql);
 
 // tordifferenz
 // mysql 5.0
 // $sql= "update  $cs_tipp b set points=3 where mid in ( select a.mid from $cs_match  a where a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and abs(a.result1 - a.result2) = abs(b.result1 - b.result2)) and b.points = -1;";
 // mysql 4.x
 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and abs(a.result1 - a.result2) = abs(b.result1 - b.result2) and ( (a.result1>a.result2 and b.result1>b.result2) or (a.result1<a.result2 and b.result1<b.result2 ) or (a.result1=a.result2 and b.result1=b.result2) )set points= $cs_pts_supertipp where b.points = -1 and b.result1>-1 and b.result2>-1;";
 $res = $wpdb->query($sql);
 
 // tendenz
 // mysql 5.0
 //$sql= "update $cs_tipp b set points=1 where mid in ( select a.mid from $cs_match a where a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( (a.result1<a.result2 and b.result1<b.result2) or (a.result1=a.result2 and b.result1=b.result2) or (a.result1>a.result2 and b.result1>b.result2)  )) and b.points = -1;";
 // mysql 4.x
 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( (a.result1<a.result2 and b.result1<b.result2) or (a.result1=a.result2 and b.result1=b.result2) or (a.result1>a.result2 and b.result1>b.result2)  ) set points= $cs_pts_tendency where b.points = -1 and b.result1>-1 and b.result2>-1;";
 $res = $wpdb->query($sql);
 
 // falscher tipp (setzt alle restlichen auf 0)
 // mysql 5.0
 //$sql= "update  $cs_tipp b set points=0 where mid in ( select a.mid from  a where a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 ) and b.points = -1;";
 // mysql 4.x
 $sql= "update $cs_tipp b inner join $cs_match a on  a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1  set points=0 where b.points = -1 and b.result1>-1 and b.result2>-1;";
 $res = $wpdb->query($sql);
 
 // champion tipp addieren auf finalspiel
 //$sql="update  $cs_tipp b inner join $cs_match a on b.mid=a.mid and a.result1<> -1 and a.result2 <> -1 and a.matchtime = max(a.matchtime) and a.round='F' set points = points +10";
 //$res = $wpdb->query($sql);

 $sql="select max(matchtime) as mtime from $cs_match where round='F';";
 $ftime = $wpdb->get_row($sql);
 $fmatchtime = $ftime->mtime;
 $sql="select case winner when 1 then tid1 when 2 then tid2 end as winner from $cs_match a where a.round='F' and a.winner <> -1 and matchtime = '$fmatchtime';";
 $res=$wpdb->get_row($sql);

 $champion = $res->winner;

 if ($champion) {
   $sql="select userid from $cs_users where champion=$champion;";
   $res = $wpdb->get_results($sql);

   foreach($res as $r) {
     $sql="update $cs_tipp set points=points + $cs_pts_champ where userid=".$r->userid." limit 1;";
     $wpdb->query($sql);
   }
   
 }
}


// reporting functions
function get_ranking() {
  include("globals.php");
  global $wpdb;

  //select fuer ranking der tipper
  $sql = "select b.user_nicename, a.userid,sum(a.points) as points from $cs_tipp a inner join $wp_users b on a.userid=b.ID where points <> -1 group by b.user_nicename, a.userid order by points DESC;";
  $res = $wpdb->get_results($sql);

  return $res;
}


// liefert die platzierung der gruppe groupid und davon die ersten
// count plaetze zurueck. ist groupid nicht angegeben werden alle gruppen
// zuruekcgeliefert. ist count  = 0 werden alle teams zuruckgegegben
function get_team_clification($groupid='', $count=0)
{
  include("globals.php");
  global $wpdb;

  //$wpdb->show_errors(true);

  // punktvergabe fuer match einlesen
  $cs_pts_winner=get_option("cs_pts_winner");
  $cs_pts_looser=get_option("cs_pts_looser");
  $cs_pts_deuce=get_option("cs_pts_deuce");

  $sql1= <<<EOD
   create temporary table if not exists cs_tt
         select groupid,name,tid,icon,qualified,
	 sum(result1) as tore,
	 sum(result2) as gegentore, 
	 sum( case winner when 0 then $cs_pts_deuce when 1 then $cs_pts_winner else $cs_pts_looser end)  as points
	 from $cs_match 
	 inner join $cs_team 
	 on tid=tid1
	 where winner <>-1 and tid1<>0 and round='V'
         group by groupid,name,icon,qualified
	 UNION 
         select groupid,name,tid,icon,qualified,
	 sum(result2) as tore,
	 sum(result1) as gegentore, 
	 sum( case winner when 0 then $cs_pts_deuce when 2 then $cs_pts_winner else $cs_pts_looser end)  as points
	 from $cs_match 
	 inner join $cs_team 
	 on tid=tid2
	 where winner <>-1 and tid2<>0 and round='V'
         group by groupid,name,icon,qualified
	 UNION
         select distinct groupid,name,tid,icon,qualified, 
         0 as tore,0 as gegentore,0 as points
	 from $cs_match inner join $cs_team on tid=tid1
	 where winner =-1 and tid1<>0 and round ='V'
	 UNION
         select distinct groupid,name,tid,icon, qualified,
         0 as tore,0 as gegentore,0 as points
	 from $cs_match inner join $cs_team on tid=tid2
	 where winner =-1 and tid2<>0 and round='V';
EOD;
 
 $sql2= "select groupid, name,tid,icon,qualified, sum(tore) as store,sum(gegentore) as sgegentore, sum(points) as spoints, (sum(tore)-sum(gegentore)) as tdiff from cs_tt ";
 
 if ($groupid !="")
   $sql2.=" where groupid = '$groupid' ";
 
 $sql2 .= "group by groupid,name,icon order by groupid,qualified,spoints DESC,tdiff DESC"; 
 
 if ($count !=0)
   $sql2 .= " limit 0,$count";

 $sql2 .= ";";

 $sql3= "drop temporary table cs_tt;";
 
 $wpdb->query($sql1);
 $results = $wpdb->get_results($sql2);
 $wpdb->query($sql3);

 return $results;
}


// konvertiert einen teamcode der finalrunde in einen lesbaren text
function team2text($teamcode) {
  
  if (substr($teamcode,0,1) != '#')
    return $teamcode;

  $code1 = substr($teamcode,1,1);

  if ( $code1 == "W" or $code1 == "V") 
    $erg=($code1 == "W" ? __("Gewinner","wpcs") : __("Verlierer","wpcs") ).", ".__("Spielnr.","wpcs"). substr($teamcode,2);
  else
    $erg = __("Gruppe ","wpcs").$code1.", ".substr($teamcode,2).". ".__("Platz","wpcs");
  
  return $erg;
}


// aktualisiere die team eintraege der finalrundenspiele
function update_finals() {
  include("globals.php");
  global $wpdb;
  

  // Pseudo ids wieder aktivieren = alle Teams in finalrunde zuruecksetzen
  $sql="update  $cs_match set tid1=ptid1, tid2=ptid2 where round='F';";
  $wpdb->query($sql);


  // ermittle fertige gruppen mit platzierung
  // und teamcode finalrunde
  
  // ermittle fertige gruppen
  $sql0=<<<EOD
    create temporary table if not exists cs_tt1 
      select distinct groupid
      from $cs_team a inner join $cs_match b 
      on a.tid = b.tid1 
      where round = 'V' and winner=-1; 
EOD;

 $sql1=<<<EOD
    select distinct a.groupid from $cs_team a
    left outer join cs_tt1 t2
    on a.groupid = t2.groupid
    where t2.groupid is NULL
    and a.groupid <> ''
    ;
EOD;

 $sql2="drop temporary table cs_tt1;";
 
 $wpdb->query($sql0);
 $res0 = $wpdb->get_results($sql1);
 $wpdb->query($sql2);


 // aktualisiere daten fuer fertige gruppen
 foreach ($res0 as $res) {
   
   // hole platzierung der fertigen gruppe
   $res_group = get_team_clification($res->groupid,get_option("cs_group_teams"));

   $no = 1;
   // tausche die platzhalter gegen die richtigen platzierten aus
   foreach ($res_group as $qt) {
     // teamcode im pseudo tabelleneintrag
     $tcode = "#" . $qt->groupid . $no;

     // tid des pseudoeintrages
     $sql = "select tid from  $cs_team where name='".$tcode."';";
     $dtid = $wpdb -> get_row($sql);

     // alte und neue teamid
     $oldtid = $dtid->tid;
     $newtid = $qt->tid;

     // austausch der ids in den match daten
     $sql="update $cs_match set tid1=$newtid where round='F' and tid1=$oldtid;";
     $wpdb->query($sql);

     
     $sql="update $cs_match set tid2=$newtid where round='F' and tid2=$oldtid;";
     $wpdb->query($sql);

     // platzierung erhoehen
     $no += 1;
   }
 }

 // aktualisiere daten fuer k.o.-runde
 // selektiere noch zu ersetzende pseudo teams 
 $sql0=<<<EOD
   create temporary table if not exists cs_tt2
   SELECT a.mid,'1' as tnr,a.tid1 as tid ,
   substring( b.name, 2,1 ) as wl,substring(b.name,3) as wlmid,a.matchtime
   FROM $cs_match a
   INNER JOIN $cs_team b ON a.tid1 = b.tid
   WHERE a.round = 'F' AND (b.name LIKE '#W%' or b.name like '#L%')
   UNION
   SELECT a.mid,'2' as tnr,a.tid2 as tid,
   substring( b.name, 2,1 ) as wl, substring(b.name,3) as wlmid,a.matchtime
   FROM $cs_match a
   INNER JOIN $cs_team b ON a.tid2 = b.tid
   WHERE a.round = 'F' AND (b.name LIKE '#W%' or b.name like '#L%')
   ;
EOD;

 $sql1="select * from cs_tt2 order by matchtime;";
 $sql2="drop temporary table cs_tt2;";
 
 $wpdb->query($sql0);
 $res0 = $wpdb->get_results($sql1);
 $wpdb->query($sql2);

 foreach ($res0 as $res) {
   // zugehoeriges spiel suchen
   $sql0="select * from $cs_match where winner <> -1 and mid=".$res->wlmid.";";
   $row=$wpdb->get_row($sql0);

   // nur updaten wenn ergebnis vorliegt
   if ( !empty($row) ) {
     // ermittle einzutragendes team
     if (( $res->wl=='W' and $row->winner==1) or ( $res->wl=='L' and $row->winner==2 ))
       $newtid = $row->tid1;
     if (( $res->wl=='W' and $row->winner==2) or ( $res->wl=='L' and $row->winner==1 ))
       $newtid = $row->tid2;

     $sql1="update $cs_match set tid".$res->tnr."=".$newtid." where mid=".$res->mid;
     $wpdb->query($sql1);
     
   }
 }
}


// verschickt an die abonnenten das aktuelle ranking
function mailservice()
{
  include("globals.php");
  global $wpdb;
  
  // email adressen holen
  $sql="select user_nicename, user_email from $wp_users inner join $cs_users on ID=userid where mailservice=1;";
  $res_email=$wpdb->get_results($sql);
  

  // ausgabe des aktuellen punktestandes und des ranges
  $rank = get_ranking();
  $i=0;
  $msg  = "<h2>".__("EM-Tippspiel Mailservice","wpcs")."</h2>\n";
  $msg .= "<h2>".__("Aktueller Punktestand","wpcs")."</h2>\n";
  $msg .= "<table border='1' width='500px' cellpadding='0'><thead><tr>\n";
  $msg .= '<th scope="col" style="text-align: center">'.__("Platz","wpcs").'</th>'."\n";
  $msg .= '<th scope="col" style="text-align: center">'.__("Spieler","wpcs").'</th>'."\n";
  $msg .= '<th width="20">'.__("Punktestand","wpcs").'</th>'."\n";

  $pointsbefore= -1; 
  $i=0;
  $j=1;
  foreach ($rank as $row) {
    // platzierung erhoehen, wenn punkte sich veraendern
    if ($row->points != $pointsbefore) {
      $i += $j;
      $j = 1;
    } else
      $j += 1;
    
    $msg .= "<tr><td align='center'>$i</td><td align='center'>".$row->user_nicename."</td><td align='center'>".$row->points. "</td></tr>";
    
    // gruppenwechsel versorgen
    $pointsbefore = $row->points;
  }
  $msg .= '</table>'."\n<p>&nbsp;";
  
  foreach($res_email as $row) {
    // mail senden
    // header bauen
    $header = "From: webmaster@tuxlog.de";   
    $header .= "MIME-Version: 1.0\n"; // ohne \r das ist wichtig
    $header .= "Content-Type: text/html; charset=utf-8\r\n";

    $stat = mail  ( $row->user_email , "Update EM2008 Tippspiel" , $msg, $header);
    if ( $stat) 
      echo __("Die email an ","wpcs").$row->user_email.__(" wurde versendet.<br />","wpcs");
    else 
      echo __("Die email an ","wpcs").$row->user_email.__("konnte <b>nicht</b> versendet werden","wpcs");
  }
}

function get_cswinner() 
{
  include("globals.php");
  global $wpdb;
  $wteamname = "";

  // tunier beendet?
  $sql0="select count(*) as anz from $cs_match where winner=-1;";
  $row=$wpdb->get_row($sql0);
  
  if ( $row->anz == 0 ) {
    // selektiere winner team als gewinner des letzten spiels = finale
    $sql1="select case winner when 1 then tid1 when 2 then tid2 else 0 end as wteam from $cs_match order by matchtime desc limit 0,1;";
    $row=$wpdb->get_row($sql1);
    $wteam = $row->wteam;

    $sql2="select name from $cs_team where tid=$wteam";
    $row=$wpdb->get_row($sql2);
    $wteamname = $row->name;
  }

  return $wteamname;
}
?>
