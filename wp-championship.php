<?php
/*
Plugin Name: wp-championship
Plugin URI: http://www.tuxlog.de/wp-championship
Description: wp-championship is championship plugin for wordpress designed for the WM 2010.
Version: 2.2
Author: tuxlog 
Author URI: http://www.tuxlog.de
*/

/*  Copyright 2007-2010  Hans Matzen  (email : webmaster at tuxlog dot de)

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

// globale konstanten einlesen / Parameter
include ("globals.php");
// include setup functions
require_once("setup.php");
// admin dialog
require_once("cs_admin.php");
require_once("cs_admin_team.php");
require_once("cs_admin_match.php");
require_once("cs_admin_finals.php");
require_once("cs_admin_users.php");
require_once("cs_admin_stats.php");
require_once("cs_usertipp.php");
require_once("cs_userstats.php");

// activating deactivating the plugin
register_activation_hook(__FILE__,'wp_championship_install');
// aktion fuer erinnerungsmails hinzufügen
add_action('cs_mailreminder', 'mailservice2');

// uncomment this to loose everything when deactivating the plugin
register_deactivation_hook(__FILE__,'wp_championship_deinstall');

// add option page 
add_action('admin_menu', 'add_menus');

// init plugin
add_action('init', 'wp_championship_init');

//
// just return the css link
// this function is called via the wp_head hook
//
function wpcs_css() 
{
    $def  = "wp-championship-default.css";
    $user = "wp-championship.css";
    
    if (file_exists( WP_PLUGIN_DIR . "/wp-championship/" . $user))
	$def =$user;
    
    $plugin_url = plugins_url("wp-championship/");
    
    echo '<link rel="stylesheet" id="wp-championship-css" href="'. 
	$plugin_url . $def . '" type="text/css" media="screen" />' ."\n";
    
}

// add css im header hinzufügen 
add_action('wp_head', 'wpcs_css');

// add widgets
// widget #1: nextgames, shows the n coming games with date and location
// widget #2: highscore, shows the n first players with their points
// widget #3: finalround, shows the finalround table
// widget #4: lastresults, shows the n last results 


function wp_championship_init()
{
  // get translation 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';
  if(function_exists('load_textdomain') and $locale != "de_DE") 
    load_textdomain("wpcs",ABSPATH . "wp-content/plugins/wp-championship/lang/".$locale.".mo");

  // Action calls for all functions 
  add_filter('the_content', 'searchcsusertipp');
  add_filter('the_excerpt', 'searchcsusertipp');
  
  add_filter('the_content', 'searchcsuserstats');
  add_filter('the_excerpt', 'searchcsuserstats');

  // javascript hinzufügen für tablesorter / floating menu
  wp_enqueue_script('cs_tablesort', '/' . PLUGINDIR . '/wp-championship/jquery.tablesorter.min.js',
		    array('jquery'), "2.0.3");
  wp_enqueue_script('cs_dimensions', '/' . PLUGINDIR . '/wp-championship/jquery.dimensions.js',
		    array('jquery'), "1.2");
}

// adds the admin menustructure
function add_menus() {

  $PPATH=ABSPATH.PLUGINDIR."/wp-championship/";

  add_menu_page('wp-championship','wp-champion', 8, $PPATH."cs_admin.php","cs_admin",	site_url("/wp-content/plugins/wp-championship") . '/worldcup-icon.png');

  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Teams',"wpcs"), __('Mannschaften', "wpcs"), 8, $PPATH."cs_admin_team.php", "cs_admin_team") ;

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Matches',"wpcs"), __('Vorrunde', "wpcs"), 8, $PPATH."cs_admin_match.php", "cs_admin_match") ; 
 
   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Finals',"wpcs"), __('Finalrunde', "wpcs"), 8, $PPATH."cs_admin_finals.php", "cs_admin_finals") ; 

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Users',"wpcs"), __('Mitspieler', "wpcs"), 8, $PPATH."cs_admin_users.php", "cs_admin_users") ; 

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Stats',"wpcs"), __('Statistiken', "wpcs"), 8, $PPATH."cs_admin_stats.php", "cs_admin_stats") ;
 }
  


?>
