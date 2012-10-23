<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2007-2012  Hans Matzen  (email : webmaster at tuxlog.de)

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
function cs_admin_users()
{
	include("globals.php");

	// base url for links
	$thisform = "admin.php?page=wp-championship/cs_admin_users.php";

	// get sql object
	$wpdb =& $GLOBALS['wpdb'];

	// for debugging only
	//$wpdb->show_errors(true);

	// find out what we have to do
	$action = "";
	if ( isset( $_POST['submit'] ) )
		$action = "savenew";
	elseif ( isset( $_POST['update'] ) )
		$action = "update";
	elseif ( isset($_GET['action']) and $_GET['action'] == 'remove' )
		$action = "remove";
	elseif ( isset($_GET['action']) and  $_GET['action'] == 'modify' )
		$action = "edit";


	// add or update user data
	//
	if ( $action == "savenew" or $action == "update" ) {

		// set empty fields to false
		if ( $_POST['mailservice']=="" )
		$_POST['mailservice']="0";
		if ( $_POST['mailreceipt']=="" )
		$_POST['mailreceipt']="0";
		if ( $_POST['isadmin']=="" )
		$_POST['isadmin']="0";


		// insert new user into database
		if ( $action == "savenew" ) {
			$sql="select count(*) as anz from $cs_users where userid=".$_POST['user'].";";
			$results = $wpdb->get_row($sql);

			if ($results->anz == 0) {

				$sql = "insert into ". $cs_table_prefix ."users values (". $_POST['user'] . "," . $_POST['isadmin'] . "," . $_POST['mailservice'] . "," .$_POST['mailreceipt'] . "," . $_POST['stellv'] . ",".$_POST['champtipp'].",'1900-01-01 00:00:00',-1,'".$_POST['tippgroup']."');";
				$results = $wpdb->query($sql);
				if ( $results == 1 )
				admin_message ( __('Mitspieler erfolgreich angelegt.',"wpcs") );
				else
				admin_message( __('Datenbankfehler; Vorgang abgebrochen.',"wpcs") );
			} else
			admin_message ( __('Mitspieler bereits vorhanden.',"wpcs") );
		}

		// update users
		if (  $action == "update" ) {
			$sql = "update ".$cs_table_prefix."users set admin=" . $_POST['isadmin'] . ", mailservice=" . $_POST['mailservice'] .", mailreceipt=" . $_POST['mailreceipt'] . ",stellvertreter=" . $_POST['stellv'] . ",champion=" . $_POST['champtipp'] . ", tippgroup='".$_POST['tippgroup']."' where userid=".$_POST['user'].";";
			$results = $wpdb->query($sql);

			if ( $results == 1 )
			admin_message( __('Mitspieler erfolgreich gespeichert.',"wpcs") );
			else
			admin_message( __('Datenbankfehler; Vorgang abgebrochen.',"wpcs") );
		}
	}

	// remove data from database
	if ( $action == 'remove' ) {
		$sql= "delete from ".$cs_table_prefix."users where userid=".$_GET['userid'].";";
		$results = $wpdb->query($sql);
		if ( $results >= 1 )
		admin_message( __('Mitspieler gelöscht.',"wpcs") );
		else
		admin_message( __('Datenbankfehler, Vorgang abgebrochen.',"wpcs") );
	}


	// output user add/modify form
	if ( $action == 'edit' ) {
		// select data to modify
		$sql= "select * from  $cs_users where userid=".$_GET['userid'].";";
		$results = $wpdb->get_row($sql);
	} 

	//
	// build form ==========================================================
	//
	$out = "";

	$champtipp_select_html='<option value="-1">-</option>';
	//$sql="select tid,name,champion from $cs_team left outer join $cs_users on tid=champion where name not like '#%'order by name;";

	$sql="select tid,name from $cs_team where name not like '#%' order by name;";
	$results1 = $wpdb->get_results($sql);
	foreach($results1 as $res) {
		$champtipp_select_html .= "<option value='".$res->tid."' ";
		if (isset($results->champion) and $res->tid == $results->champion) // and $res->champion <>"")
		$champtipp_select_html .="selected='selected'";
		$champtipp_select_html .=">".$res->name."</option>\n";
	}

	$stellv_select_html='<option value="0">-</option>';
	$user_select_html='';
	$sql="select ID,user_nicename from $wp_users order by user_nicename;";
	$results1 = $wpdb->get_results($sql);
	foreach($results1 as $res) {
		$stellv_select_html .= "<option value='".$res->ID."' ";
		if (isset($results->stellvertreter) and $res->ID == $results->stellvertreter)
		$stellv_select_html .="selected='selected'";
		$stellv_select_html .=">".$res->user_nicename."</option>\n";

		$user_select_html .= "<option value='".$res->ID."' ";
		if (isset($results->userid) and $res->ID == $results->userid)
		$user_select_html .="selected='selected'";
		$user_select_html .=">".$res->user_nicename."</option>\n";
	}

	// select header for update or add match
	if ( $action == 'edit' ) {
		$out .= '<div class="wrap"><h2>'.__('Mitspieler ändern',"wpcs").'</h2><div id="ajax-response"></div>'."\n"; 
		$out .= '<form name="modifyuser" id="modifyuser" method="post" action="#"><input type="hidden" name="action" value="modifyuser" /><input type="hidden" name="uid" value="'.$results->userid.'" />'."\n";
	} else {
		$out .= '<div class="wrap"><h2>'.__('Mitspieler hinzufügen',"wpcs").'</h2><div id="ajax-response"></div>'."\n";
		$out .= '<form name="adduser" id="adduser" method="post" action="#"><input type="hidden" name="action" value="adduser" />'."\n";
	}

	$out .= '<table class="editform" style="width:100%"><tr>';
	$out .= '<th style="width:33%" scope="row" ><label for="user">'.__('Mitspieler',"wpcs").':</label></th>'."\n";
	$out .= '<td style="width:67%"><select id="user" name="user">'.$user_select_html.'</select></td></tr>'."\n";
	$out .= '<tr><th scope="row" ><label for="mailservice">'.__('Mailservice','wpcs').':</label></th>'."\n";
	$out .= '<td><input name="mailservice" id="mailservice" type="checkbox" value="1" '. ($action=='edit' && $results->mailservice==1?'checked="checked"':'') . '  /></td></tr>'."\n";
	$out .= '<tr><th scope="row" ><label for="mailreceipt">'.__('Mailbestätigung','wpcs').':</label></th>'."\n";
	$out .= '<td><input name="mailreceipt" id="mailreceipt" type="checkbox" value="1" '. ($action=='edit' && $results->mailreceipt==1?'checked="checked"':'') . '  /></td></tr>'."\n";
	$out .= '<tr><th scope="row" ><label for="isadmin">'.__('Tippspiel-Admin','wpcs').':</label></th>'."\n";
	$out .= '<td><input name="isadmin" id="isadmin" type="checkbox" value="1" '. ($action=='edit' && $results->admin=="1"?'checked="checked"':'').'  /></td></tr>'."\n";
	$out .= '<tr><th scope="row" ><label for="stellv">'.__('Stellvertreter',"wpcs").' :</label></th>'."\n";
	$out .= '<td><select id="stellv" name="stellv">'.$stellv_select_html.'</select></td></tr>'."\n";
	$out .= '<tr><th scope="row" ><label for="champtipp">'.__('Sieger-Tipp',"wpcs").' :</label></th>'."\n";
	$out .= '<td><select id="champtipp" name="champtipp">'.$champtipp_select_html.'</select></td></tr>'."\n";

	$out .= '<tr><th scope="row" ><label for="tippgroup">'.__('Tippgruppe','wpcs').':</label></th>'."\n";
	$out .= '<td><input name="tippgroup" id="tippgroup" type="text" value="'. (isset($results->tippgroup)?$results->tippgroup:"").'"  /></td></tr>'."\n";

	$out .= '</table>'."\n";

	// add submit button to form
	if ( $action == 'edit' )
	$out .= '<p class="submit"><input type="submit" name="update" value="'.__('Mitspieler speichern','wpcs').' &raquo;" /></p></form></div>'."\n";
	else
	$out .= '<p class="submit"><input type="submit" name="submit" value="'.__('Mitspieler hinzufügen','wpcs').' &raquo;" /></p></form></div>'."\n";

	echo $out;

	//
	// output user table
	//
	$out = "";
	$out = "<div class=\"wrap\">";
	$out .= "<h2>".__("Mitspieler","wpcs")."</h2>\n";
	$out .= "<table class=\"widefat\"><thead><tr>\n";
	$out .= '<th scope="col" style="text-align: center">Mitspieler-ID</th>'."\n";
	$out .= '<th scope="col">'.__('Name',"wpcs")."</th>"."\n";
	$out .= '<th scope="col" style="width:70px;text-align: center">'.__('Tippspiel-Admin',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="width:70px;text-align: center">'.__('Mailservice',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="width:70px;text-align: center">'.__('Mailbestätigung',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="width:90px;text-align: center">'.__('Stellvertreter',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="width:90px;text-align: center">'.__('Sieger-Tipp',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="width:90px;text-align: center">'.__('Tippgruppe',"wpcs").'</th>'."\n";
	$out .= '<th scope="col" style="text-align: center">'.__('Aktion',"wpcs").'</th></tr></thead>'."\n";
	// match loop
	$sql="select * from $cs_users a inner join $wp_users b on a.userid=b.ID left outer join  $cs_team c on a.champion = c.tid order by b.user_nicename;";
	$results = $wpdb->get_results($sql);

	foreach($results as $res) {
		$out .= "<tr><td style='text-align:center'>".$res->userid."</td><td>".$res->user_nicename."</td>";
		$out .= "<td style='text-align:center'>".$res->admin."</td>";
		$out .= "<td style='text-align:center'>".$res->mailservice."</td><td style='text-align:center'>".$res->mailreceipt."</td>";
		$out .= "<td style='text-align:center'>".$res->stellvertreter."</td>";
		$out .= "<td style='text-align:center'>".($res->champion ==-1 ? "-" : $res->name )."</td>";
		$out .= "<td style='text-align:center'>".($res->tippgroup!=""?$res->tippgroup:"-")."</td>";
		$out .= "<td style='text-align:center'><a href=\"".$thisform."&amp;action=modify&amp;userid=".$res->userid."\">".__("Ändern","wpcs")."</a>&nbsp;&nbsp;&nbsp;";
		$out .= "<a href=\"".$thisform."&amp;action=remove&amp;userid=".$res->userid."\">".__("Löschen","wpcs")."</a></td></tr>\n";
	}
	$out .= '</table></div>'."\n";

	echo $out;
}

?>