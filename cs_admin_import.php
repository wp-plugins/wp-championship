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
		exit("wp-load.php not found. Please set path in cs_admin_import.php");
	}
}

require_once(dirname(__FILE__)."/globals.php");
 
// get sql object
global $wpdb;

if (!empty($_POST)) {
  
  $immode = "";
  if (array_key_exists('immode',$_POST)) {
    $immode = $_POST['immode'];
  }
 
  $csv_delall = "";
  if (array_key_exists('csv_delall',$_POST)) {
    $csv_delall = $_POST['csv_delall'];
  }

  $csv_file = "";
  if (array_key_exists('csvfile',$_POST)) {
    $csv_file = $_POST['csvfile'];
  } 

  $fnmode = "";
  if (array_key_exists('fnmode',$_POST)) {
    $fnmode = $_POST['fnmode'];
  }

  // check if current data should be deleted
  if ($csv_delall == true) {
    if ($immode=='team') {
      $sql = "truncate table $cs_team;"; // this also resets the auto increment counter
    }

    if ($immode=='match') {
      $sql = "truncate table $cs_match;";	
    }

    $result = $wpdb->query($sql);
    echo __("Daten gelöscht.","wpcs")."<br />";
  }

  // insert new data
  $row = 1;
  $handle = fopen(dirname(__FILE__) . "/sql/". $csv_file, "r");
  if ($fnmode == "true") {
    // ersten Datensatz überspringen, wenn feldnamen enthalten sind
    $data = fgetcsv($handle, 512, ",", "'");
  }
    while (($data = fgetcsv($handle, 512, ",", "'")) !== FALSE) {
	$num = count($data);
	$row++;
	$errorflag=false;

	if ($immode=="team") {
	  if ($num==7) {
	    $sql  = "insert into $cs_team (tid,name,shortname,icon,groupid,qualified,penalty) values (0,";
	    $sql .= "'".$data[1]."',";
	    $sql .= "'".$data[2]."',"; 
	    $sql .= "'".$data[3]."',"; 
	    $sql .= "'".$data[4]."',"; 
	    $sql .= $data[5].",";
	    $sql .= $data[6].");";
	  } else {
	    $errorflag=true;
	    echo __("Der Datensatz $row hat die falsche Anzahl Felder". count($num). " Ignoriert.","wpcs")."<br />"; 
	  }
	}
	
	if ($immode=="match") {
	  if ($num==12) {
	    $sql  = "insert into $cs_match (mid,round,spieltag,tid1,tid2,location,matchtime,result1,result2,winner,ptid1,ptid2) values (0,";
	    $sql .= "'".$data[1]."',";
	    $sql .= $data[2].","; 
	    $sql .= $data[3].","; 
	    $sql .= $data[4].","; 
	    $sql .= "'".$data[5]."',";
	    $sql .= "'".$data[6]."',";
	    $sql .= $data[7].","; 
	    $sql .= $data[8].",";  
	    $sql .= $data[9].","; 
	    $sql .= $data[10].","; 
	    $sql .= $data[11].");";

	  } else {
	    $erroflag=true;
	    echo __("Der Datensatz $row hat die falsche Anzahl Felder". count($num). " Ignoriert.","wpcs")."<br />"; 
	  }
	}
	$result = $wpdb->query($sql);
	if ($result and !$errorflag) {
	  echo __("Datensatz $data[0] eingefügt.","wpcs")."<br />";
	} else {
	  echo __("Datenbankfehler. Datensatz wurde nicht eingefügt.","wpcs")."<br />";
	}
    }
    fclose($handle);
    
    // you must end here to stop the displaying of the html below
    exit (0);
}

//
// import formular aufbauen ===================================================
//
$out = "";
// add log area style
$out .= "<style>#message {margin:20px; padding:20px; background:#cccccc; color:#cc0000;}</style>";
 
$out .= '<div id="importform" class="wrap" >';
$out .= '<h2>wp-championship '.__('Import',"wpcs").'</h2>';
$out .= '<label for="immode">'.__('Datenimport für','wpcs').':</label>'."\n";
$out .= '<select name="immode" id="immode">'."\n";
$out .= '<option value="team">'.__('Teams','wpcs').'</option>\n';
$out .= '<option value="match">'.__('Spiele','wpcs').'</option>\n';
$out .= "</select><br/><br/>\n";

$out .= "<p>". __("Die CSV-Datei muss im Verzeichnis wp-championship/sql/ abgelegt sein, um sie als Importdatei zu verwenden.","wpcs")."</p>";
$out .= '<label for="csvfile">'.__('CSV-Datei auswählen','wpcs').':</label>'."\n";
$out .= '<select name="csvfile" id="csvfile">'."\n";

// icon file list on disk
$flist = scandir((dirname(__FILE__) . '/sql'));
// file loop
$pak_select_html="";
foreach($flist as $pfile) 
{
    if (substr($pfile,0,1) != "." 
	and substr($pfile,strlen($pfile)-4,4) == ".csv") 
    {
	$pak_select_html .= "<option value='".$pfile."' ";
	$pak_select_html .= ">".$pfile."</option>\n";
    }
} 
$out .= $pak_select_html . "</select><br/><br/>\n";

// first line contains fieldnames
$out .= '<label for="fnmode">'. __('Erste Zeile enthält Feldnamen','wpcs').':</label>'."\n";
$out .= '<input name="fnmode" id="fnmode" type="checkbox" value="1" /><br/>'."\n";

// import mit oder ohne überschreiben
$out .= '<label for="csvdelall">'. __('Vor dem Import alle vorhandenen Daten löschen','wpcs').':</label>'."\n";
$out .= '<input name="csvdelall" id="csvdelall" type="checkbox" value="1" />'."\n";

// add submit button to form
$href= site_url("wp-admin") . "/admin.php?page=cs_admin.php";

$out .= '<p class="submit">';
$out .= '<input type="submit" name="startimport" id="startimport" value="'.
    __('Starte import','wpcs').' &raquo;" onclick="submit_this(\'import\')" />';
$out.="&nbsp;&nbsp;&nbsp;";
$out .= '<input type="submit" name="cancelimport" id="cancelimport" value="'.
    __('Schliessen','wpcs').'" onclick="tb_remove();" /></p>';
$out .= '<hr />'."\n";
// dic ocntainer fuer das verarbeitungs log
$out .= '<div id="message">'.__('Import Protokoll','wpcs').'</div>';
$out .= "</div>\n";

echo $out;
?>
