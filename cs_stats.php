<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2010-2012  Hans Matzen  (email : webmaster at tuxlog.de)

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
/*
 verwendet cs_stats.js für die ajaxeffekte 
 */

// if called directly, get parameters from GET and output the forecast html
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	// direktaufruf für Stats1
	require_once( dirname(__FILE__) . '/../../../wp-config.php');
	
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	$newday   = (isset($_GET['newday'])?esc_attr($_GET['newday']):"");
	$newday5  = (isset($_GET['newday5'])?esc_attr($_GET['newday5']):"");
	$username = (isset($_GET['username'])?esc_attr($_GET['username']):"");
	$args=array();
	$out = "";

	// set character set in case of wrong collation in cs tables
	$sql0="SET CHARACTER SET $wpdb->charset;";
	$r0= $wpdb->query($sql0);

	if (isset($newday) and $newday !="") {
		// Stats 1

		$stats1_tippgroup   = (isset($_GET['tippgroup'])?esc_attr($_GET['tippgroup']):"");
		$tippgroup_sql="";
		if ($stats1_tippgroup !="")
		$tippgroup_sql=" where a.tippgroup='$stats1_tippgroup' ";
			
		if (get_option("cs_modus")==1)
		$sql1 = "SELECT b.user_nicename, sum(e.points) as punkte from $cs_users a inner join $wp_users b on a.userid = b.ID left outer join ( select c.* from $cs_tipp c inner join $cs_match d on c.mid = d.mid where date(d.matchtime) = '$newday' and c.points>0 ) e on e.userid = a.userid $tippgroup_sql group by b.user_nicename order by sum(e.points) desc";
		else
		$sql1 = "SELECT b.user_nicename, sum(e.points) as punkte from $cs_users a inner join $wp_users b on a.userid = b.ID left outer join ( select c.* from $cs_tipp c inner join $cs_match d on c.mid = d.mid where spieltag = '$newday' and c.points>0 ) e on e.userid = a.userid $tippgroup_sql group by b.user_nicename order by sum(e.points) desc" ;
		$r1= $wpdb->get_results($sql1);

		$out .= "<p>&nbsp;</p>";
		$out .= "<table border='1' ><tr><th>" . __("Spieler","wpcs") . "</th><th>" . __("Punkte","wpcs") . "</th></tr>\n";

		foreach ($r1 as $r)
		$out .= "<tr><td>" . $r->user_nicename . "</td><td style='text-align:right'>" . ($r->punkte== NULL?0:$r->punkte) . "</td></tr>\n";

		$out .= "</table>\n";
	} else if (isset($newday5) and $newday5 !="") {
		// Stats 5
		
		
		// get data for header
		if (get_option("cs_modus")==1)
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as shortname1,b.icon as icon1, c.name as team2,c.shortname as shortname2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') and result1>-2 and result2>-2 and date(a.matchtime)='$newday5' order by origtime,mid;";
		else
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as shortname1, b.icon as icon1, c.name as team2,c.shortname as shortname2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' and result1>-2 and result2>-2 and spieltag=$newday5 order by spieltag,origtime,mid;";
		$r1 = $wpdb->get_results($sql);

		$out .= "<p>&nbsp;</p>";
		$out .= "<table id='stats5' border='1' ><tr><th id='stats5'>" . __("Benutzername","wpcs") . "</th>";
		foreach($r1 as $r) {
			$short_team1 = (strlen(trim($r->shortname1))>0?$r->shortname1:substr($r->team1,0,3));
			$short_team2 = (strlen(trim($r->shortname2))>0?$r->shortname2:substr($r->team2,0,3));

			$out .= "<th id='stats5'>" . $short_team1 . "<br />" . ($r->result1==-1?"-":$r->result1) . ":" . ($r->result2==-1?"-":$r->result2) . "<br/>" . $short_team2 . "</th>";
		}
		$out .="<th id='stats5'>&empty;</th><th id='stats5'>".__("Punkte","wpcs")."</th>";
		$out .="</tr>";

		$stats5_tippgroup   = (isset($_GET['tippgroup'])?esc_attr($_GET['tippgroup']):"");
		$tippgroup_sql="";
		if ($stats5_tippgroup !="")
			$tippgroup_sql=" where tippgroup='$stats5_tippgroup' ";
		
		// get data for table
		$sql="select user_nicename, userid from $wp_users inner join $cs_users on ID=userid $tippgroup_sql order by user_nicename;";
		$r2 = $wpdb->get_results($sql);

		foreach($r2 as $r) {
			// fetch results per day and user
			if (get_option("cs_modus")==1)
				$sql="select a.result1 as res1, a.result2 as res2, a.points as points, b.matchtime as origtime from $cs_match b left outer join $cs_tipp a on a.mid=b.mid and a.userid=$r->userid where date(matchtime)='$newday5' and b.result1>-1 and b.result2>-1 and b.round in ('V','F') order by origtime;";
			else
				$sql="select a.result1 as res1, a.result2 as res2, a.points as points, b.matchtime as origtime from $cs_match b left outer join $cs_tipp a on a.mid=b.mid and a.userid=$r->userid where spieltag=$newday5  and b.result1>-1 and b.result2>-1 and b.round ='V' order by spieltag,origtime;";

			$r3 = $wpdb->get_results($sql);
			if ($r3) {

				$out .= "<tr><td>" . $r->user_nicename . "</td>";
				$anz = 0;
				$sum = 0;
				foreach ($r3 as $s) {
					if ( $s->res1 ==-1 or $s->res1 == NULL )
						$out .= "<td>-:-<sub>-</sub></td>";
					else {
						$out .= "<td>" . $s->res1 . ":" . $s->res2 . "<sub>" . $s->points . "</sub></td>";
						$sum += $s->points;
						$anz += 1;
					}
				}
				if ($anz > 0)
					$out .= "<td>" . round($sum/$anz,2) . "</td>";
				else
					$out .= "<td>-</td>";
				$out .= "<td>$sum</td>";
				$out .= "</tr>";
			}
		}
		$out .="</table>";
	} else {
		// Stats 4
		$stats4_tippgroup   = (isset($_GET['wpc_stats4_tippgroup'])?esc_attr($_GET['wpc_stats4_tippgroup']):"");
		$tippgroup_sql="";
		if ($stats4_tippgroup !="")
			$tippgroup_sql=" tippgroup='$stats4_tippgroup' and ";
			
		if (get_option("cs_modus")==1)
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.icon as icon1, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') and result1>-1 and result2>-1 order by origtime;";
		else
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.icon as icon1, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' and result1>-1 and result2>-1 order by spieltag,origtime;";
		$r1 = $wpdb->get_results($sql);

		// hole tipps des users
		if ( $username != "?") {
			$sql="select mid,userid, result1,result2 from  $cs_tipp inner join $wp_users on ID=userid where result1<>-1 and user_nicename='".$username."' order by mid";
		} else {
			$sql="select mid,userid, user_nicename,result1,result2 from  $cs_tipp inner join $wp_users on ID=userid where $tippgroup_sql result1<>-1 order by mid";
		}
		$r2 = $wpdb->get_results($sql);

		$tipps=array();
		foreach($r2 as $r)
			$tipps[$r->mid][$r->userid] = $r;
		
		// hole relevante user
		if ( $username != "?") {
			$sql="select ID from $wp_users where user_nicename='".$username."' ";
		} else {
			$sql="select ID from $wp_users order by user_nicename";
		}
		$r3 = $wpdb->get_results($sql);
		
		$users=array();
		foreach($r3 as $r)
			$users[$r->ID]=$r->ID;
		

		$out .= "<p>&nbsp;</p>";
		if ($username != "?" ) 
			$out .= "<div>" . __("Die Tipps von","wpcs") . " " . $username . "</div>";
		$out .= "<table border='1' ><tr><th>" . __("Begegnung","wpcs") . "</th>";
		if ($username == "?" )
			$out .= "<th>" . __("Mitspieler","wpcs") . "</th>"; 
		$out .= "<th>" . __("Ergebnis","wpcs") . "</th><th>" . __("Tipp","wpcs") . "</th></tr>";

		if (empty($r2)) {
			$out .=  "<tr><td colspan='3'>" . __('Es sind noch keine Tipps abgegeben worden.',"wpcs") ."</td></tr>";
		} else {
			$lteam = "";
			foreach ($r1 as $r) {
				foreach ($users as $s) {
					$ctipp = $tipps[$r->mid][$s];
					$tr1 =  (isset($ctipp->result1) && $ctipp->result1!=-1?$ctipp->result1:"-");
					$tr2 =  (isset($ctipp->result2) && $ctipp->result2!=-1?$ctipp->result2:"-");
					if ($tr1!="-") {
						if ($lteam != $r->team1 . " - " . $r->team2)
							$out .= "<tr><td>" . $r->team1 . " - " . $r->team2 . "</td>";
						else
							$out .= "<tr><td>&nbsp;</td>";
						$lteam = $r->team1 . " - " . $r->team2;
						if ($username == "?" )
							$out .= "<td style='text-align:center'>" . $ctipp->user_nicename . "</td>\n";
						$out .= "<td style='text-align:center'>" . $r->result1 . ":".$r->result2 . "</td>\n";
						$out .= "<td style='text-align:center'>" . $tr1 . ":" . $tr2 . "</td></tr>\n";
					}
				}
			}
		}
			$out .= "</table>\n";
		
	}
	echo $out;
}



// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 1 Punkte jedes Spielers pro Spieltag
// -----------------------------------------------------------------------------------
function show_Stats1($atts)
{
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		return $out;
	}

	// parameter holen dabei übersteuert tippgruppe, tippgroup
	$tippgroup = (isset($atts['tippgroup'])?$atts['tippgroup']:"");
	$tippgroup = (isset($atts['tippgruppe'])?$atts['tippgruppe']:"");

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

	$out .= "<div class='wpc-stats1-sel'><form action='#'>" . __("Spieltag","wpcs").":";
	$out .= "<input id='wpc_stats1_tippgroup' type='hidden' value='$tippgroup' />";
	$out .= "<select id='wpc_stats1_selector' size='1' onchange='wpc_stats1_update();' >";
	if (get_option("cs_modus")==1)
	$sql1 = "SELECT date( matchtime ) as sday FROM $cs_match GROUP BY date( matchtime );";
	else
	$sql1 = "SELECT spieltag as sday FROM $cs_match where spieltag > 0 GROUP BY spieltag;";

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
// Funktion zur Ausgabe der Statistik 2 Verteilung der Tipps über alle User
// -----------------------------------------------------------------------------------
function show_Stats2($atts=array())
{
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		return $out;
	}

	// parameter holen dabei übersteuert tippgruppe, tippgroup
	$tippgroup = (isset($atts['tippgroup'])?$atts['tippgroup']:"");
	$tippgroup = (isset($atts['tippgruppe'])?$atts['tippgruppe']:"");
	
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

	if ($tippgroup !="")
	$sql1 =<<<EOS
	SELECT 
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM $cs_tipp a inner join $cs_users b on a.userid=b.userid 
	WHERE result1>=0 and result2>=0 and tippgroup='$tippgroup'  
	group by IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;
	else
	$sql1 =<<<EOS
	SELECT 
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM $cs_tipp WHERE result1>=0 and result2>=0
	group by IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;

	$r1= $wpdb->get_results($sql1);
	
	// Ueberschrift ausgeben
	$out .= "<h2>".__('Tipph&auml;ufigkeit','wpcs')."</h2>";
	
	if (empty($r1)) {
		$out .=  __('Es sind noch keine Tipps abgegeben worden.',"wpcs");
	} else {
		$urlparm="?";
		// 	anzahl aller tipps ermitteln
		foreach($r1 as $r)
			$tanz = $tanz + $r->anzahl;

		foreach($r1 as $r)
			$urlparm .= $r->tip . "=" . round($r->anzahl / $tanz,2)*100 . "&";
	
		$out .= "<p>&nbsp;</p>";
		$out .= "<img src='" . site_url("wp-content/plugins/wp-championship/") . "func_pie.php" . $urlparm . "' alt='Piechart'/>";
	}
	
	return $out;
}

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 3 Verteilung der Tipps pro Spieler
// -----------------------------------------------------------------------------------
function show_Stats3($atts=array())
{
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		return $out;
	}

	// parameter holen dabei übersteuert tippgruppe, tippgroup
	$tippgroup = (isset($atts['tippgroup'])?$atts['tippgroup']:"");
	$tippgroup = (isset($atts['tippgruppe'])?$atts['tippgruppe']:"");
	
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

	if ($tippgroup!="")
	$sql1 =<<<EOS
	SELECT user_nicename,
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM $cs_tipp a inner join $wp_users b on b.ID=a.userid
	inner join $cs_users c on a.userid = c.userid
	WHERE result1>=0 and result2>=0 and c.tippgroup='$tippgroup'
	group by a.userid, IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;
	else
	$sql1 =<<<EOS
	SELECT user_nicename,
	IF(result1>result2,concat(cast(result1 as char), cast(result2 as char)), 
	   concat(cast(result2 as char), cast(result1 as char))) as tip, count(*) as anzahl
	FROM $cs_tipp
        inner join $wp_users
        on ID=userid
	WHERE result1>=0 and result2>=0
	group by userid, IF(result1>result2,concat(cast(result1 as char), 
	      cast(result2 as char)), concat(cast(result2 as char), cast(result1 as char)))
EOS;

	$r1= $wpdb->get_results($sql1);

	$out .= "<h2>".__('Tipph&auml;ufigkeit im Detail','wpcs')."</h2>";
	
	if (empty($r1)) {
		$out .=  __('Es sind noch keine Tipps abgegeben worden.',"wpcs");
	} else {
		$out .= "<p>&nbsp;</p>";
		$out .= "<table id='stats3' border='1' ><tr id='stats3'><th id='stats3'>" . __("Spieler","wpcs") . "</th>\n";

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
			$out .= "<th id='stats3' style='padding:5px;text-align:center;'>" . $ek ."</th>";
		$out .="</tr>";

		$olduser="";
		foreach ($sm as $uname => $r) {
			if ($olduser != $uname) {
				if ($olduser !="") $out .= "</tr>\n";
					$out .= "<tr><td>" . $uname . "</td>";
			}
			foreach($sm1 as $erg) {
				if (isset($r[$erg]) and $r[$erg]>0)
					$out .= "<td style='text-align:right'>" . $r[$erg] . "</td>\n";
				else
					$out .= "<td style='text-align:right'>-</td>\n";
			}
			$olduser = $uname;
		}
		$out .= "</tr></table>\n";
	}
	
	return $out;
}

// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 4 Tipps eines ausgewählten Spielers
// -----------------------------------------------------------------------------------
function show_Stats4($atts)
{
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		return $out;
	}

	// parameter holen dabei übersteuert tippgruppe, tippgroup
	$tippgroup = (isset($atts['tippgroup'])?$atts['tippgroup']:"");
	$tippgroup = (isset($atts['tippgruppe'])?$atts['tippgruppe']:"");
	
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

	if ($tippgroup !="")
		$sql1 = "SELECT user_nicename FROM $cs_users inner join $wp_users on ID=userid where tippgroup = '$tippgroup' order by user_nicename;";
	else
		$sql1 = "SELECT user_nicename FROM $cs_users inner join $wp_users on ID=userid order by user_nicename;";
	$r1= $wpdb->get_results($sql1);

	$out .= "<h2>" . __("Spielertipps","wpcs") . "</h2>";
	
	$out .= "<div class='wpc-stats4-sel'><form action='#'>" . __("Spieler","wpcs").":";
	$out .= "<input id='wpc_stats4_tippgroup' type='hidden' value='$tippgroup' />";
	$out .= "<select id='wpc_stats4_selector' size='1' onchange='wpc_stats4_update();' >";
	
	$out .= "<option value='?'>" . __("All","wpcs") . "</option>";
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


// -----------------------------------------------------------------------------------
// Funktion zur Ausgabe der Statistik 5 Spieltagsübersicht
// -----------------------------------------------------------------------------------
function show_Stats5($atts)
{
	include("globals.php");
	global $wpdb,$userdata,$wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo <=0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website","wpcs")."<br />";
		return $out;
	}

	// parameter holen dabei übersteuert tippgruppe, tippgroup
	$tippgroup = (isset($atts['tippgroup'])?$atts['tippgroup']:"");
	$tippgroup = (isset($atts['tippgruppe'])?$atts['tippgruppe']:"");
	
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

	$out .= "<h2>" . __("Spieltagsübersicht","wpcs") . "</h2>";


	$out .= "<div class='wpc-stats5-sel'><form action='#'>" . __("Spieltag","wpcs").":";
	$out .= "<input id='wpc_stats5_tippgroup' type='hidden' value='$tippgroup' />";
	$out .= "<select id='wpc_stats5_selector' size='1' onchange='wpc_stats5_update();' >";
	if (get_option("cs_modus")==1)
	$sql1 = "SELECT date( matchtime ) as sday FROM $cs_match GROUP BY date( matchtime );";
	else
	$sql1 = "SELECT spieltag as sday FROM $cs_match where spieltag > 0 GROUP BY spieltag;";

	$r1= $wpdb->get_results($sql1);

	foreach ($r1 as $r)
	$out .= "<option value='" . $r->sday. "'>" . $r->sday . "</option>";

	$out .= "</select>";
	$out .= "<input id='wpc_selector_site' type='hidden' value='" . site_url("/wp-content/plugins/wp-championship")."' />";
	$out .= "</form>";
	$out .= "<script type='text/javascript'>window.onDomReady(wpc_stats5_update);</script>";
	$out .= "</div>";
	$out .= "<div id='wpc-stats5-res'></div>";

	return $out;
}

?>
