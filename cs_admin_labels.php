<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2006-2010  Hans Matzen  (email : webmaster at tuxlog.de)

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
function cs_admin_labels()
{
  include("globals.php");

  // base url for links
  $thisform = "admin.php?page=wp-championship/cs_admin_labels.php";
  
  // get sql object
  global $wbdb;

  $fieldnames = array ("cs_label_group", "cs_col_group", "cs_label_icon1", "cs_col_icon1", "cs_label_match",
		       "cs_col_match", "cs_label_icon2", "cs_col_icon2", "cs_label_location", "cs_col_location",
		       "cs_label_time", "cs_col_time", "cs_label_tip", "cs_col_tip", "cs_label_points",
		       "cs_col_points", "cs_label_place", "cs_col_place", "cs_label_player", "cs_col_player",
		       "cs_label_upoints", "cs_col_upoints", "cs_label_trend",  "cs_label_steam",
		       "cs_col_steam", "cs_label_smatch", "cs_col_smatch", "cs_label_swin", "cs_col_swin", 
		       "cs_label_stie", "cs_col_stie", "cs_label_sloose", "cs_col_sloose", "cs_label_sgoal",
		       "cs_col_sgoal", "cs_label_spoint", "cs_col_spoint", "cs_tipp_sort");

  // find out what we have to do
  $action = "";
  if ( isset( $_POST['update'] ) )
      $action = "update";
  
  // update options
  //
  if ( $action == "update" ) {
      foreach ($fieldnames as $fn) 
	  update_option( $fn, $_POST["$fn"]);
      admin_message( __('Einstellungen erfolgreich gespeichert.',"wpcs") );
    }
  
  // load options
  foreach ($fieldnames as $fn) {
      eval("\$$fn = get_option(\"$fn\");");
  }


  // build form
  $out = "";
  
  //
  // für die tabellen Tipp, Statistiken
  // Tippseite:   Vorrundenspiele Finalrunde
  //    Spalten Gruppe, Icon 1, Begegnung, Icon2, Ort, Datum/Zeit, Tipp/Ergebnis, Punkte
  // Statistik: Vorrunde Finalrunde Gruppe
  //    Mannschaft Spiele Siege Unentschieden Niederlagen Tore Punkte
  // Aktueller Punktestand:
  //    Platz, Spieler, Punktestand, Trend
  // Spalte 1-n anzeigen ja/nein, Beschriftung text ggf. mit ohne Icon  nach welcher Spalte standardmäßig sortiert
  //
  // labels options form
  $out .= '<div class="wrap"><h2>'.__('wp-championship Bezeichnungen',"wpcs").'</h2><div id="ajax-response"></div>'."\n"; 
  $out .= '<form name="options" id="options" method="post" action=""><input type="hidden" name="action" value="update" />'."\n";
  $out .= '<table class="editform" width="100%" cellspacing="2" cellpadding="2">';
  
  $out .= '<tr><th colspan="3" align="left" scope="row">'.__('Vorrundenspiele').'/'.__('Finalrunde').':</th></tr>'."\n";
  
  $out .= '<tr><th width="30%" scope="row" align="left" valign="top"><label for="cs_label_group">'.__('Bezeichnung Spalte 1 (Gruppe)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_group" id="cs_label_group" type="text" value="'.$cs_label_group.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_group" id="cs_col_group" type="checkbox" value="1"'.($cs_col_group==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').'</td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_icon1">'.__('Bezeichnung Spalte 2 (Icon 1)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_icon1" id="cs_label_icon1" type="text" value="'.$cs_label_icon1.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_icon1" id="cs_col_icon1" type="checkbox" value="1" '.($cs_col_icon1==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_match">'.__('Bezeichnung Spalte 3 (Begegnung)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_match" id="cs_label_match" type="text" value="'.$cs_label_match.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_match" id="cs_col_match" type="checkbox" value="1" '.($cs_col_match==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_icon2">'.__('Bezeichnung Spalte 4 (Icon 2)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_icon2" id="cs_label_icon2" type="text" value="'.$cs_label_icon2.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_icon2" id="cs_col_icon2" type="checkbox" value="1" '.($cs_col_icon2==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_location">'.__('Bezeichnung Spalte 5 (Ort)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_location" id="cs_label_location" type="text" value="'.$cs_label_location.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_location" id="cs_col_location" type="checkbox" value="1" '.($cs_col_location==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_time">'.__('Bezeichnung Spalte 6 (Datum/Zeit)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_time" id="cs_label_time" type="text" value="'.$cs_label_time.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_time" id="cs_col_time" type="checkbox" value="1" '.($cs_col_time==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_tip">'.__('Bezeichnung Spalte 7 (Tipp/Ergebnis)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_tip" id="cs_label_tip" type="text" value="'.$cs_label_tip.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_tip" id="cs_col_tip" type="checkbox" value="1" '.($cs_col_tip==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_points">'.__('Bezeichnung Spalte 8 (Punkte)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_points" id="cs_label_points" type="text" value="'.$cs_label_points.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_points" id="cs_col_points" type="checkbox" value="1" '.($cs_col_points==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
    // number of group box
  $out .= '<tr><th scope="row" align="left">'.__('Standardsortierung nach Spalte','wpcs').':</th><td colspan="2"><select name="cs_tipp_sort" id="cs_tipp_sort" class="postform">'."\n";
  for ($i = 1; $i < 9; $i++) {
    $out .= '<option value="'.$i.'"';
    if ( $i == $cs_tipp_sort )
      $out .= ' selected="selected"';
    $out .= '>'.$i.'</option>';
  }
  $out .= '</select></td></tr>'."\n";
  $out .= '<tr><td colspan="3">&nbsp;</td></tr>';
  
  $out .= '<tr><th colspan="3" align="left" scope="row">'.__('Aktueller Punktestand').':</th></tr>'."\n";
  
  $out .= '<tr><th width="30%" scope="row" align="left" valign="top"><label for="cs_label_place">'.__('Bezeichnung Spalte 1 (Platz)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_place" id="cs_label_place" type="text" value="'.$cs_label_place.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_place" id="cs_col_place" type="checkbox" value="1" '.($cs_col_place==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_player">'.__('Bezeichnung Spalte 2 (Spieler)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_player" id="cs_label_player" type="text" value="'.$cs_label_player.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_player" id="cs_col_player" type="checkbox" value="1" '.($cs_col_player==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_upoints">'.__('Bezeichnung Spalte 3 (Punktezahl)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_upoints" id="cs_label_upoints" type="text" value="'.$cs_label_upoints.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_upoints" id="cs_col_upoints" type="checkbox" value="1" '.($cs_col_upoints==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_icon2">'.__('Bezeichnung Spalte 4 (Trend)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_trend" id="cs_label_trend" type="text" value="'.$cs_label_trend.'" size="20" /></td>'."\n";
  $out .= '<td>&nbsp;</td></tr>';
  $out .= '<tr><td colspan="3">&nbsp;</td></tr>';

  
  $out .= '<tr><th colspan="3" align="left" scope="row">'.__('Statistiken').':</th></tr>'."\n";
  
  $out .= '<tr><th width="30%" scope="row" align="left" valign="top"><label for="cs_label_steam">'.__('Bezeichnung Spalte 1 (Mannschaft)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_steam" id="cs_label_steam" type="text" value="'.$cs_label_steam.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_steam" id="cs_col_steam" type="checkbox" value="1" '.($cs_col_steam==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_smatch">'.__('Bezeichnung Spalte 2 (Spiele)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_smatch" id="cs_label_smatch" type="text" value="'.$cs_label_smatch.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_smatch" id="cs_col_smatch" type="checkbox" value="1" '.($cs_col_smatch==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_swin">'.__('Bezeichnung Spalte 3 (Siege)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_swin" id="cs_label_swin" type="text" value="'.$cs_label_swin.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_swin" id="cs_col_swin" type="checkbox" value="1" '.($cs_col_swin==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_stie">'.__('Bezeichnung Spalte 4 (Unentschieden)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_stie" id="cs_label_stie" type="text" value="'.$cs_label_stie.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_stie" id="cs_col_stie" type="checkbox" value="1" '.($cs_col_stie==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_sloose">'.__('Bezeichnung Spalte 5 (Niederlagen)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_sloose" id="cs_label_sloose" type="text" value="'.$cs_label_sloose.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_sloose" id="cs_col_sloose" type="checkbox" value="1" '.($cs_col_sloose==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_sgoal">'.__('Bezeichnung Spalte 6 (Tore)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_sgoal" id="cs_label_sgoal" type="text" value="'.$cs_label_sgoal.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_sgoal" id="cs_col_sgoal" type="checkbox" value="1" '.($cs_col_sgoal==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><th width="30%" scope="row"  align="left" valign="top"><label for="cs_label_spoint">'.__('Bezeichnung Spalte 7 (Punkte)',"wpcs").':</label></th>'."\n";
  $out .= '<td><input name="cs_label_spoint" id="cs_label_spoint" type="text" value="'.$cs_label_spoint.'" size="20" /></td>'."\n";
  $out .= '<td><input name="cs_col_spoint" id="cs_col_spoint" type="checkbox" value="1" '.($cs_col_spoint==1?"checked=\"checked\"":""). ' /> '.__('ausgeblendet','wpcs').' </td></tr>';
  $out .= '<tr><td colspan="3">&nbsp;</td></tr>';
  
 $out .= '</table>'."\n";
 
 // add submit button to form
 $out .= '<p class="submit"><input type="submit" name="update" value="'.__('Einstellungen speichern','wpcs').' &raquo;" /></p>';
 
 
 $out .= '</form></div>'."\n";
 
 echo $out;
}
?>