<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2012  Hans Matzen  (email : webmaster at tuxlog.de)

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
// if called directly, get parameters from GET and output the forecast html
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	require_once( dirname(__FILE__) . '/../../../wp-config.php');

	include("globals.php");
	global $wpdb,$wpcs_demo;

	$teamid  = (isset($_GET['teamid'])?esc_attr($_GET['teamid']):"");
	$args=array();

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		echo $out;
		exit;
	}


	// for debugging
	//$wpdb->show_errors(true);

	
	// ausgabe alle spiele des teams
	// -------------------------------------------------------------------

	if (file_exists(  get_stylesheet_directory() . '/wp-championship/icons/' )){
		$iconpath = get_stylesheet_directory_uri() . '/wp-championship/icons/';
	} else {
		$iconpath = plugins_url( 'icons/' , __FILE__ );
	}
	
	$matches = cs_get_team_matches($teamid);
	
	$out .= "<p>&nbsp;</p>";
	$out .= "<table border='1' >\n";
	$out .= "<tr><th>" . __("Datum","wpcs") . "</th><th>&nbsp;</th><th>" . __("Begegnung","wpcs") . "</th><th>&nbsp;</th><th>" . __("Ergebnis","wpcs") . "</th></tr>";
	foreach ($matches as $m) {
			$out .= "<tr><td>" . $m['date']."</td>";
			$out .= "<td><img src='" . $iconpath . $m['icon1'] . "' width='30'></td>";
			$out .= "<td  style='text-align:center'>" . $m['name1'] . " - " . $m['name2'] . "</td>";
			$out .= "<td><img src='" . $iconpath . $m['icon2'] . "' width='30'></td>";
			$out .= "<td style='text-align:center'>" . $m['res1'] . ":" . $m['res2'] . "</td></tr>\n";
	}
	$out .= "</table>\n";
	

	$out .= "<p>&nbsp;</p></div>\n";

	echo $out;
}
?>
