<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2011  Hans Matzen  (email : webmaster at tuxlog.de)

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

require_once(ABSPATH . "wp-includes/class-IXR.php");
require_once(ABSPATH . "wp-includes/class-wp-xmlrpc-server.php");
require_once("cs_stats.php");
//require_once("cs_userstats.php");

//
// class to extend wordpress xmlrpc interface with wp-championship specific methods
//
class WPC_XMLRPC extends wp_xmlrpc_server {
	public function __construct() {
		parent::__construct();

		// define xml methods
		$methods = array(
            'wpc.getStats'       => 'this:wpc_get_Stats',
         	'wpc.getTop'         => 'this:wpc_get_Top',
			'wpc.getRank'		 => 'this:wpc_get_Rank',
			'wpc.getNews'		 => 'this:wpc_get_News',
		    'wpc.getStatsParams' => 'this:wpc_get_Stats_Params'            
            );

            $this->methods = array_merge($this->methods, $methods);
	}

	public static function wpc_getName() {
		return __CLASS__;
	}
	
	protected function html_header() {
		$def  = "xwp-championship-default.css";
    	$user = "xwp-championship.css";
    	$plugin_url = plugins_url("wp-championship/");
    
    	if (file_exists( WP_PLUGIN_DIR . "/wp-championship/" . $user))
			$def =$user;
    
		// das css wird in den header mit rein geschrieben, denn da wir über xmlrpc
		// aufgerufen werden kann der browser realtive urls nicht auflösen
		$csstext = file_get_contents( WP_PLUGIN_DIR . "/wp-championship/" . $def);
		// jetzt ersetzen wir noch die url(..) image angaben mit der absoluten url
		$pattern = "/url\((.*)\)/i";
		$matches=array();
		preg_match($pattern,$csstext,$matches);
		
		$csstext = str_replace($matches[0],"url(" . $plugin_url . $matches[1] . ")",$csstext);
		
    	$head =<<<EOL
		<html>
		<head>
		  <title>wp-championship</title>
		  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
                  <meta name="viewport" content="width=device-width" />
		  <style type="text/css">
		  $csstext
		  </style>		 
		</head>
		<body class="xwpc_body">
		
EOL;
		// nur fürs testen im loalen netz
		$head = str_replace("host1","192.168.2.11",$head);
		
		return $head;
	}
	
	protected function html_footer() {
		$foot ="</body></html>";
		return $foot;
	}

	public function wpc_login($args) {
		$blog_id	= (int) $args[0];
		$username	= $args[1];
		$password	= $args[2];

		if ( !$user = $this->login($username, $password) )
			return $this->error;
		else
			return;
	}


	public function wpc_get_Stats($args) {
		$blog_id	= (int) $args[0];
		$username	= $args[1];
		$password	= $args[2];
		$whichstats = $args[4];
		$param      = $args[5];

		if ( !$user = $this->login($username, $password) )
		return $this->error;
			
		$erg = "";
		switch ($whichstats) {
			case 0:
				$erg = __("Diese Statistik gibt es nicht.","wpcs");
				//$erg = show_UserStats();
				break;
			case 1:
				$erg = $this->show_UserStats1($param);
				break;
			case 2:
				$erg = show_Stats2();
				break;
			case 3:
				$erg = show_Stats3();
				break;
			case 4:
				$erg = $this->show_UserStats4($param);
				break;
			case 5:
				$erg = $this->show_UserStats5($param);
				break;
			case 6:
				$erg = $this->wpc_get_top($param);
				break;
			case 7:
				$erg = $this->wpc_get_Rank($param);
				break;
			default:
				$erg = __("Diese Statistik gibt es nicht.","wpcs");
				break;
		}
		return $this->html_header() . $erg . $this->html_footer();
	}

	//
	// Methode zum Abfragen der möglichen Parameter für eine Statistik
	//
	public function wpc_get_Stats_Params($args) {
			
		include("globals.php");
		global $wpdb;
			
		$blog_id	= (int) $args[0];
		$username	= $args[1];
		$password	= $args[2];
		$whichstats = $args[4];

		if ( !$user = $this->login($username, $password) )
		return $this->error;
			
		$erg = "";
		switch ($whichstats) {
			case 0:
				$erg = "";
				break;
			case 1:
				if (get_option("cs_modus")==1)
					$sql1 = "SELECT date( matchtime ) as sday FROM $cs_match GROUP BY date( matchtime );";
				else
					$sql1 = "SELECT spieltag as sday FROM $cs_match where spieltag > 0 GROUP BY spieltag;";

				$r1= $wpdb->get_results($sql1);

				foreach ($r1 as $r)
				$erg .= $r->sday. ",";
				break;
			case 2:
				$erg = "";
				break;
			case 3:
				$erg = "";
				break;
			case 4:
				$sql1 = "SELECT user_nicename FROM $cs_users inner join $wp_users on ID=userid order by user_nicename;";
				$r1= $wpdb->get_results($sql1);

				if (get_option("cs_xmlrpc_alltipps") > 0)
					$erg .= __("Alle","wpcs") . ",";
				foreach ($r1 as $r) {
					if (get_option("cs_xmlrpc_alltipps") > 0 or $username == $r->user_nicename)
						$erg .= $r->user_nicename . ",";
				}
				break;
			case 5:
				if (get_option("cs_modus")==1)
				$sql1 = "SELECT date( matchtime ) as sday FROM $cs_match GROUP BY date( matchtime );";
				else
				$sql1 = "SELECT spieltag as sday FROM $cs_match where spieltag > 0 GROUP BY spieltag;";

				$r1= $wpdb->get_results($sql1);

				foreach ($r1 as $r)
				$erg .= $r->sday . ",";

				break;
			case 6:
				$sql1 = "SELECT count(*) as c FROM $cs_users;";
				$r1= $wpdb->get_row($sql1);
				$erg = "";
				for ($i = 1; $i <= $r1->c; $i++) {
    				$erg .= $i . ",";
				}
				break;
			case 7:
				$sql1 = "SELECT count(*) as c FROM $cs_team;";
				$r1= $wpdb->get_row($sql1);
				$erg = "";
				for ($i = 1; $i <= $r1->c; $i++) {
					$erg .= $i . ",";
				}
				break;
			default:
				$erg = "Diese Statistik gibt es nicht.";
				break;
		}
		return $erg;
	}

	protected function wpc_get_News() {
		$out="";
		$text = get_option("cs_xmlrpc_news");
		
		return trim($text);
	}

	protected function wpc_get_Top($count)
	{
		include("globals.php");
		global $wpdb;

		//
		// lese alternative bezeichnungen
		//
		$fieldnames = array ("csx_label_group", "csx_col_group", "csx_label_icon1", "csx_col_icon1", "csx_label_match",
		      "csx_col_match", "csx_label_icon2", "csx_col_icon2", "csx_label_location", "csx_col_location",
		      "csx_label_time", "csx_col_time", "csx_label_tip", "csx_col_tip", "csx_label_points",
		      "csx_col_points", "csx_label_place", "csx_col_place", "csx_label_player", "csx_col_player",
		      "csx_label_upoints", "csx_col_upoints", "csx_label_trend", "csx_col_trend", "csx_label_steam",
		      "csx_col_steam", "csx_label_smatch", "csx_col_smatch", "csx_label_swin", "csx_col_swin", 
		      "csx_label_stie", "csx_col_stie", "csx_label_sloose", "csx_col_sloose", "csx_label_sgoal",
		      "csx_col_sgoal", "csx_label_spoint", "csx_col_spoint", "csx_tipp_sort");

		foreach ($fieldnames as $fn)
			eval("\$$fn = get_option(\"$fn\");");
		
		if ($csx_label_place=="")  $csx_label_place   = __("Platz","wpcs");
		if ($csx_label_player=="") $csx_label_player  = __("Spieler","wpcs");
		if ($csx_label_upoints=="")$csx_label_upoints = __("Punktestand","wpcs");
		if ($csx_label_trend=="")  $csx_label_trend   = __("Trend","wpcs");

		if ($csx_label_steam=="")  $csx_label_steam = __("Mannschaft","wpcs");
		if ($csx_label_smatch=="") $csx_label_smatch= __("Spiele","wpcs");
		if ($csx_label_swin==""  ) $csx_label_swin  = __("Siege","wpcs");
		if ($csx_label_stie==""  ) $csx_label_stie  = __("Unentschieden","wpcs");
		if ($csx_label_sloose=="") $csx_label_sloose= __("Niederlagen","wpcs");
		if ($csx_label_sgoal=="" ) $csx_label_sgoal = __("Tore","wpcs");
		if ($csx_label_spoint=="") $csx_label_spoint= __("Punkte","wpcs");

		$out = "";
		// ausgabe des aktuellen punktestandes und des ranges
		$rank = get_ranking();
		//$out .= "<div><h3 class='xwpc_head'>".__("Aktueller Punktestand","wpcs")."</h3>\n";
		$out .= "<table class='xwpc_table'><tr>\n";
		if (!$csx_col_place)
		$out .= '<th class="xwpc_th">'.$csx_label_place .'</th>'."\n";
		if (!$csx_col_player)
		$out .= '<th  class="xwpc_th">'.$csx_label_player.'</th>'."\n";
		if (!$csx_col_upoints)
		$out .= '<th  class="xwpc_th">'.$csx_label_upoints.'</th>';
		if (get_option('cs_rank_trend'))
		$out .= '<th  class="xwpc_th">'.$csx_label_trend.'</th>';
		$out .= "</tr>\n";
		
		$pointsbefore= -1;
		$i=0; $j=1;
		foreach ($rank as $row) {
			// platzierung erhoehen, wenn punkte sich veraendern
			if ($row->points != $pointsbefore) {
				$i = $i + $j;
				$j=1;
			} else
			$j += 1;

			if ($i < $row->oldrank )
			$trend = "&uArr;";
			elseif ($i > $row->oldrank )
			$trend = "&dArr;";
			else
			$trend = "&rArr;";

			$out .= "<tr>";

			if (!$csx_col_place)   $out .= "<td  class='xwpc_td' align='center'>$i</td>";
			if (!$csx_col_player)  $out .= "<td  class='xwpc_td' align='center'>".$row->user_nicename."</td>";
			if (!$csx_col_upoints) $out .= "<td  class='xwpc_td' align='center'>".$row->points. "</td>";

			if (get_option('cs_rank_trend'))
			$out .= "<td class='xwpc_td' align='center'>$trend</td>";
			$out .= "</tr>";

			// gruppenwechsel versorgen
			$pointsbefore = $row->points;
		}
		$out .= '</table>'."\n";

		return $out;
	}


	protected function wpc_get_Rank($count) {

		include("globals.php");
		global $wpdb;

		$out="";
		//
		// lese alternative bezeichnungen
		//
		$fieldnames = array ("csx_label_group", "csx_col_group", "csx_label_icon1", "csx_col_icon1", "csx_label_match",
		      "csx_col_match", "csx_label_icon2", "csx_col_icon2", "csx_label_location", "csx_col_location",
		      "csx_label_time", "csx_col_time", "csx_label_tip", "csx_col_tip", "csx_label_points",
		      "csx_col_points", "csx_label_place", "csx_col_place", "csx_label_player", "csx_col_player",
		      "csx_label_upoints", "csx_col_upoints", "csx_label_trend", "csx_col_trend", "csx_label_steam",
		      "csx_col_steam", "csx_label_smatch", "csx_col_smatch", "csx_label_swin", "csx_col_swin", 
		      "csx_label_stie", "csx_col_stie", "csx_label_sloose", "csx_col_sloose", "csx_label_sgoal",
		      "csx_col_sgoal", "csx_label_spoint", "csx_col_spoint", "csx_tipp_sort");

		foreach ($fieldnames as $fn)
		eval("\$$fn = get_option(\"$fn\");");
		if ($csx_label_place=="")  $csx_label_place   = __("Platz","wpcs");
		if ($csx_label_player=="") $csx_label_player  = __("Spieler","wpcs");
		if ($csx_label_upoints=="")$csx_label_upoints = __("Punktestand","wpcs");
		if ($csx_label_trend=="")  $csx_label_trend   = __("Trend","wpcs");

		if ($csx_label_steam=="")  $csx_label_steam = __("Mannschaft","wpcs");
		if ($csx_label_smatch=="") $csx_label_smatch= __("Spiele","wpcs");
		if ($csx_label_swin==""  ) $csx_label_swin  = __("Siege","wpcs");
		if ($csx_label_stie==""  ) $csx_label_stie  = __("Unentschieden","wpcs");
		if ($csx_label_sloose=="") $csx_label_sloose= __("Niederlagen","wpcs");
		if ($csx_label_sgoal=="" ) $csx_label_sgoal = __("Tore","wpcs");
		if ($csx_label_spoint=="") $csx_label_spoint= __("Punkte","wpcs");

		// Spielübersicht Vorrunde
		$iconpath = get_option("siteurl") . "/wp-content/plugins/wp-championship/icons/";

		// tabellen loop
		// hole tabellen daten
		$results = get_team_clification();

		$groupid_old = "";

		if ($count=="Vorrunde" or $count=="Beide"){
		$out .= "<h3  class='xwpc_head'>".__("Vorrunde","wpcs")."</h3>\n";
		$out .= "<div id='cs_stattab_v'>";

		foreach($results as $res) {

			// bei gruppenwechsel footer / header ausgeben
			if ($res->groupid != $groupid_old) {
				if ($groupid_old !="")
				$out .= '</table><p>&nbsp;</p>';
				 
				 
				$out .= "<h4  class='xwpc_head'>".__("Gruppe","wpcs")." ".$res->groupid."</h4>\n";
				$out .= "<table class='xwpc_table'><thead><tr>\n";
				if (!$csx_col_steam)   $out .= '<th  class="xwpc_th">'.$csx_label_steam."</th>"."\n";
				if (!$csx_col_smatch)  $out .= '<th  class="xwpc_th">'.$csx_label_smatch.'</th>'."\n";
				if (!$csx_col_swin)    $out .= '<th  class="xwpc_th">'.$csx_label_swin.'</th>'."\n";
				if (!$csx_col_stie)    $out .= '<th  class="xwpc_th">'.$csx_label_stie.'</th>'."\n";
				if (!$csx_col_sloose)  $out .= '<th  class="xwpc_th">'.$csx_label_sloose.'</th>'."\n";
				if (!$csx_col_sgoal)   $out .= '<th  class="xwpc_th">'.$csx_label_sgoal.'</th>'."\n";
				if (!$csx_col_spoint)  $out .= '<th  class="xwpc_th">'.$csx_label_spoint.'</th></tr>';
				$out .= '</thead>'."\n";
			}
			 
			// hole statistiken des teams
			$stats=array();
			$stats=get_team_stats($res->tid);
			 
			// zeile ausgeben
			$out .= "<tr>";
			if (!$csx_col_steam) {
				$out .= "<td class='xwpc_td'><img class='csicon' alt='icon1' src='".$iconpath . $res->icon."' />";
				$out .= "<font size='-1'>" . $res->name . "</font></td>";
			}
			if (!$csx_col_smatch) $out .= "<td  class='xwpc_td' align=\"center\">".$stats['spiele']."</td>";
			if (!$csx_col_swin)   $out .= "<td  class='xwpc_td' align=\"center\">".$stats['siege']."</td>";
			if (!$csx_col_stie)   $out .= "<td  class='xwpc_td' align=\"center\">".$stats['unentschieden']."</td>";
			if (!$csx_col_sloose) $out .= "<td  class='xwpc_td' align=\"center\">".$stats['niederlagen']."</td>";
			if (!$csx_col_sgoal)  $out .= "<td  class='xwpc_td' align=\"center\"> ".$res->store." : " .$res->sgegentore." </td>";
			if (!$csx_col_spoint) $out .= "<td  class='xwpc_td' align='center'>" . $res->spoints." </td>";
			$out .= "</tr>\n";
			 
			// gruppenwechsel versorgen
			$groupid_old = $res->groupid;
		}
		$out .= "</table><p>&nbsp;</p></div>\n";
		}
		
		if ($count=="Finalrunde" or $count=="Beide"){ 
		// Finalrunde ausgeben
		$sql1=<<<EOD
   select a.mid as mid, b.icon as icon1, b.name as name1,
   c.icon as icon2, c.name as name2, a.result1 as result1,
   a.result2 as result2, a.location as location, 
   date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,
   a.matchtime as origtime,  
   a.matchtime as matchts
   from  $cs_match a
   inner join  $cs_team b
   on a.tid1=b.tid
   inner join  $cs_team c
   on a.tid2=c.tid
   where a.round='F'
     order by origtime;
EOD;

		$results = $wpdb->get_results($sql1);

		// tabellen kopf ausgeben
		if (!empty($results)) {
			$out .= "<h3  class='xwpc_head' >".__("Finalrunde","wpcs")."</h3>\n";

			$out .= "<table class='xwpc_table'><thead><tr>\n";
			$out .= '<th  class="xwpc_th">'.__("Spielnr.","wpcs").'</th>'."\n";
			$out .= '<th  class="xwpc_th">&nbsp;</th>';
			$out .= '<th  class="xwpc_th">'.__('Begegnung',"wpcs")."</th>"."\n";
			$out .= '<th  class="xwpc_th">&nbsp;</th>';
			$out .= '<th  class="xwpc_th">'.__('Ort',"wpcs").'</th>'."\n";
			$out .= '<th  class="xwpc_th">'.__("Datum<br />Zeit").'</th>'."\n";
			$out .= '<th  class="xwpc_th">'.__("Ergebnis","wpcs").'</th>';
			$out .= '</tr></thead>'."\n";
		}

		foreach($results as $res) {
			// zeile ausgeben
			$out .= "<tr>";
			$out .= "<td class='xwpc_td' align='center'>".$res->mid."</td>";
			if ($res->icon1 != "")
			$out .= "<td class='xwpc_td' ><img class='csicon' alt='icon1' width='15' src='".$iconpath.$res->icon1."' /></td>";
			else
			$out .= "<td class='xwpc_td' >&nbsp;</td>";
			$out .= "<td  class='xwpc_td' align='center'><font size='-1'>".team2text($res->name1) . " - " . team2text($res->name2)."</font></td>";
			if ($res->icon2 != "")
			$out .= "<td class='xwpc_td' ><img class='csicon' alt='icon2' width='15' src='".$iconpath.$res->icon2."' /></td>";
			else
			$out .= "<td class='xwpc_td' >&nbsp;</td>";
			$out .= "<td  class='xwpc_td' align=\"center\"><font size='-1'>".$res->location."</font></td>";
			$out .= "<td  class='xwpc_td' align=\"center\">".$res->matchtime."</td>";
			$out .= "<td  class='xwpc_td' align='center'>";
			$out .= ( $res->result1==-1 ? "-" : $res->result1) ." : ". ($res->result2==-1?"-":$res->result2) . "</td>";
			$out .= "</tr>\n";

		}
		if (!empty($results))
		$out .= "</table>\n";
		}

		return $out;


	}

	protected function show_UserStats1($parm)
	{
		include("globals.php");
		global $wpdb;

		$out = "";
		$newday=$parm;

		if (get_option("cs_modus")==1)
		$sql1 = "SELECT b.user_nicename, sum(e.points) as punkte from $cs_users a inner join $wp_users b on a.userid = b.ID left outer join ( select c.* from $cs_tipp c inner join $cs_match d on c.mid = d.mid where date(d.matchtime) = '$newday' and c.points>0 ) e on e.userid = a.userid order by sum(e.points) desc";
		else
		$sql1 = "SELECT b.user_nicename, sum(e.points) as punkte from $cs_users a inner join $wp_users b on a.userid = b.ID left outer join ( select c.* from $cs_tipp c inner join $cs_match d on c.mid = d.mid where spieltag = '$newday' and c.points>0 ) e on e.userid = a.userid group order by sum(e.points) desc" ;
		$r1= $wpdb->get_results($sql1);

		$out .= "<p>&nbsp;</p>";
		$out .= "<table class='xwpc_table' ><tr><th class='xwpc_th'>" . __("Spieler","wpcs") . "</th><th class='xwpc_th'>" . __("Punkte","wpcs") . "</th></tr>\n";

		foreach ($r1 as $r)
		$out .= "<tr><td class='xwpc_td'>" . $r->user_nicename . "</td><td class='xwpc_td' align='right'>" . ($r->punkte== NULL?0:$r->punkte) . "</td></tr>\n";
		$out .= "</table>\n";

		return $out;
	}

	protected function show_UserStats4($parm)
	{
		include("globals.php");
		global $wpdb;

		$out = "";
		$username=$parm;

		if ($username=="All" or $username=="Alle")
		$username="?";
			
		if (get_option("cs_modus")==1)
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as steam1, b.icon as icon1, c.name as team2, c.shortname as steam2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') and result1>-1 and result2>-1 order by origtime;";
		else
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as steam1, b.icon as icon1, c.name as team2, c.shortname as steam2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' and result1>-1 and result2>-1 order by spieltag,origtime;";
		$r1 = $wpdb->get_results($sql);

		$out .= "<p>&nbsp;</p>";
		// hole tipps des users
		if ( $username != "?") {
			$sql="select mid,result1,result2 from  $cs_tipp inner join $wp_users on ID=userid where user_nicename='".$username."' order by mid";
			$out .= "<p>Tipps von $username:</p>";
		} else {
			$sql="select mid,result1,result2,user_nicename from  $cs_tipp inner join $wp_users on ID=userid $tipgroup_sql order by mid";
			$out .= "<p>Tipps aller Spieler:</p>";
		}
		$r2 = $wpdb->get_results($sql);

		$tipps=array();
		foreach($r2 as $r)
			$tipps[$r->mid] = $r;

		$out .= "<table class='xwpc_table' ><tr><th class='xwpc_th'>" . __("Begegnung","wpcs") . "</th>";
		if ( $username == "?")
		$out .= "<th class='xwpc_th'>" . __("Spieler","wpcs") . "</td>";
		$out .= "<th class='xwpc_th'>" . __("Ergebnis","wpcs") . "</th><th class='xwpc_th'>" . __("Tipp","wpcs") . "</th></tr>";

		foreach ($r1 as $r) {
			if ($tipps[$r->mid]->user_nicename!="" or $username!="?") {
				if (get_option("cs_xmlrpc_shortname") > 0)
					$out .= "<tr><td class='xwpc_td'>" . $r->steam1 . " - " . $r->steam2 . "</td>";
				else
					$out .= "<tr><td class='xwpc_td'>" . $r->team1 . " - " . $r->team2 . "</td>";
				if ( $username == "?")
				$out .= "<td class='xwpc_td'>" . $tipps[$r->mid]->user_nicename . "</td>";
				$out .= "<td class='xwpc_td' align='center'>" . $r->result1 . ":".$r->result2 . "</td>\n";
				$tr1 =  ($tipps[$r->mid]->result1==-1?"-":$tipps[$r->mid]->result1);
				$tr2 =  ($tipps[$r->mid]->result2==-1?"-":$tipps[$r->mid]->result2);

				$out .= "<td class='xwpc_td' align='center'>" . $tr1 . ":" . $tr2 . "</td></tr>\n";
			}
		}

		$out .= "</table>\n";

		return $out;
	}

	protected function show_UserStats5($parm)
	{
		include("globals.php");
		global $wpdb;

		$out = "";
		$newday5=$parm;
			
		// get data for header
		if (get_option("cs_modus")==1)
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as shortname1,b.icon as icon1, c.name as team2,c.shortname as shortname2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') and result1>-2 and result2>-2 and date(a.matchtime)='$newday5' order by origtime,mid;";
		else
		$sql="select a.mid as mid,b.groupid as groupid,b.name as team1,b.shortname as shortname1, b.icon as icon1, c.name as team2,c.shortname as shortname2, c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' and result1>-2 and result2>-2 and spieltag=$newday5 order by spieltag,origtime,mid;";
		$r1 = $wpdb->get_results($sql);

		$out .= "<p>&nbsp;</p>";
		$out .= "<table class='xwpc_table' ><tr><th class='xwpc_th'>" . __("Benutzername","wpcs") . "</th>";
		foreach($r1 as $r) {
			$short_team1 = (strlen(trim($r->shortname1))>0?$r->shortname1:substr($r->team1,0,3));
			$short_team2 = (strlen(trim($r->shortname2))>0?$r->shortname2:substr($r->team2,0,3));

			$out .= "<th class='xwpc_th'>" . $short_team1 . "<br />" . ($r->result1==-1?"-":$r->result1) . ":" . ($r->result2==-1?"-":$r->result2) . "<br/>" . $short_team2 . "</th>";
		}
		$out .="<th class='xwpc_th'>&empty;</th><th class='xwpc_th'>".__("Punkte","wpcs")."</th>";
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
			$sql="select a.result1 as res1, a.result2 as res2, a.points as points, b.matchtime as origtime from $cs_match b left outer join $cs_tipp a on a.mid=b.mid and a.userid=$r->userid where date(b.matchtime)='$newday5' and b.result1>-1 and b.result2>-1 and b.round in ('V','F') order by origtime;";
			else
			$sql="select a.result1 as res1, a.result2 as res2, a.points as points, b.matchtime as origtime from $cs_match b left outer join $cs_tipp a on a.mid=b.mid and a.userid=$r->userid where spieltag=$newday5  and b.result1>-1 and b.result2>-1 and b.round ='V' order by spieltag,origtime;";

			$r3 = $wpdb->get_results($sql);
			if ($r3) {

				$out .= "<tr><td class='xwpc_td'>" . $r->user_nicename . "</td>";
				$anz = 0;
				$sum = 0;
				foreach ($r3 as $s) {
					if ( $s->res1 ==-1 or $s->res1 === NULL )
					$out .= "<td class='xwpc_td'>-:-<sub>-</sub></td>";
					else {
						$out .= "<td class='xwpc_td'>" . $s->res1 . ":" . $s->res2 . "<sub>" . $s->points . "</sub></td>";
						$sum += $s->points;
						$anz += 1;
					}
				}
				if ($anz > 0)
				$out .= "<td class='xwpc_td'>" . round($sum/$anz,2) . "</td>";
				else
				$out .= "<td class='xwpc_td'>-</td>";
				$out .= "<td class='xwpc_td'>$sum</td>";
				$out .= "</tr>";
			}
		}
		$out .="</table>";

		return $out;
	}
}

// replace xmlrpc class for wordpress
add_filter('wp_xmlrpc_server_class', array('WPC_XMLRPC', 'wpc_getName'));

?>
