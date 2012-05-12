<?php

//
// function to show an admin message on an admin page
//
if (!function_exists("admin_message"))
{
	function admin_message($msg) {
		echo "<div class='updated'><p><strong>";
		echo $msg;
		echo "</strong></p></div>\n";
	}
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
// calculates the points for each user and match and stores it in cs_tipp
//
function calc_points($new=false)
{
	include ("globals.php");
	global $wpdb;

	//$wpdb->show_errors(true);

	// for testing
	$new=true;

	// alles zuruecksetzen
	if ($new) {
		$sql = "update $cs_tipp set points=-1 where points <>-1";
		$res = $wpdb->query($sql);
	}

	// punktevorgaben lesen
	$cs_pts_tipp= get_option("cs_pts_tipp");           // korrekter tipp
	$cs_pts_tendency=get_option("cs_pts_tendency");    // tendenz
	$cs_pts_supertipp=get_option("cs_pts_supertipp");  // tendenz und tordifferenz
	$cs_pts_champ=get_option("cs_pts_champ");          // championtipp
	$cs_pts_oneside=get_option("cs_pts_oneside");      // einseitg richtiger tipp
	$cs_oneside_tendency=get_option("cs_oneside_tendency");  // einseitg richtiger tipp nur mit richtiger tendenz gültig
	$cs_goalsum=get_option("cs_goalsum");              // schwellwert für torsumme tipp
	$cs_pts_goalsum=get_option("cs_pts_goalsum");      // punkte für tosummentipp
	$cs_goalsum_auto = get_option("cs_goalsum_auto");  // torsummentipp aus tipp berechnen oder separat?

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

	// einseitig richtiger tipp ohne und mit tendenz
	// mysql 4.x
	if ($cs_pts_oneside > 0) {
		if ($cs_oneside_tendency > 0)
	 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( (a.result1<a.result2 and b.result1<b.result2) or (a.result1=a.result2 and b.result1=b.result2) or (a.result1>a.result2 and b.result1>b.result2)  ) and ( a.result1=b.result1 or a.result2=b.result2 ) set points= points + $cs_pts_oneside where b.points = $cs_pts_tendency and b.result1>-1 and b.result2>-1;";
	 else
	 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( a.result1=b.result1 or a.result2=b.result2 ) set points= $cs_pts_oneside where b.points = -1 and b.result1>-1 and b.result2>-1;";
	 $res = $wpdb->query($sql);
	}


	// falscher tipp (setzt alle restlichen auf 0)
	// mysql 5.0
	//$sql= "update  $cs_tipp b set points=0 where mid in ( select a.mid from  a where a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 ) and b.points = -1;";
	// mysql 4.x
	$sql= "update $cs_tipp b inner join $cs_match a on  a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1  set points=0 where b.points = -1 and b.result1>-1 and b.result2>-1;";
	$res = $wpdb->query($sql);


	// torsummen tipp prüfen und ggf addieren
	if ($cs_goalsum > 0) {
		if ($cs_goalsum_auto==0)
	 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( a.result1+a.result2 <= b.result3 and a.result1+a.result2 > $cs_goalsum ) set points=points+$cs_pts_goalsum where b.result1>-1 and b.result2>-1;";
	 else
	 $sql= "update $cs_tipp b inner join $cs_match a on a.mid=b.mid and a.result1 <> -1 and a.result2 <> -1 and ( a.result1+a.result2 <= b.result1+b.result2 and a.result1+a.result2 > $cs_goalsum ) set points=points+$cs_pts_goalsum where b.result1>-1 and b.result2>-1;";
	 $res = $wpdb->query($sql);
	}



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
function get_ranking($tippgroup="") {
	include("globals.php");
	global $wpdb;

	//select fuer ranking der tipper
	//$sql = "select b.user_nicename, a.userid,sum(a.points) as points from $cs_tipp a inner join $wp_users b on a.userid=b.ID where points <> -1 group by b.user_nicename, a.userid order by points DESC;";
	//$sql = "select b.user_nicename, a.userid,sum(a.points) as points, c.rang as oldrank from $cs_tipp a inner join $wp_users b on a.userid=b.ID inner join $cs_users c on a.userid=c.userid where points <> -1 group by b.user_nicename, a.userid order by points DESC;";
	if ($tippgroup <> "") {
		$sql = "select b.user_nicename, a.userid,sum(a.points) as points, c.rang as oldrank from $cs_tipp a inner join $wp_users b on a.userid=b.ID inner join $cs_users c on a.userid=c.userid inner join $cs_match d on a.mid = d.mid where points <> -1 and c.tippgroup='$tippgroup' group by b.user_nicename, a.userid order by points DESC;";
	} else {
		$sql = "select b.user_nicename, a.userid,sum(a.points) as points, c.rang as oldrank from $cs_tipp a inner join $wp_users b on a.userid=b.ID inner join $cs_users c on a.userid=c.userid inner join $cs_match d on a.mid = d.mid where points <> -1 group by b.user_nicename, a.userid order by points DESC;";
	}
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

	// turniermodus lesen
	$cs_modus      = get_option("cs_modus");
	// punktvergabe fuer match einlesen
	$cs_pts_winner = get_option("cs_pts_winner");
	$cs_pts_looser = get_option("cs_pts_looser");
	$cs_pts_deuce  = get_option("cs_pts_deuce");

	$sql1= <<<EOD
	create temporary table if not exists cs_tt
         select groupid,name,tid,icon,qualified,
	 sum(result1) as tore,
	 sum(result2) as gegentore, 
	 sum( case winner when 0 then $cs_pts_deuce when 1 then $cs_pts_winner else $cs_pts_looser end)  as points
	 from $cs_match 
	 inner join $cs_team 
	 on tid=tid1
	 where winner<>-1 and tid1<>0 and round='V'
         group by groupid,name,icon,qualified
	 UNION ALL
         select groupid,name,tid,icon,qualified,
	 sum(result2) as tore,
	 sum(result1) as gegentore, 
	 sum( case winner when 0 then $cs_pts_deuce when 2 then $cs_pts_winner else $cs_pts_looser end)  as points
	 from $cs_match 
	 inner join $cs_team 
	 on tid=tid2
	 where winner <>-1 and tid2<>0 and round='V'
         group by groupid,name,icon,qualified
	 UNION ALL
         select distinct groupid,name,tid,icon,qualified, 
         0 as tore,0 as gegentore,0 as points
	 from $cs_match inner join $cs_team on tid=tid1
	 where winner =-1 and tid1<>0 and round ='V'
	 UNION ALL
         select distinct groupid,name,tid,icon, qualified,
         0 as tore,0 as gegentore,0 as points
	 from $cs_match inner join $cs_team on tid=tid2
	 where winner =-1 and tid2<>0 and round='V';
EOD;

	$sql2= "select groupid, name,tid,icon,qualified, sum(tore) as store,sum(gegentore) as sgegentore, sum(points) as spoints, (sum(tore)-sum(gegentore)) as tdiff from cs_tt ";

	if ($groupid !="")
	$sql2.=" where groupid = '$groupid' ";

	$sql2 .= "group by groupid,name,icon order by groupid,qualified,spoints DESC,tdiff DESC, store DESC";

	if ($count !=0)
	$sql2 .= " limit 0,$count";

	$sql2 .= ";";

	$sql3= "drop temporary table cs_tt;";

	$wpdb->query($sql1);
	$results = $wpdb->get_results($sql2);
	$wpdb->query($sql3);

	// erweiterung fuer bundesligamodus
	if ($cs_modus == 2) {

		$points1=0; $diff1=0; $tore1=0; $tid1=0;
		$points2=0; $diff2=0; $tore2=0; $tid2=0;

		foreach($results as $key => $r) {
			// hole werte des aktuellen teams
			$points1=$r->spoints;
			$diff1=$r->tdiff;
			$tore1=$r->store;
			$tid1=$r->tid;

			// vergleiche mit vorherigem team
			if ($points1==$points2 and $diff1==$diff2 and $tore1==$tore2)
			// ermittle besseres team im direkten vergleich
			$erstes_team = compare_direct($tid1,$tid2);

			if ($erstes_team == 2) {
				// plaetze tauschen
				$temp = $results[$key];
				$results[$key] = $results[$key - 1];
				$results[$key - 1] = $temp;
			}
			// vergleichswerte aktualisieren
			$points1=$points2; $diff1=$diff2; $tore1=$tore2; $tid1=$tid2;
		}
	}

	return $results;
}

// vergleicht zwei teams im direkten vergleich gemaess bundesliga reglement
// zuerst das gesamtergebnis direkter vergleich, dann die auswaertstore im direkten
// vergleich und dann die auswaertstore im gesamten turnier
// liefert 1 zurueck wenn team1 besser war und 2 wenn team2 besser war
// und 0 wenn der bessere nicht zu ermitteln war
function compare_direct($team1,$team2)
{
	include("globals.php");
	global $wpdb;

	$tore1=0;$tore2=0; $atore1=0; $atore2=0; $winner=-1;

	//$wpdb->show_errors(true);

	$sql="select * from $cs_match where (tid1=$team1 and tid2=$team2) or (tid1=$team2 and tid2=$team1) and winner <> -1";
	$res = $wpdb->get_results($sql);

	// summiere die erzielten tore beider mannschaften auf
	foreach($res as $r) {
		if ($r->tid1 == $team1) {
			$tore1 = $tore1 + $r->result1;
			$tore2 = $tore2 + $r->result2;
			$atore2 = $atore2 + $r->result2;
		} else {
			$tore2 = $tore2 + $r->result1;
			$tore1 = $tore1 + $r->result2;
			$atore1 = $atore1 + $r->result2;
		}
	}

	// ermittle den sieger im direkten vergleich
	// erst gesamtergebnis, dann auswaertstore im direkten vergleich
	if ($tore1 > $tore2 )
	$winner = 1;
	else if ($tore2 > $tore1)
	$winner = 2;
	else if ($atore1 > $atore2)
	$winner = 1;
	else if ($atore2 > $atore1 )
	$winner = 2;
	else {
		// jetzt geht es um die auswaertstore im ganzen turnier
		$sql="select sum(result2) as atore from $cs_match where tid2=$team1 and winner<>-1";
		$res = $wpdb->get_row($sql);
		$atoresum1 = $res->atore;

		$sql="select sum(result2) as atore from $cs_match where tid2=$team2 and winner<>-1";
		$res = $wpdb->get_row($sql);
		$atoresum2 = $res->atore;

		if ( $atoresum1 > $atoresum2 )
		$winner = 1;
		else if ( $atoresum2 > $atoresum1 )
		$winner = 2;
		else
		$winner = 0;
	}

	return $winner;
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
   WHERE a.round = 'F' AND (b.name LIKE '#W%' or b.name like '#V%')
   UNION
   SELECT a.mid,'2' as tnr,a.tid2 as tid,
   substring( b.name, 2,1 ) as wl, substring(b.name,3) as wlmid,a.matchtime
   FROM $cs_match a
   INNER JOIN $cs_team b ON a.tid2 = b.tid
   WHERE a.round = 'F' AND (b.name LIKE '#W%' or b.name like '#V%')
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
			if (( $res->wl=='W' and $row->winner==1) or ( $res->wl=='V' and $row->winner==2 ))
			$newtid = $row->tid1;
			if (( $res->wl=='W' and $row->winner==2) or ( $res->wl=='V' and $row->winner==1 ))
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
	$msg  = "<h2>".__("Tippspiel Mailservice","wpcs")."</h2>\n";
	$msg .= "<h2>".__("Aktueller Punktestand","wpcs")."</h2>\n";
	$msg .= "<table border='1' width='500px' cellpadding='0'><thead><tr>\n";
	$msg .= '<th scope="col" style="text-align: center">'.__("Platz","wpcs").'</th>'."\n";
	$msg .= '<th scope="col" style="text-align: center">'.__("Spieler","wpcs").'</th>'."\n";
	$msg .= '<th width="20">'.__("Punktestand","wpcs").'</th>'."\n";
	if (get_option('cs_rank_trend'))
	$msg .= '<th width="20">'.__("Trend","wpcs").'</th>';
	$msg .= '</tr></thead>';

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

		if ($i < $row->oldrank )
		$trend = "&uArr;";
		elseif ($i > $row->oldrank )
		$trend = "&dArr;";
		else
		$trend = "&rArr;";

		$msg .= "<tr><td align='center'>$i</td><td align='center'>".$row->user_nicename."</td><td align='center'>".$row->points. "</td>";
		if (get_option('cs_rank_trend'))
		$msg .= "<td align='center'>$trend</td>";
		$out .= "</tr>";


		// gruppenwechsel versorgen
		$pointsbefore = $row->points;
	}
	$msg .= '</table>'."\n<p>&nbsp;";

	foreach($res_email as $row) {
		// mail senden
		// header bauen
		$header = "From: " . get_option("admin_email") ."\n";
		$header .= "MIME-Version: 1.0\n"; // ohne \r das ist wichtig
		$header .= "Content-Type: text/html; charset=utf-8\r\n";

		$stat = wp_mail( $row->user_email , "Update Tippspiel" , $msg, $header);
		if ( $stat)
		echo __("Die email an ","wpcs").$row->user_email.__(" wurde versendet.","wpcs");
		else
		echo __("Die email an ","wpcs").$row->user_email.__(" konnte <b>nicht</b> versendet werden","wpcs");
		echo "<br />\n";
	}
}

// verschickt an die abonnenten erinnerungen falls noch nicht getippt wurde
function mailservice2()
{
	// prüfen ob wir erinnern sollen
	$cs_reminder= get_option("cs_reminder");
	if ( ! $cs_reminder)
	return;

	// globale variable einlesen
	include("globals.php");
	global $wpdb;


	// holen der match ids fuer die spiele die noch nicht angefangen haben aber in
	// den nächsten stunden anfangen
	$cs_reminder_hours = get_option("cs_reminder_hours");
	$now  =  time() + ( get_option( 'gmt_offset' ) * 3600 );
	$then =  $now + ( $cs_reminder_hours * 3600 );

	$mnow  = gmdate( 'Y-m-d H:i:s', $now );
	$mthen = gmdate( 'Y-m-d H:i:s', $then );

	$sql = "select mid from cs_match where matchtime > '$mnow' and matchtime <= '$then';";
	$res_mid = $wpdb->get_results($sql);



	$mids="(";
	foreach ($res_mid as $row)
	$mids .= $row->mid . ", ";

	$mids .= "-9999)";

	// holen der userids, die fuer diese match ids noch nicht getippt haben
	$sql ="select a.userid, b.mid from cs_users a, cs_match b where b.mid in $mids and not exists ( select userid, mid from cs_tipp where userid=a.userid and mid=b.mid ) order by a.userid, b.mid;";
	$res_user = $wpdb->get_results($sql);

	// fuer jeden user mit fehlendem tipp email zusammenstellen und senden
	foreach ( $res_user as $u )
	{
		// email adresse holen
		$sql="select user_nicename, user_email from $wp_users where ID=$u->userid;";
		$res_email=$wpdb->get_results($sql);

		// match daten adresse holen
		$sql="select b.name name1,c.name name2,a.matchtime,a.location from cs_match a inner join cs_team b on a.tid1 = b.tid inner join cs_team c on a.tid2=c.tid where mid=$u->mid;";
		$res_match=$wpdb->get_results($sql);


		// mailnachricht zusammen bauen
		$msg  = "<h2>".__("Tippspiel Mailservice","wpcs")."</h2>\n";
		$msg .= "<h2>".__("Möchtest du noch tippen? Das folgende Spiel beginnt bald: ","wpcs")."</h2>\n";
		$msg .= $res_match[0]->name1 . " : " . $res_match[0]->name2 . " in " . $res_match[0]->location . " startet " . $res_match[0]->matchtime . "<br /><br/>\n";
		$msg .= "Viel Glück wünscht der Tippspiel-Admin.\n";
		 

		// header bauen
		$header = "From: " . get_option("admin_email") . "\n";
		$header .= "MIME-Version: 1.0\n"; // ohne \r das ist wichtig
		$header .= "Content-Type: text/html; charset=utf-8\r\n";

		// mail senden
		$stat = wp_mail  ( $res_email[0]->user_email , "Update Tippspiel" , $msg, $header);
		if ( $stat)
		echo __("Die Erinnerungsemail an ","wpcs").$res_email[0]->user_email.__(" wurde versendet.","wpcs");
		else
		echo __("Die Erinnerungsemail an ","wpcs").$res_email[0]->user_email.__(" konnte <b>nicht</b> versendet werden","wpcs");
		echo "<br />";
	}
}

// verschickt an die abonnenten die Bestätigungsmail
function mailservice3($userid, $tipps)
{
	include("globals.php");
	global $wpdb;

	// email adressen holen
	$sql="select user_nicename, user_email from $wp_users inner join $cs_users on ID=userid where mailreceipt=1 and userid=$userid;";
	$res_email=$wpdb->get_row($sql);


	// ausgabe des aktuellen punktestandes und des ranges
	$rank = get_ranking();
	$i=0;
	$msg  = "<h2>".__("Tippspiel Mailservice","wpcs")."</h2>\n";
	$msg .= "<h2>".__("Tippbestätigung","wpcs")."</h2>\n";
	$msg .= "<table border='1' width='500px' cellpadding='0'><thead><tr>\n";
	$msg .= '<th scope="col" style="text-align: center">'.__("Spiel","wpcs").'</th>'."\n";
	$msg .= '<th scope="col" style="text-align: center">'.__("Tipp","wpcs").'</th>'."\n";
	$msg .= '<th scope="col" style="text-align: center">'.__("Datum/Zeit","wpcs").'</th>'."\n";
	$msg .= '</tr></thead>';

	foreach ($tipps as $key=>$val) {
		$sql="select b.name as name1, c.name as name2, a.matchtime as matchtime from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join cs_team c on a.tid2=c.tid where mid=$key;";
		$resm=$wpdb->get_row($sql);
		$t1 = $resm->name1;
		$t2 = $resm->name2;
		$t3 = $resm->matchtime;
		
		if (substr($t1,0,1)=="#")
			$t1=__("n/a","wpcs");
		if (substr($t2,0,1)=="#")
			$t2=__("n/a","wpcs");
		
		$msg .= "<tr><td align='center'>$t1 : $t2</td><td align='center'>".($val=="-1:-1"?" -:- ":$val)."</td><td align='center'>".$t3. "</td>";
		$msg .= "</tr>";


	}
	$msg .= '</table>'."\n<p>&nbsp;";

	// mail senden
	// header bauen
	$header = "From: " . get_option("admin_email") ."\n";
	$header .= "MIME-Version: 1.0\n"; // ohne \r das ist wichtig
	$header .= "Content-Type: text/html; charset=utf-8\r\n";

	$stat = wp_mail( $res_email->user_email , "Update Tippspiel" , $msg, $header);
	if ( $stat)
	echo __("Die Bestätigungsemail an ","wpcs").$res_email->user_email.__(" wurde versendet.","wpcs");
	else
	echo __("Die Bestätigungsemail an ","wpcs").$res_email->user_email.__(" konnte <b>nicht</b> versendet werden","wpcs");
	echo "<br />\n";
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
		if (get_option("cs_modus")==1) {
			 
	  // selektiere winner team als gewinner des letzten spiels = finale
	  $sql1="select case winner when 1 then tid1 when 2 then tid2 else 0 end as wteam from $cs_match where round='F' order by matchtime desc limit 0,1;";
	  $row=$wpdb->get_row($sql1);
	  $wteam = $row->wteam;
	   
	  $sql2="select name from $cs_team where tid=$wteam";
	  $row=$wpdb->get_row($sql2);
	  $wteamname = $row->name;
		} else {
	  $r =  get_team_clification('', 1);
	  $wteamname = $r[0]->name;
		}
	}

	return $wteamname;
}

// liefert die spielstatistiken fuer ein team zurueck
// ermittelt werden:
// anzahl der spiele, anzahl siege, anzahl unentschieden, anzahl niederlagen
//
function get_team_stats($teamid)
{
	include("globals.php");
	global $wpdb;

	//$wpdb->show_errors(true);

	// anzahl spiele
	$sql1= "select count(*) as anz1 from $cs_match where round='V' and (tid1=$teamid or tid2=$teamid) and winner <> -1; ";

	// anzahl siege
	$sql2= "select count(*) as anz2 from $cs_match where round='V' and ( tid1=$teamid and winner=1) or (tid2=$teamid and winner = 2); ";

	// anzahl unentschieden
	$sql3= "select count(*) as anz3 from $cs_match where round='V' and ( tid1=$teamid or tid2=$teamid ) and winner = 0; ";

	// anzahl niederlagen
	$sql4= "select count(*) as anz4 from $cs_match where round='V' and ( tid1=$teamid and winner=2) or (tid2=$teamid and winner = 1); ";

	$res1 = $wpdb->get_row($sql1);
	$res2 = $wpdb->get_row($sql2);
	$res3 = $wpdb->get_row($sql3);
	$res4 = $wpdb->get_row($sql4);

	return array('spiele' => $res1->anz1, 'siege' => $res2->anz2,
	       'unentschieden' => $res3->anz3, 'niederlagen' => $res4->anz4 );
}


function get_float_js() {
	$js = <<<EOL
<script type="text/javascript">
var name = "#WPCSfloatMenu";
var menuYloc = null;
	
jQuery(document).ready(function(){
	menuYloc = parseInt(jQuery(name).css("top").substring(0,jQuery(name).css("top").indexOf("px")))
	    jQuery(window).scroll(function () { 
		    offset = menuYloc+jQuery(document).scrollTop()+"px";
		    jQuery(name).animate({top:offset},{duration:500,queue:false});
		});
    }); 
</script>
EOL;

	return $js;
}

//
// speichert die aktuelle platzierung alle mitspieler in der tabelle cs_users
// in der spalte rang
//
function store_current_ranking() {
	include("globals.php");
	global $wpdb;

	$pointsbefore=-1;
	$i=0;
	$j=1;
	// hole aktuelle mitspielerplatzierung
	$rank = get_ranking();

	foreach ($rank as $row) {
		// platzierung erhoehen, wenn punkte sich veraendern
		if ($row->points != $pointsbefore) {
			$i += $j;
			$j = 1;
		} else
		$j += 1;

		$sql="update $cs_users set rang=$i where userid=$row->userid;";
		$wpdb->query($sql);

		// gruppenwechsel versorgen
		$pointsbefore = $row->points;
	}
}

//
// fuegt den user mit userid id dem tippspiel hinzu
//
function cs_add_user($id) {
	include("globals.php");
	global $wpdb;
	
	$sql="select count(*) as anz from $cs_users where userid=".$id.";";
	$results = $wpdb->get_row($sql);
	
	if ($results->anz == 0) {
		$sql = "insert into ". $cs_table_prefix ."users values (". $id . ",0,0,0,0,-1,'1900-01-01 00:00:00',-1,'".''."');";
		$results = $wpdb->query($sql);
		//if ( $results == 1 )
		//	admin_message ( __('Mitspieler erfolgreich angelegt.',"wpcs") );
		//else
		//	admin_message( __('Datenbankfehler; Vorgang abgebrochen.',"wpcs") );
	} 
}
?>
