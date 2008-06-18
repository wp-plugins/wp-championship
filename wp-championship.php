<?php
/*
Plugin Name: wp-championship
Plugin URI: http://www.tuxlog.de/wp-championship
Description: wp-championship is championship plugin for worpress designed for the EM 2008.
Version: 1.2
Author: Hans Matzen <webmaster at tuxlog.de>
Author URI: http://www.tuxlog.de
*/

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
// uncomment this to loose everything when deactivating the plugin
//register_deactivation_hook(__FILE__,'wp_championship_deinstall');


// add option page 
add_action('admin_menu', 'add_menus');

// init plugin
add_action('init', 'wp_championship_init');

// add widgets
// widget #1: nextgames, shows the n coming games with date and location
// widget #2: highscore, shows the n first players with their points
// widget #3: finalround, shows the finalround table
// widget #4: lastresults, shows the n last results 


function wp_championship_init()
{
  // add css in header
  //add_action('wp_head', 'wp_greet_css');

  // Action calls for all functions 
  add_filter('the_content', 'searchcsusertipp');
  add_filter('the_excerpt', 'searchcsusertipp');
  
  add_filter('the_content', 'searchcsuserstats');
  add_filter('the_excerpt', 'searchcsuserstats');
}

// adds the admin menustructure
function add_menus() {

  $PPATH=ABSPATH.PLUGINDIR."/wp-championship/";

  add_menu_page('wp-championship','wp-championship', 8, $PPATH."cs_admin.php","cs_admin");

  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Teams',"wpcs"), __('Mannschaften', "wpcs"), 8, $PPATH."cs_admin_team.php", "cs_admin_team") ;

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Matches',"wpcs"), __('Vorrunde', "wpcs"), 8, $PPATH."cs_admin_match.php", "cs_admin_match") ; 
 
   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Finals',"wpcs"), __('Finalrunde', "wpcs"), 8, $PPATH."cs_admin_finals.php", "cs_admin_finals") ; 

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Users',"wpcs"), __('Mitspieler', "wpcs"), 8, $PPATH."cs_admin_users.php", "cs_admin_users") ; 

   add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Stats',"wpcs"), __('Statistiken', "wpcs"), 8, $PPATH."cs_admin_stats.php", "cs_admin_stats") ;
 }
  


?>
