<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2007,2008  Hans Matzen  (email : webmaster at tuxlog.de)

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

// generic functions
require_once("functions.php");

//
// function to show and maintain the set of teams for the championship
//
function cs_admin()
{
  include("globals.php");

  // base url for links
  $thisform = "admin.php?page=wp-championship/cs_admin.php";
  // get options and define group ids
  $groupstr="ABCDEFGHIJKLM";


  // get sql object
  $wpdb =& $GLOBALS['wpdb'];

  // find out what we have to do
  $action = "";
  if ( isset( $_POST['update'] ) )
	$action = "update";
  else if ( isset( $_POST['deltipps'] ) )
	$action = "deltipps";
  else if ( isset( $_POST['delresults'] ) )
	$action = "delresults";
  else if ( isset( $_POST['deltables'] ) )
	$action = "deltables";
 else if ( isset( $_POST['mailservice1'] ) )
	$action = "mailservice1";
 else if ( isset( $_POST['newcalc1'] ) )
	$action = "newcalc1";

  // update options
  //
  $errflag=0;
  if ( $action == "update" ) {
    // check form contents for mandatory fields
    // and/or set default values
    if ( $_POST['cs_group_teams']=="" or $_POST['cs_pts_winner']=="" or $_POST['cs_pts_looser']=="" or $_POST['cs_pts_deuce']=="" or $_POST['cs_pts_champ']=="" or $_POST['cs_pts_tipp']=="" or $_POST['cs_pts_tendency']=="" or $_POST['cs_pts_supertipp']=="" )
      $errflag=1;

    
    // send a message about mandatory data
    if ( $errflag == 1 )
      admin_message ( __( 'Bitte alle Felder füllen.',"wpcs" ) );

    
    // update settings
    if ( $errflag==0 and $action == "update" ) {
      update_option( "cs_groups", $_POST['cs_groups'] );
      update_option( "cs_pts_winner", $_POST['cs_pts_winner'] );
      update_option( "cs_pts_looser", $_POST['cs_pts_looser'] ); 
      update_option( "cs_pts_deuce", $_POST['cs_pts_deuce'] ); 
      update_option( "cs_final_teams", $_POST['cs_final_teams'] ); 
      update_option( "cs_pts_tipp", $_POST['cs_pts_tipp'] ); 
      update_option( "cs_pts_tendency", $_POST['cs_pts_tendency'] ); 
      update_option( "cs_pts_supertipp", $_POST['cs_pts_supertipp'] ); 
      update_option( "cs_pts_champ", $_POST['cs_pts_champ'] ); 
      update_option( "cs_group_teams", $_POST['cs_group_teams'] ); 
      update_option( "cs_stellv_schalter", $_POST['cs_stellv_schalter'] );
      update_option( "cs_modus", $_POST['cs_modus'] );

      admin_message( __('Einstellungen erfolgreich gespeichert.',"wpcs") );
    }
  }

   if ( $action == "deltipps" and $_POST['deltipps_ok']==1) {
     $sql="update $cs_users set champion=-1, championtime='1900-01-01 00:00';";
     $wpdb->query($sql);
     $sql="delete from  $cs_tipp where 1=1;";
     $wpdb->query($sql);

     admin_message(__("Alle Tipps wurden gelöscht","wpcs"));
   }

   if ( $action == "delresults" and $_POST['delresults_ok']==1) {
     // Ergebnisse  entfernen
     $sql="update  $cs_match set result1=-1, result2=-1, winner=-1;";
     $wpdb->query($sql);
     // Pseudo ids wieder aktivieren
     $sql="update  $cs_match set tid1=ptid1, tid2=ptid2 where round='F';";
     $wpdb->query($sql);
     // manuelle platzierungen entfernen
     $sql="update  $cs_team set qualified=0 where qualified <>0;";
     $wpdb->query($sql);
     admin_message(__("Alle Ergebnisse wurden gelöscht","wpcs"));
   }

   if ( $action == "deltables" and $_POST['deltables_ok']==1) {
     // Tabellen  entfernen
     $sql="drop table $cs_users, $cs_match, $cs_team, $cs_tipp;";
     $wpdb->query($sql);
     
     admin_message(__("Alle wp-championship Tabellen wurden gelöscht","wpcs"));
   }
   
   if ( $action == "mailservice1" and $_POST['mailservice_ok']==1) {
     mailservice();
     admin_message(__("Die Mails wurden verschickt.","wpcs"));
   }
   
   if ( $action == "newcalc1" and $_POST['newcalc_ok']==1) {
     // punkt nach eingabe neu berechnen
     calc_points();
     // finalrunde eintraege aktualisieren
     update_finals();
     admin_message(__("Die Neuberechnung wurde durchgeführt.","wpcs"));
   }

  // load options
  $cs_groups = get_option("cs_groups");
  $cs_pts_winner = get_option("cs_pts_winner");
  $cs_pts_looser = get_option("cs_pts_looser");
  $cs_pts_deuce = get_option("cs_pts_deuce");
  $cs_final_teams = get_option("cs_final_teams");
  $cs_pts_tipp = get_option("cs_pts_tipp");
  $cs_pts_tendency = get_option("cs_pts_tendency");
  $cs_pts_supertipp = get_option("cs_pts_supertipp");
  $cs_pts_champ = get_option("cs_pts_champ");
  $cs_group_teams = get_option("cs_group_teams");
  $cs_stellv_schalter= get_option("cs_stellv_schalter");
  $cs_modus= get_option("cs_modus");
   
  // build form
  $out = "";
  
  // general options form
  $out .= '<div class="wrap"><h2>'.__('wp-championship Einstellungen',"wpcs").'</h2><div id="ajax-response"></div>'."\n"; 
  $out .= '<form name="options" id="options" method="post" action=""><input type="hidden" name="action" value="update" />'."\n";
  $out .= '<table class="editform" width="100%" cellspacing="2" cellpadding="2"><tr>';
  $out .= '<th width="30%" scope="row" valign="top"><label for="cs_groups">'.__('Anzahl Gruppen Vorrunde',"wpcs").':</label></th>'."\n";
  
  // number of group box
  $out .= '<td><select name="cs_groups" id="cs_groups" class="postform">'."\n";
  for ($i = 1; $i < 13; $i++) {
    $out .= '<option value="'.$i.'"';
    if ( $i == $cs_groups )
      $out .= ' selected="selected"';
    $out .= '>'.$i.'</option>';
  }
  $out .= '</select></td>'."\n";

   // bestätigungs feld um die neuberechnung auszuloesen
 $out .= '<th scope="row" valign="top"><label for="newcalc_ok">'.__('Platzierung und Punkte neu berechnen?',"wpcs").':</label></th>'."\n";
 $out .= '<td ><input name="newcalc_ok" id="newcalc_ok" type="checkbox" value="1"  />';
 // button zum ausloesen der neuberechnung
 $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="newcalc1" value="'.__('Neuberechnung durchführen','wpcs').' &raquo;" /></td></tr>'."\n";

  // points for winning team
  $out .= '<tr><th  scope="row" valign="top"><label for="cs_pts_winner">'.__('Punkt für den Gewinner einer Begegnung',"wpcs").':</label></th>'."\n"; 
  $out .= '<td><input name="cs_pts_winner" id="cs_pts_winner" type="text" value="'.$cs_pts_winner.'" size="3" /></td>'."\n";
 
 // bestätigungs feld um die mailservice auszuloesen
 $out .= '<th scope="row" valign="top"><label for="mailservice_ok">'.__('Mailservice einmal auslösen?',"wpcs").':</label></th>'."\n";
 $out .= '<td><input name="mailservice_ok" id="mailservice_ok" type="checkbox" value="1"  />';
 // button zum ausloesen des mailservice
 $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="mailservice1" value="'.__('Mailservice auslösen','wpcs').' &raquo;" /></td></tr>'."\n"; 

  // points for loosing team
  $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_looser">'.__('Punkte für den Verlierer einer Begegnung',"wpcs").':</label></th>'."\n";  
  $out .= '<td><input name="cs_pts_looser" id="cs_pts_looser" type="text" value="'.$cs_pts_looser.'" size="3" /></td>'."\n";
 
  // bestätigungs feld um die tipps zu löschen
  $out .= '<th scope="row" valign="top"><label for="deltipps_ok">'.__('Alle Tipps löschen?',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="deltipps_ok" id="deltipps_ok" type="checkbox" value="1"  />';
  // button zum loeschen der tipps
  $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="deltipps" value="'.__('Tipps löschen','wpcs').' &raquo;" /></td></tr>'."\n";
  
  // points for deuce
  $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_deuce">'.__('Punkte bei Unentschieden',"wpcs").':</label></th>'."\n"; 
  $out .= '<td><input name="cs_pts_deuce" id="cs_pts_deuce" type="text" value="'.$cs_pts_deuce.'" size="3" /></td>'."\n";
  
// bestätigungsfeld um die ergebnisse zu löschen
 $out .= '<th scope="row" valign="top"><label for="delresults_ok">'.__('Alle Ergebnisse löschen?',"wpcs").':</label></th>'."\n";
 $out .= '<td><input name="delresults_ok" id="delresults_ok" type="checkbox" value="1"  />';
 // button zum loeschen der ergebnisse
 $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="delresults" value="'.__('Ergebnisse löschen','wpcs').' &raquo;" /></td></tr>'."\n";

  // number of teams from each group joining finalround
  $out .= '<tr><th scope="row" valign="top"><label for="cs_group_teams">'.__('Anzahl der Teams pro Gruppe, die sich für die Finalrunde qualifizieren',"wpcs").':</label></th>'."\n"; 
  $out .= '<td><input name="cs_group_teams" id="cs_group_teams" type="text" value="'.$cs_group_teams.'" size="3" /></td>'."\n";

 // bestätigungsfeld um die tabellen zu löschen
 $out .= '<th scope="row" valign="top"><label for="deltables_ok">'.__('Alle Tabellen aus der Datenbank entfernen?',"wpcs").':</label></th>'."\n";
 $out .= '<td><input name="deltables_ok" id="deltables_ok" type="checkbox" value="1"  />';
 // button zum loeschen der tabellen
 $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="deltables" value="'.__('Tabellen entfernen','wpcs').' &raquo;" /></td></tr>'."\n";
 
  // points for wright tipp
  $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_tipp">'.__('Punkt für korrekten Tipp',"wpcs").':</label></th>'."\n"; 
  $out .= '<td><input name="cs_pts_tipp" id="cs_pts_tipp" type="text" value="'.$cs_pts_tipp.'" size="3" /></td></tr>'."\n";

  // points for tendency
  $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_tendency">'.__('Punkt bei richtiger Tendenz',"wpcs").':</label></th>'."\n"; 
$out .= '<td><input name="cs_pts_tendency" id="cs_pts_tendency" type="text" value="'.$cs_pts_tendency.'" size="3" /></td>'."\n";

// schalter fuer stellvertreterfunktion
 $out .= '<th scope="row" valign="top"><label for="cs_stellv_schalter">'.__('Deaktiveren der Stellvertreterfunktion',"wpcs").':</label></th>'."\n";
 $out .= '<td><input name="cs_stellv_schalter" id="cs_stellv_schalter" type="checkbox" value="1"  ';
 if ( $cs_stellv_schalter > 0)
   $out .= " checked='checked' ";
 $out .= '/></td></tr>';

// points for supertipp 
 $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_supertipp">'.__('Punkt bei richtiger Tendenz und richtiger Tordifferenz',"wpcs").':</label></th>'."\n"; 
 $out .= '<td ><input name="cs_pts_supertipp" id="cs_pts_supertipp" type="text" value="'.$cs_pts_supertipp.'" size="3" /></td>'."\n";
 
 // turniermodus
 $out .= '<th scope="row" valign="top"><label for="cs_modus">'.__('Turniermodus',"wpcs").':</label></th>'."\n";
 $out .= '<td><select name="cs_modus" id="cs_modus" class="postform">'."\n";
 $out .= '<option value="1"';
 if ( $cs_modus == 1 )
   $out .= ' selected="selected"';
 $out .= '>'.__("Standard","wpcs").'</option>';
 $out .= '<option value="2"';
 if ( $cs_modus == 2 )
   $out .= ' selected="selected"';
 $out .= '>'.__("Deutsche Bundesliga","wpcs").'</option>';
 $out .= '</select></td></tr>'."\n";

  // field for champion tipp ponts
  $out .= '<tr><th scope="row" valign="top"><label for="cs_pts_champ">'.__('Punkte für richtigen Sieger-Tipp',"wpcs").':</label></th>'."\n";
 $out .= '<td ><input name="cs_pts_champ" id="cs_pts_champ" type="text" value="'.$cs_pts_champ.'" size="3" /></td></tr>'."\n";

 
 
  $out .= '</table>'."\n";
  
  // add submit button to form
  $out .= '<p class="submit"><input type="submit" name="update" value="'.__('Einstellungen speichern','wpcs').' &raquo;" /></p>';


  $out .= '</form></div>'."\n";
  
  echo $out;
}
?>