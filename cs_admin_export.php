<?php
 /* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2014  Hans Matzen  (email : webmaster at tuxlog.de)

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

// include wordpress stuff

// Kleine Routine zu Ermittlung wo wor wp-load.php finden
// Wird wp-load.php nicht gefunden, dann wird ein Fehler ausgegeben.
//
 
// Wenn man das Verzeichnis wp-content ausserhalb der normalen Verzeichnissstruktur angelegt hat
// dann muss man die Variable wppath auf diesen Pfad einstellen 
$wppath  = "";     

// Prüfen ob der load path schon definiert ist
if ( !defined('WP_LOAD_PATH') ) {
	// hier ligt wp-load.php, bei der Standardinstallation
	$std_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	
	if (file_exists($std_path . 'wp-load.php')) {
		require_once($std_path."wp-load.php");
	} else if (file_exists($wppath . 'wp-load.php')) {
		require_once( $wppath . "/" . "wp-load.php");
	} else {
		exit("wp-load.php not found. Please set path in cs_admin_export.php");
	}
}

require_once(dirname(__FILE__)."/globals.php");

// get sql object
global $wpdb;

$export_data="";


//
// export daten zusammen stellen
//
$dlmode = "";
if (array_key_exists('dlmode',$_POST)) {
  $dlmode = $_POST['dlmode'];
} 
if (array_key_exists('dlmode',$_GET)) {
  $dlmode = $_GET['dlmode'];
}
$exmode = "";
if (array_key_exists('exmode',$_POST)) {
  $exmode = $_POST['exmode'];
}
if (array_key_exists('exmode',$_GET)) {
  $exmode = $_GET['exmode'];
}

$fnmode = "";
if (array_key_exists('fnmode',$_POST)) {
  $fnmode = $_POST['fnmode'];
}
if (array_key_exists('fnmode',$_GET)) {
  $fnmode = $_GET['fnmode'];
}

if ($dlmode!="") {
  
  if ($exmode=="team") { 
    if ($fnmode == "true") {
      $export_data = __("'TeamID','Name','Kurzname','Wappen','GruppenID','Qualifiziert','Strafpunkte'\n",'wpcs'); 
    }

    $sql="select * from $cs_team order by tid;";
    //$wpdb->show_errors(true);
    $results = $wpdb->get_results($sql);
    foreach($results as $res) {
      $export_data .= $res->tid . ",'";
      $export_data .= $res->name . "','";
      $export_data .= $res->shortname . "','";
      $export_data .= $res->icon . "','";
      $export_data .= $res->groupid . "',";
      $export_data .= $res->qualified .",";
      $export_data .= $res->penalty . "\n";
    }
  }
  
  if ($exmode=="match") {
    if ($fnmode == "true") {
      $export_data = __("'MatchID','Runde','Spieltag','TeamID1','TeamID2','Ort','DatumZeit','Ergebnis1','Ergebnis2','Gewinner','origTeamID1','origTeamID2'\n",'wpcs'); 
    }

    $sql="select * from $cs_match order by mid;";
    //$wpdb->show_errors(true);
    $results = $wpdb->get_results($sql);
    foreach($results as $res) {
      $export_data .= $res->mid . ",'";
      $export_data .= $res->round . "',";
      $export_data .= $res->spieltag . ",";
      $export_data .= $res->tid1 . ",";
      $export_data .= $res->tid2 . ",'";
      $export_data .= $res->location . "','";
      $export_data .= $res->matchtime . "',";
      $export_data .= $res->result1 . ",";
      $export_data .= $res->result2 . ",";
      $export_data .= $res->winner . ",";
      $export_data .= $res->ptid1 . ",";
      $export_data .= $res->ptid2 . "\n";
    }
  }

  // display csv or download it

  if ($dlmode == "true") {
    $fileName = $exmode.".csv";
    
    //header_remove();
    header("Pragma: public"); 
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-type: text/csv"); 
    header("Content-Disposition: attachment; filename={$fileName}");
    header("Content-Transfer-Encoding: binary");

    $fh = @fopen( 'php://output', 'w' );
    $results = $wpdb->get_results( $sql, ARRAY_A );
    foreach($results as $res) {
      fputcsv($fh, $res);
    }
    
    // Close the file
    fclose($fh);
  } else {
    echo $export_data;
  }
  // you must end here to stop the displaying of the html below
  exit (0);
}
//
// export ausgeben ===================================================
//
$out = "";
// add log area style
$out .= "<style>#message {margin:20px; padding:20px; background:#cccccc; color:#cc0000;}</style>";
 
$out .= '<div id="exportform" class="wrap" >';
$out .= '<h2>wp-championship '.__('Export',"wpcs").'</h2><br/>';
$out .= '<label for="exmode">'.__('Datenexport für','wpcs').':</label></th>'."\n";
$out .= '<select name="exmode" id="exmode">'."\n";
$out .= '<option value="team">'.__('Teams','wpcs').'</option>\n';
$out .= '<option value="match">'.__('Spiele','wpcs').'</option>\n';
$out .= "</select><br/>\n";

// first line contains fieldnames
$out .= '<label for="fnmode">'. __('Feldnamen in erster Zeile ausgeben','wpcs').':</label>'."\n";
$out .= '<input name="fnmode" id="fnmode" type="checkbox" value="1" /><br/>'."\n";

// show or download data
$out .= '<label for="dlmode">'. __('Daten als csv Datei herunterladen','wpcs').':</label>'."\n";
$out .= '<input name="dlmode" id="dlmode" type="checkbox" value="1" /><br/>'."\n";

// add submit button to form
$href= site_url("wp-admin") . "/admin.php?page=cs_admin.php";
$out .= '<p class="submit">';
$out .= '<input type="submit" name="startexport" id="startexport" value="'.
    __('Export starten','wpcs').' &raquo;" onclick="submit_this(\'export\')" />';
$out .= "&nbsp;&nbsp;&nbsp;";
$out .= '<input type="submit" name="cancelexport" id="cancelexport" value="'.
    __('Schliessen','wpcs').'" onclick="tb_remove();" />';
$out .= '</p>'."\n";
$out .= "<p>" . __('Mit STRG+a kann der CSV-Export markiert, mit STRG+c kopiert und mit STRG+v in einem Tabellenblatt (z.B. LibreOffice) oder Editor eingefügt werden.',"wpcs") . "</p>";
$out .= '<hr />'."\n";

// div container fuer das verarbeitungs log
$out .= '<textarea name="message" id="message" cols="55" rows="15">&nbsp;</textarea>';
echo $out;
?>
