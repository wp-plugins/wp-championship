<?php
/*
Plugin Name: wp-championship
Plugin URI: http://www.tuxlog.de/wp-championship
Description: wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer.
Version: 5.0
Author: tuxlog 
Author URI: http://www.tuxlog.de
*/

/*  Copyright 2007-2014  Hans Matzen  (email : webmaster at tuxlog dot de)

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
require_once("cs_admin_labels.php");
require_once("cs_usertipp.php");
require_once("cs_userstats.php");
require_once("cs_stats.php");
require_once("wpc_autoupdate.php");
require_once("class_cs_widget.php");
// xmlrpc extension laden wenn diese aktiviert ist
if (get_option("cs_xmlrpc") > 0)
	require_once("cs_xmlrpc.php");

// set this to the demo user id to enable demo mode, everything can be read without being logged in
// or to 0 or false to disable demo mode
static $wpcs_demo=0;
	
// activating deactivating the plugin
register_activation_hook(__FILE__,'wp_championship_install');

// aktion fuer erinnerungsmails hinzufügen
add_action('cs_mailreminder', 'cs_mailservice2');

// uncomment this to loose everything when deactivating the plugin
register_deactivation_hook(__FILE__,'wp_championship_deinstall');

// add option page 
add_action('admin_menu', 'add_menus');

// init plugin
add_action('init', 'wp_championship_init');

// register class
add_action('widgets_init', create_function('', 'return register_widget("cs_widget");')); 

if (get_option("cs_newuser_auto")==1) {
	add_action('user_register','cs_add_user');
}
//
// just return the css link
// this function is called via the wp_head hook
//
function wpcs_css() 
{
    $plugin_url = plugins_url( '/' , __FILE__ );
    $def  = "wp-championship-default.css";
    $user = "wp-championship.css";
    
    if (file_exists( plugin_dir_path( __FILE__ ) . $user))
	    $def =$user;

    if (file_exists(  get_stylesheet_directory() . '/wp-championship/wp-championship.css' ))
    {
        $plugin_url = get_stylesheet_directory_uri() . '/wp-championship/';
    }
    
    echo '<link rel="stylesheet" id="wp-championship-css" href="'. 
	$plugin_url . $def . '" type="text/css" media="screen" />' ."\n";
    
}

// add css im header hinzufügen 
add_action('wp_head', 'wpcs_css');
add_action('admin_head', 'wpcs_css');


function wp_championship_init()
{
  // get translation 
  load_plugin_textdomain('wpcs',false,dirname( plugin_basename( __FILE__ ) ) . "/lang/");
    
  if (function_exists('add_shortcode')) {
  	add_shortcode('cs-usertipp', 'show_UserTippForm');
	add_shortcode('cs-userstats','show_UserStats');
  	add_shortcode('cs-stats1',   'show_Stats1');
  	add_shortcode('cs-stats2',   'show_Stats2');
  	add_shortcode('cs-stats3',   'show_Stats3');
  	add_shortcode('cs-stats4',   'show_Stats4');
  	add_shortcode('cs-stats5',   'show_Stats5');
  	add_shortcode('cs-stats6',   'show_Stats6');
  	add_shortcode('cs-stats7',   'show_Stats7');
  }

  // javascript hinzufügen für tablesorter / floating menu und statistik ajaxeffekt
  if ( ! is_admin()) {
    wp_enqueue_script('cs_tablesort', plugins_url('jquery.tablesorter.min.js', __FILE__),
		      array('jquery'), "2.0.3",true);
    //wp_enqueue_script('cs_dimensions', plugins_url('jquery.dimensions.js', __FILE__),
    //	    array('jquery'), "1.2"); 
    if (file_exists(  get_stylesheet_directory() . '/wp-championship/cs_stats.js' )){
      wp_enqueue_script('cs_stats', get_stylesheet_directory_uri() . '/wp-championship/cs_stats.js', array('jquery'), "9999"); 
    } else {
      wp_enqueue_script('cs_stats', plugins_url('cs_stats.js', __FILE__), array('jquery'), "9999"); 
    }
    wp_enqueue_script('cs_hovertable', plugins_url('jquery.tooltip.js', __FILE__),
		      array('jquery'), "9999");
  } 
}

function wpcs_add_adminjs()
{
	// javascript hinzufügen für tablesorter / floating menu und statistik ajaxeffekt
	wp_enqueue_script('cs_admin', plugins_url('cs_admin.js', __FILE__),
			array(), "9999");
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style ( 'thickbox' );
}

// adds the admin menustructure
function add_menus() {

  $PPATH = plugin_dir_path( __FILE__ );

  $jspage = add_menu_page('wp-champion',__('Tippspiel',"wpcs"), 'manage_options', $PPATH."cs_admin.php","cs_admin",	plugin_dir_url( __FILE__ ) . '/worldcup-icon.png');
  add_action('admin_print_styles-' . $jspage, 'wpcs_add_adminjs');

  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Teams',"wpcs"), __('Mannschaften', "wpcs"), 'manage_options', $PPATH."cs_admin_team.php", "cs_admin_team") ;
  add_action('admin_print_styles-' . $jspage, 'wpcs_add_adminjs');
  
  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Matches',"wpcs"), __('Vorrunde', "wpcs"), 'manage_options', $PPATH."cs_admin_match.php", "cs_admin_match") ; 
 
  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Finals',"wpcs"), __('Finalrunde', "wpcs"), 'manage_options', $PPATH."cs_admin_finals.php", "cs_admin_finals") ; 

  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Users',"wpcs"), __('Mitspieler', "wpcs"), 'manage_options', $PPATH."cs_admin_users.php", "cs_admin_users") ; 

  add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Stats',"wpcs"), __('Statistiken', "wpcs"), 'manage_options', $PPATH."cs_admin_stats.php", "cs_admin_stats") ;

  $jspage = add_submenu_page( $PPATH."cs_admin.php", __('wp-championship Bezeichungen',"wpcs"), __('Bezeichnungen', "wpcs"), 'manage_options', $PPATH."cs_admin_labels.php", "cs_admin_labels") ;
  add_action('admin_print_styles-' . $jspage, 'wpcs_add_adminjs');
 }
?>
