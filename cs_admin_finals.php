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
// function to show and maintain the set of matches for the championship
//
function cs_admin_finals()
{
  include("globals.php");

  // base url for links
  $thisform = "admin.php?page=wp-championship/cs_admin_finals.php";
  // get group option and define group ids
  $groupstr="ABCDEFGHIJKLM";
  $cs_groups = get_option("cs_groups");
  // get sql object
  $wpdb =& $GLOBALS['wpdb'];

  // find out what we have to do
  $action = "";
  if ( isset( $_POST['submit'] ) )
	$action = "savenew";
  elseif ( isset( $_POST['update'] ) )
    $action = "update";
  elseif ( $_GET['action'] == 'remove' )
    $action = "remove";
  elseif ( $_GET['action'] == 'modify' )
    $action = "edit";


  // add or update match data
  //
  $errflag=0;

  if ( $action == "savenew" or $action == "update" ) {
    // check form contents for mandatory fields
    // and/or set default values
    if ( $_POST['matchtime']=="" )
      $errflag=1;
    if ( $_POST['location']=="" )
      $_POST['location']=__("nowhere","wpcs");
    
    // send a message about mandatory data
    if ( $errflag == 1 )
      admin_message ( __( 'Bitte geben Sie Datum und Uhrzeit der Begegnung ein..',"wpcs" ) );


    // get the teams
    if ( $_POST['winner1'] == -1 ) {
      $team1=$_POST['fgroup1'].$_POST['fplace1'];
      $team2=$_POST['fgroup2'].$_POST['fplace2'];
    } else {
      $team1=($_POST['winner1']==1 ? 'W' : 'V').$_POST['matchid1'];
      $team2=($_POST['winner2']==1 ? 'W' : 'V').$_POST['matchid2']; 
    }

    // check if teams already exist
    $sql="select count(*) as anz from $cs_team where name='#".$team1."';";
    $r0 = $wpdb->get_row($sql);
    if ( $r0->anz == 0) {
      $sql="insert into  $cs_team values (0,'#$team1','','',1);";
      $results = $wpdb->query($sql);
 
      if ( $results == 1 )
	admin_message ( __('Mannschaft 1 automatisch hinzugefügt.',"wpcs") );
      else
	admin_message( __('Datenbankfehler Mannschaft 1 konnte nicht automatisch angelegt werden.',"wpcs") );
    }
    
    $sql="select count(*) as anz from $cs_team where name='#".$team2."';";
    $r0 = $wpdb->get_row($sql);
    if ( $r0->anz == 0) {
      $sql="insert into  $cs_team values (0,'#$team2','','',1);";
      $results = $wpdb->query($sql);
      if ( $results == 1 )
	admin_message ( __('Mannschaft 2 automatisch hinzugefügt.',"wpcs") );
      else
	admin_message( __('Datenbankfehler Mannschaft 2 konnte nicht automatisch angelegt werden.',"wpcs") );
    }


    // error in update form data causes to reprint the update form
    if ( $errflag == 1 and $action == "update" )
      $action = "edit";
    
    // insert new match into database
    if ( $errflag==0 and $action == "savenew" ) {
      // get team ids
      $sql="select tid as tid1 from $cs_team where name='#".$team1."';";
      $r0 = $wpdb->get_row($sql);
      $tid1 = $r0->tid1;

      $sql="select tid as tid2 from $cs_team where name='#".$team2."';";
      $r0 = $wpdb->get_row($sql);
      $tid2 = $r0->tid2;
 
      $sql = "insert into ". $cs_table_prefix ."match values (0,'F'," . $tid1 . "," . $tid2 . ",'" . $_POST['location'] . "','" . $_POST['matchtime'] . "',-1,-1,-1,$tid1,$tid2);";
      $results = $wpdb->query($sql);
      if ( $results == 1 )
	admin_message ( __('Finalbegegnung erfolgreich angelegt.',"wpcs") );
      else
	admin_message( __('Datenbankfehler; Vorgang abgebrochen',"wpcs") );
    }
    
    // update match 
    if ( $errflag==0 and $action == "update" ) {
       // get team ids
      $sql="select tid as tid1 from $cs_team where name='#".$team1."';";
      $r0 = $wpdb->get_row($sql);
      $tid1 = $r0->tid1;

      $sql="select tid as tid2 from $cs_team where name='#".$team2."';";
      $r0 = $wpdb->get_row($sql);
      $tid2 = $r0->tid2;

      $sql = "update ".$cs_table_prefix."match set tid1=" . $tid1 . ", tid2=" . $tid2 . ",location='" . $_POST['location'] . "',matchtime='" . $_POST['matchtime'] . "', ptid1=$tid1, ptid2=$tid2 where mid=".$_POST['mid'].";";
      $results = $wpdb->query($sql);
      if ( $results == 1 )
	admin_message( __('Finalbegegnung erfolgreich gespeichert.',"wpcs") );
      else
	admin_message( __('Datenbankfehler; Vorgang abgebrochen',"wpcs") );
    }
  }
  
  // remove data from database
  if ( $action == 'remove' ) {
    $sql= "delete from ".$cs_table_prefix."match where mid=".$_GET['mid'].";";
    $results = $wpdb->query($sql);
    if ( $results == 1 )
      admin_message( __('Finalbegegnung gelöscht.',"wpcs") );
    else
      admin_message( __('Datenbankfehler; Vorgang abgebrochen',"wpcs") );
  }

 
  // output teams add/modify form
  if ( $action == 'edit' ) {
    // select data to modify
    $sql= "select * from  $cs_match where mid=".$_GET['mid'].";";
    $results = $wpdb->get_row($sql);

    // select stored data for preselection in form
    $sql="select * from  $cs_team where tid=".$results->ptid1.";";
    $r0 = $wpdb->get_row($sql);
    
    $g1=-1; $p1=-1; $w1=-1; $m1=-1;
    $code1=substr($r0->name,1,1);
    if ($code1=='W' or $code1=="V") {
      $w1 = $code1;
      $m1 = substr($r0->name,2);
    } else {
      $g1 = $code1;
      $p1 =  substr($r0->name,2);
    }

    $sql="select * from  $cs_team where tid=".$results->ptid2.";";
    $r1 = $wpdb->get_row($sql);
    
    $g2=-1; $p2=-1; $w2=-1; $m2=-1;
    $code1=substr($r1->name,1,1);
    if ($code1=='W' or $code1=="V") {
      $w2 = $code1;
      $m2 = substr($r1->name,2);
    } else {
      $g2 = $code1;
      $p2 = substr($r1->name,2);
    }
  }
  
  //
  // build form ==========================================================
  //
  $out = "";

  $match1_select_html='<option value="-1">-</option>';
  $match2_select_html='<option value="-1">-</option>';
  $sql="select mid from  $cs_match where round='F' order by mid;";
  $results1 = $wpdb->get_results($sql);
  foreach($results1 as $res) {
    $match1_select_html .= "<option value='".$res->mid."' ";
    if ($res->mid == $m1)
      $match1_select_html .="selected='selected'";
    $match1_select_html .=">".$res->mid."</option>\n";
  
    $match2_select_html .= "<option value='".$res->mid."' ";
    if ($res->mid == $m2)
      $match2_select_html .="selected='selected'";
    $match2_select_html .=">".$res->mid."</option>\n";

  }
  // build the selection boxes
  $groupsel1_html=get_group_selector(get_option("cs_groups"),'fgroup1',$g1);
  $placesel1_html=get_place_selector(get_option("cs_group_teams"),'fplace1',$p1);
  $groupsel2_html=get_group_selector(get_option("cs_groups"),'fgroup2',$g2);
  $placesel2_html=get_place_selector(get_option("cs_group_teams"),'fplace2',$p2);
  
  $wsel1_html = '<select name="winner1"><option value="-1" '.($w1 ==-1 ? 'selected="selected"':'').'>-</option><option value="1" '.($w1 =='W' ? 'selected="selected"':'').'>Gewinner</option><option value="0" '.($w1 =='V' ? 'selected="selected"':'').'>Verlierer</option></select>';
 $wsel2_html = '<select name="winner2"><option value="-1" '.($w2 ==-1 ? 'selected="selected"':'').'>-</option><option value="1" '.($w2 =='W' ? 'selected="selected"':'').'>Gewinner</option><option value="0" '.($w2 =='V' ? 'selected="selected"':'').'>Verlierer</option></select>';

  // select header for update or add match
  if ( $action == 'edit' ) {
    $out .= '<div class="wrap"><h2>'.__('Finalbegegnung ändern',"wpcs").'</h2><div id="ajax-response"></div>'."\n"; 
  $out .= '<form name="modifymatch" id="modifymatch" method="post" action=""><input type="hidden" name="action" value="modifymatch" /><input type="hidden" name="mid" value="'.$results->mid.'" />'."\n";
  } else {
    $out .= '<div class="wrap"><h2>'.__('Finalbegegnung hinzufügen',"wpcs").'</h2><div id="ajax-response"></div>'."\n";
 $out .= '<form name="addmatch" id="addmatch" method="post" action=""><input type="hidden" name="action" value="addmatch" />'."\n";
  }
 
  $out .= '<table class="editform" width="100%" cellspacing="2" cellpadding="2"><tr>';
  $out .= '<th width="33%" scope="row" valign="top"><label for="matchid1">'.__('Mannschaft 1 ',"wpcs").':</label></th>'."\n";
  $out .= '<td width="67%">Gruppe:'.$groupsel1_html.' Platz:'.$placesel1_html.' oder '.$wsel1_html.' Match Nr. <select id="matchid1" name="matchid1">'.$match1_select_html.'</select></td></tr>'."\n";
  $out .= '<tr><th scope="row" valign="top"><label for="matchid2">'.__('Mannschaft 2',"wpcs").' :</label></th>'."\n";
   $out .= '<td width="67%">Gruppe:'.$groupsel2_html.' Platz:'.$placesel2_html.' oder '.$wsel2_html.' Match Nr. <select id="matchid2" name="matchid2">'.$match2_select_html.'</select></td></tr>'."\n";

  $out .= '<tr><th scope="row" valign="top"><label for="location">'.__('Ort','wpcs').':</label></th>'."\n";
  $out .= '<td><input name="location" id="location" type="text" value="'. $results->location.'" size="40" /></td></tr>'."\n";
  $out .= '<tr><th scope="row" valign="top"><label for="matchtime">'.__('Datum / Zeit','wpcs').':</label></th>'."\n";
  $out .= '<td><input name="matchtime" id="matchtime" type="text" value="'. $results->matchtime.'" size="40" /></td></tr>'."\n";

  $out .= '</table>'."\n";
  
  // add submit button to form
  if ( $action == 'edit' ) 
    $out .= '<p class="submit"><input type="submit" name="update" value="'.__('Finalbegegnung speichern','wpcs').' &raquo;" /></p></form></div>'."\n";
  else
    $out .= '<p class="submit"><input type="submit" name="submit" value="'.__('Finalbegegnung hinzufügen','wpcs').' &raquo;" /></p></form></div>'."\n";
  
  echo $out;

  //
  // output match table
  //
  $out = "";
  $out = "<div class=\"wrap\">";
  $out .= "<h2>".__("Finalbegegnungen","wpcs")."</h2>\n"; 
  $out .= "<table class=\"widefat\"><thead><tr>\n";
  $out .= '<th scope="col" style="text-align: center">ID</th>'."\n";
  $out .= '<th scope="col">'.__('Mannschaft 1',"wpcs")."</th>"."\n";
  $out .= '<th scope="col">'.__("Mannschaft 2","wpcs").'</th>'."\n";
  $out .= '<th scope="col" width="90" style="text-align: center">'.__('Ort',"wpcs").'</th>'."\n";
  $out .= '<th scope="col" width="90" style="text-align: center">'.__('Datum / Zeit',"wpcs").'</th>'."\n";
  $out .= '<th colspan="2" style="text-align: center">'.__('Aktion',"wpcs").'</th></tr></thead>'."\n";
  // match loop
  $sql="select a.mid as mid,b.name as team1,c.name as team2,a.location as location,a.matchtime as matchtime from $cs_match a inner join $cs_team b on a.ptid1=b.tid inner join $cs_team c on a.ptid2=c.tid where a.round='F' order by mid;";
  $results = $wpdb->get_results($sql);
  foreach($results as $res) {
    $out .= "<tr><td align=\"center\">".$res->mid."</td><td>".team2text($res->team1)."</td>";
    $out .= "<td>".team2text($res->team2)."</td><td align=\"center\">".$res->location."</td>";
    $out .= "<td align=\"center\">".$res->matchtime."</td>";
    $out .= "<td align=\"center\"><a href=\"".$thisform."&amp;action=modify&amp;mid=".$res->mid."\">".__("Ändern","wpcs")."</a>&nbsp;&nbsp;&nbsp;";
    $out .= "<a href=\"".$thisform."&amp;action=remove&amp;mid=".$res->mid."\">".__("Löschen","wpcs")."</a></td></tr>\n";
  }
  $out .= '</table></div>'."\n";

  echo $out;
}

?>