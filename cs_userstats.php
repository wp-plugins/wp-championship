<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2008  Hans Matzen  (email : webmaster at tuxlog.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You 
are not allowed to call this page directly.'); }


// apply the filter to the page or post content
function searchcsuserstats($content) {

  // look for wp-greet tag
  if ( stristr( $content, '[cs-userstats]' )) {

    // replace tag with html form
    $search = '[cs-userstats]';
    $replace= show_UserStats(); 
    $content= str_replace ($search, $replace, $content);
  }

  return $content;

  }

// -----------------------------------------------------------------------------------
// Funktion zur ausgabe der User Statistikseite
// -----------------------------------------------------------------------------------
function show_UserStats()
{
  include("globals.php");
  global $wpdb,$userdata;
  
  // initialisiere ausgabe variable
  $out = "";
  
  // pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
  // und beende die funktion
  if ( !is_user_logged_in()){
    $out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
    $out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
    return $out;
  }
  
 // for debugging
 //$wpdb->show_errors(true);

 // lese anwenderdaten ein
 get_currentuserinfo();
 // merke die userid 
 $uid = $userdata->ID;

 // userdaten lesen
 $sql0="select * from $cs_users where userid=$uid";
 $r0= $wpdb->get_results($sql0);
 
 // admin flag setzen
 $is_admin=false;
 if ( $r0[0]->admin == 1 ) 
   $is_admin=true;
  
 // ermittle aktuelle uhrzeit
 $currtime=date("Y-m-d H:i:s");

 // begruessung ausgeben
 $out .= __("Willkommen ","wpcs").$userdata->user_nicename .",<br />";
 $out .= __("auf dieser Seite siehst du den aktuellen Stand des Turniers und des Tippspiels.","wpcs")."<br /></p>";
 

 // ausgabe der optionen und der tipptabelle
 // -------------------------------------------------------------------


 // anzeigen wenn der user admin des tippspiels ist
 if ( $is_admin ) 
   $out .= '<b>'.__("Du bist Tippspiel-Administrator.","wpcs").'</b><br />';


 // anzeigen der gewinnermannschaft falls tunier schon beendet
 $cswinner = get_cswinner();
 if ( $cswinner ) 
   $out.= "<hr>".__("Der Gewinner des Turniers heißt:","wpcs")."<b>$cswinner</b><hr>";
 
 // ausgabe des aktuellen punktestandes und des ranges
 $rank = get_ranking();
 $out .= "<h2>".__("Aktueller Punktestand","wpcs")."</h2>\n";
 $out .= "<table border='1' width='500' cellpadding='0'><tr>\n";
 $out .= '<th scope="col" style="text-align: center">'.__("Platz","wpcs").'</th>'."\n";
 $out .= '<th scope="col" style="text-align: center">'.__("Spieler","wpcs").'</th>'."\n";
 $out .= '<th width="20">'.__("Punktestand","wpcs").'</th>'."</tr>\n";
 
 $pointsbefore= -1;   
 $i=0; $j=1;
 foreach ($rank as $row) {
   // platzierung erhoehen, wenn punkte sich veraendern
   if ($row->points != $pointsbefore) {
     $i = $i + $j;
     $j=1;
   } else
     $j += 1;

   $out .= "<tr><td align='center'>$i</td><td align='center'>".$row->user_nicename."</td><td align='center'>".$row->points. "</td></tr>";
   // gruppenwechsel versorgen
   $pointsbefore = $row->points;
 }
 $out .= '</table>'."<p>&nbsp;</p>\n";


 // Spielübersicht Vorrunde
 $iconpath = get_option("siteurl") . "/wp-content/plugins/wp-championship/icons/";

 // tabellen loop
 // hole tabellen daten
 $results = get_team_clification();

 $groupid_old = "";
 
 $out .= "<h2>".__("Vorrunde","wpcs")."</h2>\n"; 
 
 foreach($results as $res) {

   // bei gruppenwechsel footer / header ausgeben
   if ($res->groupid != $groupid_old) {
     if ($groupid_old !="")
       $out .= '</table><p>&nbsp;</p>';
     
     $out .= "<h2>".__("Gruppe","wpcs")." ".$res->groupid."</h2>\n"; 
     $out .= "<table border='1' width='500' cellpadding='0'><thead><tr>\n";
     $out .= '<th style="text-align: center">'.__('Mannschaft',"wpcs")."</th>"."\n";
     $out .= '<th style="text-align: center">'.__('Spiele',"wpcs").'</th>'."\n"; 
     $out .= '<th style="text-align: center">'.__('Siege',"wpcs").'</th>'."\n"; 
     $out .= '<th style="text-align: center">'.__('Unentschieden',"wpcs").'</th>'."\n"; 
     $out .= '<th style="text-align: center">'.__('Niederlagen',"wpcs").'</th>'."\n";
     $out .= '<th style="text-align: center">'.__('Tore',"wpcs").'</th>'."\n";
     $out .= '<th align="center">'.__("Punkte","wpcs").'</th></tr>';
     $out .= '</thead>'."\n";
   }

   // hole statistiken des teams
   $stats=array();
   $stats=get_team_stats($res->tid);
   
   // zeile ausgeben
   $out .= "<tr><td><img alt='icon1' width='20' src='".$iconpath.$res->icon."' />";
   $out .= $res->name . "</td>";
   $out .= "<td align=\"center\">".$stats['spiele']."</td>";
   $out .= "<td align=\"center\">".$stats['siege']."</td>"; 
   $out .= "<td align=\"center\">".$stats['unentschieden']."</td>"; 
   $out .= "<td align=\"center\">".$stats['niederlagen']."</td>";
   $out .= "<td align=\"center\"> ".$res->store." : " .$res->sgegentore." </td>";
   $out .= "<td align='center'>" . $res->spoints." </td>";
   $out .= "</tr>\n";
   
   // gruppenwechsel versorgen
   $groupid_old = $res->groupid;
 }
 $out .= "</table><p>&nbsp;</p>\n";
 

 // Finalrunde ausgeben
 $sql1=<<<EOD
   select a.mid as mid, b.icon as icon1, b.name as name1,
   c.icon as icon2, c.name as name2, a.result1 as result1,
   a.result2 as result2, a.location as location, 
   date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,
   a.matchtime as matchts
   from  $cs_match a
   inner join  $cs_team b
   on a.tid1=b.tid
   inner join  $cs_team c
   on a.tid2=c.tid
   where a.round='F'
   order by a.matchtime;
EOD;

 $results = $wpdb->get_results($sql1);

 // tabellen kopf ausgeben
 $out .= "<h2>".__("Finalrunde","wpcs")."</h2>\n"; 
 $out .= "<table border='1' width='600' cellpadding='0'><thead><tr>\n";
 $out .= '<th width="20">'.__("Spielnr.","wpcs").'</th>'."\n";
 $out .= '<th>&nbsp;</th>';
 $out .= '<th scope="col" style="text-align: center">'.__('Begegnung',"wpcs")."</th>"."\n";
 $out .= '<th>&nbsp;</th>';
 $out .= '<th scope="col" style="text-align: center">'.__('Ort',"wpcs").'</th>'."\n";
 $out .= '<th scope="col" style="text-align: center">'.__("Datum<br />Zeit").'</th>'."\n";
 $out .= '<th align="center">'.__("Ergebnis","wpcs").'</th>';
 $out .= '</tr></thead>'."\n";

 foreach($results as $res) {
   // zeile ausgeben
   $out .= "<tr>";
   $out .= "<td align='center'>".$res->mid."</td>";
   if ($res->icon1 != "")
     $out .= "<td><img alt='icon1' width='15' src='".$iconpath.$res->icon1."' /></td>";
   else
     $out .= "<td>&nbsp;</td>";
   $out .= "<td align='center'>".team2text($res->name1) . " - " . team2text($res->name2)."</td>";
   if ($res->icon2 != "")
     $out .= "<td><img alt='icon2' width='15' src='".$iconpath.$res->icon2."' /></td>";
   else
     $out .= "<td>&nbsp;</td>";
   $out .= "<td align=\"center\">".$res->location."</td>";
   $out .= "<td align=\"center\">".$res->matchtime."</td>";
   $out .= "<td align='center'>";
   $out .= ( $res->result1==-1 ? "-" : $res->result1) ." : ". ($res->result2==-1?"-":$res->result2) . "</td>";
   $out .= "</tr>\n";

 }
 $out .= "</table>\n<p>&nbsp;";


 return $out;
}

?>
