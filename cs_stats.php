<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2010  Hans Matzen  (email : webmaster at tuxlog.de)

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
    // direktaufruf für Stats1
    require_once( dirname(__FILE__) . '/../../../wp-config.php');
    include("globals.php");
    global $wpdb,$userdata;
    
    $newday   = (isset($_GET['newday'])?esc_attr($_GET['newday']):"");
    $username = (isset($_GET['username'])?esc_attr($_GET['username']):"");
    $args=array();
    $out = "";

    if (isset($newday) and $newday !="") {
	// Stats 1
	$sql1 =<<<EOS
	SELECT user_nicename, sum(points) as punkte
	FROM `cs_tipp` as a 
	inner join cs_match as b
	on a.mid = b.mid
	inner join wp_users c 
	on c.ID = a.userid
	WHERE date(b.matchtime) = '$newday' and points > 0
	group by userid
EOS;
	$r1= $wpdb->get_results($sql1);
	
	
	$out .= "<p>&nbsp;</p>";
	$out .= "<table border='1' ><tr><td>" . __("Spieler","wpcs") . "</td><td>" . __("Punkte","wpcs") . "</td></tr>\n";
	
	foreach ($r1 as $r) 
	    $out .= "<tr><td>" . $r->user_nicename . "</td><td align='right'>" . $r->punkte . "</td></tr>\n";
	
	$out .= "</table>\n";
    } else {
	// Stats 4
	if (get_option("cs_modus")==1)
	    $sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.icon as icon1, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') and result1>-1 and result2>-1 order by origtime;";
	else
	    $sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.icon as icon1, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' and result1>-1 and result2>-1 order by spieltag,origtime;"; 
	$r1 = $wpdb->get_results($sql);
	
	// hole tipps des users
	$sql="select mid,result1,result2 from  $cs_tipp inner join wp_users on ID=userid where user_nicename='".$username."' order by mid";
	$r2 = $wpdb->get_results($sql);

	$tipps=array();
	foreach($r2 as $r)
	    $tipps[$r->mid] = $r;

	$out .= "<p>&nbsp;</p>";
	$out .= "<table border='1' ><tr><td>" . __("Begegnung","wpcs") . "</td><td>" . __("Ergebnis","wpcs") . "</td><td>" . __("Tipp","wpcs") . "</td></tr>";
	
	foreach ($r1 as $r) {
	    $out .= "<tr><td>" . $r->team1 . " - " . $r->team2. "</td><td align='center'>" . $r->result1 . ":".$r->result2 . "</td>\n";
	    $tr1 =  ($tipps[$r->mid]->result1==-1?"-":$tipps[$r->mid]->result1);
	    $tr2 =  ($tipps[$r->mid]->result2==-1?"-":$tipps[$r->mid]->result2);

	    $out .= "<td align='center'>" . $tr1 . ":" . $tr2 . "</td></tr>\n";
	}
	
	$out .= "</table>\n";

    }
    echo $out;
}  


// apply the filter to the page or post content
function searchcsstats($content) {

  // look for wp-greet tag
  if ( strpos( $content, '[cs-stats1]' ) > -1) {
      // replace tag with html form
      $search = '[cs-stats1]';
      $replace= show_Stats1(); 
      $content= str_replace ($search, $replace, $content);
  }

  // look for wp-greet tag
  if ( strpos( $content, '[cs-stats2]' ) > -1) {
      // replace tag with html form
      $search = '[cs-stats2]';
      $replace= show_Stats2(); 
      $content= str_replace ($search, $replace, $content);
  }

  // look for wp-greet tag
  if ( strpos( $content, '[cs-stats3]' ) > -1) {
      // replace tag with html form
      $search = '[cs-stats3]';
      $replace= show_Stats3(); 
      $content= str_replace ($search, $replace, $content);
  }

  // look for wp-greet tag
  if ( strpos( $content, '[cs-stats4]' ) > -1) {
      // replace tag with html form
      $search = '[cs-stats4]';
      $replace= show_Stats4(); 
      $content= str_replace ($search, $replace, $content);
  }

  return $content;

  }

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 1 Punkte jedes Spielers pro Spieltag
// -----------------------------------------------------------------------------------
function show_Stats1()
{
  include("globals.php");
  global $wpdb,$userdata;
  
  // initialisiere ausgabe variable
  $out = "";
  
  // pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
  // und beende die funktion
  if ( !is_user_logged_in()){
    $out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
    $out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
    return $out;
  }
  
  // for debugging
  //$wpdb->show_errors(true);

  // lese anwenderdaten ein
  get_currentuserinfo();
  // merke die userid 
  $uid = $userdata->ID;
  
  // userdaten lesen
  $sql0="select * from $cs_users where userid=$uid";
  $r0= $wpdb->get_results($sql0);
 
  // admin flag setzen
  $is_admin=false;
  if ( $r0[0]->admin == 1 ) 
      $is_admin=true;
  
  // ermittle aktuelle uhrzeit
  $currtime=date("Y-m-d H:i:s");

  $out .= "<h2>" . __("Spieltagstatistik","wpcs") . "</h2>";
$out .=<<<EOT
<script type='text/javascript'>    
    /*
      javascript for ajax like request to update the stats
      and corresponding data on the fly
    */

    /* get the data for the new location */
    function wpc_stats1_update() {
    
    var newday  = document.getElementById("wpc_stats1_selector").value;
    var siteuri = document.getElementById("wpc_selector_site").value; 
    
    jQuery.get(siteuri + "/cs_stats.php", 
	       { newday: newday, header: "0" , selector: "1" },
	       function(data){
		   jQuery("div#wpc-stats1-res").html(data);
	       });
   }

/* javascript to rebuild the onLoad event for triggering 
   the first wpc_update call */

//create onDomReady Event
window.onDomReady = initReady;

// Initialize event depending on browser
function initReady(fn)
{
    //W3C-compliant browser
    if(document.addEventListener) {
	document.addEventListener("DOMContentLoaded", fn, false);
    }
    //IE
    else {
	document.onreadystatechange = function(){readyState(fn)}
    }
      }

//IE execute function
function readyState(func)
{
    // DOM is ready
      	if(document.readyState == "interactive" || document.readyState == "complete")
      	{
	    func();
      	}
}
</script>
EOT;

  $out .= "<div class='wpc-stats1-sel'><form action=''>" . __("Spieltag","wpcs").":";
  $out .= "<select id='wpc_stats1_selector' size='1' onchange='wpc_stats1_update();' >";
  $sql1 = "SELECT date( matchtime ) as sday FROM `cs_match` GROUP BY date( matchtime );";
  $r1= $wpdb->get_results($sql1);

  foreach ($r1 as $r) 
      $out .= "<option value='" . $r->sday. "'>" . $r->sday . "</option>"; 

  $out .= "</select>";
  $out .= "<input id='wpc_selector_site' type='hidden' value='" . site_url("/wp-content/plugins/wp-championship")."' />";
  $out .= "</form>";
  $out .= "<script type='text/javascript'>window.onDomReady(wpc_stats1_update);</script>";
  $out .= "</div>";
  $out .= "<div id='wpc-stats1-res'></div>";

  return $out;
}

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 2 Verteilung der Tipps üver alle User
// -----------------------------------------------------------------------------------
function show_Stats2()
{
    include("globals.php");
    global $wpdb,$userdata;
    
    // initialisiere ausgabe variable
    $out = "";
    
    // pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
    // und beende die funktion
    if ( !is_user_logged_in()){
	$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
	$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
	return $out;
    }
    
    // for debugging
    //$wpdb->show_errors(true);
    
    // lese anwenderdaten ein
    get_currentuserinfo();
    // merke die userid 
    $uid = $userdata->ID;
    
    // userdaten lesen
    $sql0="select * from $cs_users where userid=$uid";
    $r0= $wpdb->get_results($sql0);
    
    // admin flag setzen
    $is_admin=false;
    if ( $r0[0]->admin == 1 ) 
	$is_admin=true;
    
    // ermittle aktuelle uhrzeit
    $currtime=date("Y-m-d H:i:s");
  
    $sql1 =<<<EOS
	SELECT 
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM `cs_tipp` WHERE result1>0 and result2>0
	group by IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;
    $r1= $wpdb->get_results($sql1);
    
    $urlparm="?";
    foreach($r1 as $r) 
	$urlparm .= $r->tip . "=" . $r->anzahl . "&";
    
    $out .= "<h2>".__('Tipphäufigkeit','wpcs')."</h2>";
    $out .= "<p>&nbsp;</p>";
    $out .= "<img src='" . site_url("wp-content/plugins/wp-championship/") . "func_pie.php" . $urlparm . "' alt='Piechart'/>";

    return $out;
}

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 3 Verteilung der Tipps pro Spieler
// -----------------------------------------------------------------------------------
function show_Stats3()
{
    include("globals.php");
    global $wpdb,$userdata;
    
    // initialisiere ausgabe variable
    $out = "";
    
    // pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
    // und beende die funktion
    if ( !is_user_logged_in()){
	$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
	$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
	return $out;
    }
    
    // for debugging
    //$wpdb->show_errors(true);
    
    // lese anwenderdaten ein
    get_currentuserinfo();
    // merke die userid 
    $uid = $userdata->ID;
    
    // userdaten lesen
    $sql0="select * from $cs_users where userid=$uid";
    $r0= $wpdb->get_results($sql0);
    
    // admin flag setzen
    $is_admin=false;
    if ( $r0[0]->admin == 1 ) 
	$is_admin=true;
    
    // ermittle aktuelle uhrzeit
    $currtime=date("Y-m-d H:i:s");
  
    $sql1 =<<<EOS
	SELECT user_nicename,
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM `cs_tipp`
        inner join wp_users
        on ID=userid
	WHERE result1>0 and result2>0
	group by userid, IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;
    $r1= $wpdb->get_results($sql1);
    
    $out .= "<h2>".__('Tipphäufigkeit im Detail','wpcs')."</h2>";
    $out .= "<p>&nbsp;</p>";

    $out .= "<table border='1' ><tr><td>" . __("Spieler","wpcs") . "</td>\n";
    
    // matrix aufbauen
    $sm = array();
    foreach ($r1 as $r) {
	$erg = $r->tip[0] . ":" . $r->tip[1];
	$sm[$r->user_nicename][$erg] = $r->anzahl;
    }

    // erzeuge liste der vorkommenden ergebnisse
    $sm1 = array();
    foreach(array_keys($sm) as $uk) {
	foreach(array_keys($sm[$uk]) as $ek) {
	    array_push($sm1,$ek);
	}
    }
    $sm1 = array_unique($sm1);
    asort($sm1);
    
    foreach($sm1 as $ek) 
	$out .= "<td>" . $ek ."</td>";
    $out .="</tr>";

    $olduser="";
    foreach ($sm as $uname => $r) {
	if ($olduser != $uname) {
	    if ($olduser !="") $out .= "</tr>\n";
	    $out .= "<tr><td>" . $uname . "</td>";
	}
	foreach($sm1 as $erg) {
	    if (isset($r[$erg]) and $r[$erg]>0)
		$out .= "<td align='right'>" . $r[$erg] . "</td>\n";
	    else
		$out .= "<td align='right'>-</td>\n";
	}
	$olduser = $uname;
    }
    $out .= "</tr></table>\n";
    
    return $out;
}

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 4 Tipps eines ausgewählten Spielers
// -----------------------------------------------------------------------------------
function show_Stats4()
{
  include("globals.php");
  global $wpdb,$userdata;
  
  // initialisiere ausgabe variable
  $out = "";
  
  // pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
  // und beende die funktion
  if ( !is_user_logged_in()){
    $out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
    $out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
    return $out;
  }
  
  // for debugging
  //$wpdb->show_errors(true);

  // lese anwenderdaten ein
  get_currentuserinfo();
  // merke die userid 
  $uid = $userdata->ID;
  
  // userdaten lesen
  $sql0="select * from $cs_users where userid=$uid";
  $r0= $wpdb->get_results($sql0);
 
  // admin flag setzen
  $is_admin=false;
  if ( $r0[0]->admin == 1 ) 
      $is_admin=true;
  
  // ermittle aktuelle uhrzeit
  $currtime=date("Y-m-d H:i:s");

  $out .= "<h2>" . __("Spielertipps","wpcs") . "</h2>";
$out .=<<<EOT
<script type='text/javascript'>    
    /*
      javascript for ajax like request to update the stats
      and corresponding data on the fly
    */

    /* get the data for the new location */
    function wpc_stats4_update() {
    
    var username  = document.getElementById("wpc_stats4_selector").value;
    var siteuri = document.getElementById("wpc_selector_site4").value; 
    
    jQuery.get(siteuri + "/cs_stats.php", 
	       { username: username, header: "0" , selector: "1" },
	       function(data){
		   jQuery("div#wpc-stats4-res").html(data);
	       });
   }

/* javascript to rebuild the onLoad event for triggering 
   the first wpc_update call */

//create onDomReady Event
window.onDomReady = initReady;

// Initialize event depending on browser
function initReady(fn)
{
    //W3C-compliant browser
    if(document.addEventListener) {
	document.addEventListener("DOMContentLoaded", fn, false);
    }
    //IE
    else {
	document.onreadystatechange = function(){readyState(fn)}
    }
      }

//IE execute function
function readyState(func)
{
    // DOM is ready
      	if(document.readyState == "interactive" || document.readyState == "complete")
      	{
	    func();
      	}
}
</script>
EOT;

  $out .= "<div class='wpc-stats4-sel'><form action=''>" . __("Spieltag","wpcs").":";
  $out .= "<select id='wpc_stats4_selector' size='1' onchange='wpc_stats4_update();' >";
  $sql1 = "SELECT user_nicename FROM `cs_users` inner join wp_users on ID=userid order by user_nicename;";
  $r1= $wpdb->get_results($sql1);

  foreach ($r1 as $r) 
      $out .= "<option value='" . $r->user_nicename. "'>" . $r->user_nicename . "</option>"; 

  $out .= "</select>";
  $out .= "<input id='wpc_selector_site4' type='hidden' value='" . site_url("/wp-content/plugins/wp-championship")."' />";
  $out .= "</form>";
  $out .= "<script type='text/javascript'>window.onDomReady(wpc_stats4_update);</script>";
  $out .= "</div>";
  $out .= "<div id='wpc-stats4-res'></div>";

  return $out;
}

?>
