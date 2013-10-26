<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2006-2012  Hans Matzen  (email : webmaster at tuxlog.de)

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
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('You are not allowed to call this page directly.');
}

// funktion zum holen einer url (wird verwendet um die lokale zeitzone des users zu ermitteln)
function file_get_contents_utf8($fn) {
	$content = "";
	if (ini_get('allow_url_fopen') and  function_exists("mb_convert_encoding") ) {
		$content = file_get_contents($fn);
		return mb_convert_encoding($content, 'UTF-8',
				mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	} else
		return $content;
}

// -----------------------------------------------------------------------------------
// Funktion zur Verwaltung des kompletten Tippformulars inkl. admin funktionen
// -----------------------------------------------------------------------------------
function show_UserTippForm()
{
	include("globals.php");
	global $wpdb, $userdata, $wpcs_demo;

	// initialisiere ausgabe variable
	$out = "";
	
	// hovertable count init
	$hovertable_count = 0;

	// pruefe ob anwender angemeldet ist, wenn nicht gebe hinweis aus
	// und beende die funktion
	if ( !is_user_logged_in() and $wpcs_demo==0){
		$out .= __("Sie sind nicht angemeldet.","wpcs")."<br />";
		$out .= __("Um am Tippspiel teilzunehmen benötigen Sie ein Konto auf dieser Website.","wpcs")."<br />";
		return $out;
	}
	// for debugging
	//$wpdb->show_errors(true);

	// javascript für floating link ausgeben
	$cs_floating_link = get_option("cs_floating_link");
	if ($cs_floating_link > 0 )
		$out .= get_float_js();

	// lese anwenderdaten ein
	get_currentuserinfo();
	// merke die userid
	$uid = $userdata->ID;

	// set userid from wpcs demo user
	if ($wpcs_demo > 0)
		$uid = $wpcs_demo;

	// lese torsummen schalter
	$cs_goalsum = get_option("cs_goalsum");
	$cs_goalsum_auto = get_option("cs_goalsum_auto");

	// pruefe ob jemand vertreten werden soll und darf
	$cs_stellv_schalter=get_option("cs_stellv_schalter");

	if (isset($_GET['cs_stellv']) and $_GET["cs_stellv"] > 0 and ! $cs_stellv_schalter) {
		$sql="select ID, stellvertreter, user_nicename from $cs_users inner join $wp_users on ID=userid where userid=".$_GET["cs_stellv"].";";
		$r2 = $wpdb->get_row($sql);

		if ($r2->stellvertreter == $uid ) {
			$out .= "<b>".__("Du bist als Stellvertreter aktiv für ","wpcs").$r2->user_nicename.".</b><br />";
			$out .= "<b>".__("Um wieder Deine eigenen Tipps zu bearbeiten, rufe diese Seite einfach neu auf.","wpcs")."</b><br />";
			// user switchen
			$uid=$r2->ID;
		}
	}


	// userdaten lesen
	$sql0="select * from  $cs_users where userid=$uid";
	$r0= $wpdb->get_results($sql0);

	// lesen fuer welcher anderen user der user als vertreter eingetragen ist
	// aber nur wenn nicht bereits eine stellvertreter regelung genutzt wird
	if ( $uid == $userdata->ID) {
		$sql0="select * from  $cs_users inner join $wp_users on ID=userid where stellvertreter=$uid";
		$r1=$wpdb->get_results($sql0);
	}

	// admin flag setzen
	$is_admin=false;
	if ( $r0[0]->admin == 1 )
		$is_admin=true;

	// ermittle aktuelle uhrzeit
	$currtime=date("Y-m-d H:i:s");


	// begruessung ausgeben
	$out .=  __("Willkommen ","wpcs").($uid == $userdata->ID ? $userdata->display_name : $r2->user_nicename) .",<br />";
	$out .= __("auf dieser Seite siehst du deine Tippspielübersicht, kannst neue Tipps abgeben oder abgegebene Tipps bis Spielbeginn verändern, sowie deine persönlichen Einstellungen anpassen.","wpcs")."<br /><hr />";


	// um die vertreterregelung in anspruch zu nehmen, links ausgeben
	// aber nur wenn nicht bereits eine vertreter regelung aktiv ist
	if ( $uid == $userdata->ID and ! $cs_stellv_schalter) {
		$out .= "<p>".__("Du bist als Stellvertreter eingetragen worden von:","wpcs");
		foreach ($r1 as $res) {
	  $plink = get_page_link();
	  $pdelimiter = ( strpos($plink,"/?") > 0 ? "" : "?");
	  $out .= "<a href='". $plink . $pdelimiter . "&amp;cs_stellv=".$res->ID."'>".$res->user_nicename."</a>&nbsp;";
		}
		$out .="</p>";
	}


	// in demo mode skip updates
	if ( !$wpcs_demo>0) {

		$errlist=array();
		// speichern der aenderungen und pruefen der feldinhalte
		// ------------------------------------------------------
		if ( array_key_exists('update',$_POST) and $_POST['update'] == __("Änderungen speichern","wpcs")) {

			// check nonce
			if ( function_exists( 'wp_nonce_field' ))
				check_admin_referer( 'wpcs-usertipp-update');

			// wurde als stellvertreter gespeichert?
			if (isset($_POST['cs_stellv']) && ! $cs_stellv_schalter) {
				$realuser=$uid;
				$uid=$_POST['cs_stellv'];
			}

			// optionen speichern
			$sql1="select count(*) as anz from $cs_users where userid=$uid;";
			$r1 = $wpdb->get_row($sql1);

			// datenfelder auf gueltigkeit pruefen
			if ( $_POST['stellvertreter'] == -1 or $_POST['stellvertreter'] == "-")
				$_POST['stellvertreter']=0;
			if ( !isset($_POST['mailservice']) || $_POST['mailservice'] == '' )
				$_POST['mailservice']=0;
			if ( !isset($_POST['mailreceipt']) || $_POST['mailreceipt'] == '' )
				$_POST['mailreceipt']=0;
			if ( $_POST['champion'] == '' )
				$_POST['champion']=-1;

			// user einstellungen speichern
			if ($r1->anz > 0) {
				$sql0 = "update  $cs_users set mailservice= ".$_POST['mailservice']." , stellvertreter=".$_POST['stellvertreter']." , mailreceipt=".$_POST['mailreceipt']." where userid=$uid;";
			} else {
				$sql0 = "insert into  $cs_users values ($uid,0,".$_POST['mailservice'].",".$_POST['mailreceipt'].",".$_POST['stellvertreter'].",0,'0000-00-00 00:00:00',-1,'');";
			}
			$r3 = $wpdb->query($sql0);

			// championtipp speichern und auf zulaessigkeit pruefen
			$blog_now =  current_time('mysql',0);

			$sql="select min(matchtime) as mintime from $cs_match";
			$mr = $wpdb->get_row($sql);

			if ( $blog_now <= $mr->mintime ) {
				$sql0 = "update  $cs_users set champion= ".$_POST['champion'].",championtime='".$currtime."' where userid=$uid;";
				$r2 = $wpdb->query($sql0);
			} else {
				$out .= __("Championtipp kann nicht mehr verändert werden.","wpcs")."<br />\n";
			}


			// userdaten erneut lesen
			$sql0="select * from  $cs_users where userid=$uid";
			$r0= $wpdb->get_results($sql0);


			$errflag=0;
			$errlist=array(); // enthält die ids der input felder, die fehlerhaft sind
			//
			// tipps plausibiliseren
			//
			foreach ($_POST as $key => $value) {
				$mkey = substr($key,0,4);
				if ( $mkey == "gt1_" or $mkey=="gt2_" or $mkey=="gt3_") {
					$mid=substr($key,4);

					// es sind nur zahlen zugelassen, rest herausfiltern
					// ebenso sind werte kleiner als 0 nicht zugelassen
					if ( $value != preg_replace('/[^0-9]/i', '', $value) or (int) $value < 0) {
						$out .= __("Ungueltiger Tipp, Wert:","wpcs")." $value<br />\n";
						$errflag += 1;
						$errlist[$key]=$key;
					}

					// leere felder auf -1 setzen
					if (trim($_POST[$key])=="") {
						$_POST[$key] = -1;
					}
					
					// pruefe ob das spiel schon begonnen hat
					$sql1="select matchtime from  $cs_match where mid=$mid";
					$r1 = $wpdb->get_results($sql1);
					if ($blog_now > $r1[0]->matchtime ) {
						$out .= __("Das Spiel $mid hat schon begonnen.","wpcs")."<br />".__("Der Tipp kann nicht mehr angenommen werden.","wpcs")."<br />\n";
						$errflag += 1;
					}

					// pruefe ob torsummen tipp erlaubt und im range ist
					if ($cs_goalsum > -1 and $cs_goalsum_auto==0 and $mkey=="gt3_") {
						//$_POST[$key]=(int) $_POST[$key];
						if ( $_POST[$key] < $cs_goalsum and $_POST[$key] >= 0) {
							$out .= __("Die Summe der Tore muss größer als der Schwellwert sein","wpcs")."(".$cs_goalsum.").<br />\n";
							$errflag += 1;
							$errlist[$key]=$key;
							$_POST[$key]=-1;
						}
					}
				}
			}

			// pruefe ob tipp vollständig (beide werte gefüllt?)
			foreach ($_POST as $key => $value) {
				$mkey = substr($key,0,4);
				if ( $mkey == "gt1_" ) {
					$mid=substr($key,4);
					if  ( !(  ($_POST[$key] == -1 and $_POST['gt2_'.$mid] == -1) or ($_POST[$key] >= 0 and $_POST['gt2_'.$mid] >= 0) )) {
						$out .= __("Es fehlt eine Seite des Tipps oder eine Eingabe ist fehlerhaft.","wpcs")."<br />\n";
						$errflag += 1;
						$errlist[$key]=$key;
					}

				}
			}


			// wenn alles in ordnung ist $errflag == 0, dann speichere den tipp
			if ($errflag == 0) {
				// tipp speichern
				$have_tipps=array();
				foreach ($_POST as $key => $value) {
					if ( substr($key,0,4) == "gt1_" or substr($key,0,4)=="gt2_" or substr($key,0,4)=="gt3_") {
						// speichere tipp fuer spiel mid
						$mid=substr($key,4);

						// pruefe ob satz bereits vorhanden
						$sql1="select * from $cs_tipp where userid=$uid and mid=$mid;";
						$r1 = $wpdb->get_row($sql1);

						if ($r1) {
							if ( $r1->result1 != (int) $_POST['gt1_'.$mid] or
									$r1->result2 != (int) $_POST['gt2_'.$mid] or
									(isset($_POST['gt3_'.$mid]) and $r1->result3 != (int) $_POST['gt3_'.$mid]) )  {
								$sql2="update  $cs_tipp set result1=". (int) $_POST['gt1_'.$mid].", result2=".(int) $_POST['gt2_'.$mid].", result3=".(int) $_POST['gt3_'.$mid].", tipptime='$currtime' where userid=$uid and mid=$mid;";
								$r2 = $wpdb->query($sql2);
							}
						} else {
							$sql2="insert into  $cs_tipp values ($uid, $mid, ".(int) $_POST['gt1_'.$mid].", ". (int) $_POST['gt2_'.$mid].", ".(int) $_POST['gt3_'.$mid].",'$currtime',-1);";
							$r2 = $wpdb->query($sql2);
						}
						// tipp merken fuer tipp bestätigungsmail
						$have_tipps[$mid]=$_POST['gt1_'.$mid] . ":" . $_POST['gt2_'.$mid];
					}
				}
				$out .= __("Die Tipps wurden erfolgreich gespeichert.","wpcs")."<br/>";
			}


			if ( $is_admin) {
				$errflag=0;
				$have_results=0;
				// eingegebene ergebnisse plausibiliseren
				foreach ($_POST as $key => $value) {
					$mkey = substr($key,0,4);
					if ( $mkey == "rt1_" or $mkey=="rt2_" ) {
						$mid=substr($key,4);

						// leere felder werden als - dargestellt
						if ( $value =="-" ) {
							$_POST[$key] = "";
							$value="";
						}

						// es sind nur zahlen zugelassen, rest herausfiltern
						// ebenso sind werte kleiner als 0 nicht zugelassen
						if ( $value != preg_replace('/[^0-9]/i', '', $value) or (int) $value < 0) {
							$out .= __("Ungueltiges Ergebnis, Wert:","wpcs")." $value<br />\n";
							$errflag += 1;
							$errlist[$key] = $key;
						}

						// leere felder auf -1 setzen = ergebnis loeschen
						if ($_POST[$key]=="")
							$_POST[$key] = -1;
					}
				}
					
				// pruefe ob ergebnisse vollständig (beide werte gefüllt?)
				foreach ($_POST as $key => $value) {
					$mkey = substr($key,0,4);
					if ( $mkey == "rt1_" ) {
						$mid=substr($key,4); 
						if  ( !(  ($_POST['rt1_'.$mid] == -1 and $_POST['rt2_'.$mid] == -1) or 
								  ($_POST['rt1_'.$mid] >= 0 and $_POST['rt2_'.$mid] >= 0) )) {
							$out .= __("Es fehlt eine Seite des Ergebnisses oder eine Eingabe ist fehlerhaft.","wpcs")."<br />\n";
							$errflag += 1;
							$errlist[$key] = $key;
							
						}
					}
				}

				// wenn alles in ordnung ist $errflag == 0, dann speichere die ergebnisse
				if ($errflag == 0) {
					// tipp speichern
					foreach ($_POST as $key => $value) {
						if ( substr($key,0,4) == "rt1_" or substr($key,0,4)=="rt2_") {
							// speichere tipp fuer spiel mid
							$mid=substr($key,4);

							// gewinner ermitteln
							if ( (int) $_POST['rt1_'.$mid] == -1 and (int) $_POST['rt2_'.$mid]==-1 )
								$winner=-1;
							else if ( (int) $_POST['rt1_'.$mid] > (int) $_POST['rt2_'.$mid] )
								$winner = 1;
							else if ( (int) $_POST['rt2_'.$mid] > (int) $_POST['rt1_'.$mid] )
								$winner = 2;
							else $winner = 0;

							$sql4="select count(*) as anz from cs_match where result1=". (int) $_POST['rt1_'.$mid]." and result2=".(int) $_POST['rt2_'.$mid]." and winner=$winner and mid=$mid;";
							$r4 = $wpdb->get_row($sql4);
							// wenn dieser satz noch nicht aktuell ist, dann speichern wir ihn
							if ($r4->anz == 0) {
								$have_results=1;

								$sql3="update  $cs_match set result1=". (int) $_POST['rt1_'.$mid].", result2=".(int) $_POST['rt2_'.$mid].", winner=".$winner." where mid=$mid;";
								$r3 = $wpdb->query($sql3);
							}
						}
					}
					if ($have_results)
						$out .= __("Die Ergebnisse wurden erfolgreich gespeichert.","wpcs")."<br/>";
				}
					
				// aktuelle mitspieler platzierung speichern
				if (get_option('cs_rank_trend') and $have_results)
					store_current_ranking();
				// punkt nach eingabe neu berechnen
				calc_points();
				// finalrunde eintraege aktualisieren
				update_finals();
			} // end is_admin

			// mailservice für alle user durchführen
			// mailservice durchfuehren (verschickt mails an alle die sie haben wollten)
			if ($have_results)
				mailservice();
			if (isset($have_tipps) and $_POST['mailreceipt']!=0)
				mailservice3($uid, $have_tipps);


			// wurde als stellvertreter gespeichert dann nach speichern
			// wieder umschalten auf realuser
			if (isset($_POST['cs_stellv']))
				$uid=$realuser;
		}
	} // end of demo if

	// ermitteln der zeitzonen des client browsers und des servers
	// die zeitzone in stunden zu gmt  steht in $cltimezone
	// die zeitzone des servers in stunden zu gmt  steht in $setimezone
	// die differenz zwischen lokaler client zeit und spielzeiten in sekunden steht in $timediff
	// die ip adresse für localhost wird dabei ausgesteuert
	//
	$timediff = 0;
	$geores="";
	//if ( $_SERVER['REMOTE_ADDR'] == "127.0.0.1" )
	//	$geores="";
	//else {
	//	$geo_uri = "http://ipinfodb.com/ip_query.php?ip=".$_SERVER['REMOTE_ADDR']."&timezone=true";
	//	$geores  = file_get_contents_utf8($geo_uri);
	//}
	//if ($geores != "" ) {
	//	$spos = strpos($geores, "Gmtoffset") + 10;
	//	$epos = strpos($geores,">",$spos);
	//	$cltimezone = substr($geores,$spos,$epos-$spos+1);
	//	$setimezone = get_option('gmt_offset') * 3600;
	//	$timediff = -1 * $setimezone + $cltimezone;
	//}

	//
	// ausgabe des floating links
	//
	if ($cs_floating_link)
		$out .= '<div id="WPCSfloatMenu"><ul class="menu1"><li><a href="#" onclick="window.scrollTo(0,0); return false;"> ' . __("Nach oben",'wpcs') . ' </a></li></ul></div>';

	
	// -------------------------------------------------------------------
	// ausgabe der optionen und der tipptabelle
	// -------------------------------------------------------------------


	//
	// lese alternative bezeichnungen
	//
	$fieldnames = array ("cs_label_group", "cs_col_group", "cs_label_icon1", "cs_col_icon1", "cs_label_match",
			"cs_col_match", "cs_label_icon2", "cs_col_icon2", "cs_label_location", "cs_col_location",
			"cs_label_time", "cs_col_time", "cs_label_tip", "cs_col_tip", "cs_label_points",
			"cs_col_points", "cs_label_place", "cs_col_place", "cs_label_player", "cs_col_player",
			"cs_label_upoints", "cs_col_upoints", "cs_label_trend", "cs_col_trend", "cs_label_steam",
			"cs_col_steam", "cs_label_smatch", "cs_col_smatch", "cs_label_swin", "cs_col_swin",
			"cs_label_stie", "cs_col_stie", "cs_label_sloose", "cs_col_sloose", "cs_label_sgoal",
			"cs_col_sgoal", "cs_label_spoint", "cs_col_spoint", "cs_tipp_sort");

	foreach ($fieldnames as $fn)
		eval("\$$fn = get_option(\"$fn\");");

	if ($cs_label_group=="")    $cs_label_group   = __("Gruppe","wpcs");
	if ($cs_label_icon1=="")    $cs_label_icon1   = "&nbsp;";
	if ($cs_label_match==""  )  $cs_label_match   = __("Begegnung","wpcs");
	if ($cs_label_icon2==""  )  $cs_label_icon2   = "&nbsp;";
	if ($cs_label_location=="") $cs_label_location =  __("Ort","wpcs");
	if ($cs_label_time=="" )    $cs_label_time    = __("Datum<br />Zeit","wpcs");
	if ($cs_label_tip=="")      $cs_label_tip     = __("Tipp<br/>Ergebnis","wpcs");
	if ($cs_label_points=="")   $cs_label_points  = __("Punkte","wpcs");


	// teamliste fuer select aufbauen
	$team1_select_html="";
	$sql="select tid,name from  $cs_team where name not like '#%' order by name;";
	$results1 = $wpdb->get_results($sql);
	$team1_select_html .= "<option value='-1'>-</option>";
	foreach($results1 as $res) {
		$team1_select_html .= "<option value='".$res->tid."' ";
		if ($res->tid == $r0[0]->champion) {
			$team1_select_html .="selected='selected'";
			$champion_team = $res->name;
		}
		$team1_select_html .=">".$res->name."</option>\n";
	}


	// userliste fuer select aufbauen
	$user1_select_html="";
	$sql="select ID,user_nicename from $wp_users inner join $cs_users on ID=userid order by user_nicename;";
	$results1 = $wpdb->get_results($sql);
	$user1_select_html .= "<option value='-1'>-</option>";
	foreach($results1 as $res) {
		$user1_select_html .= "<option value='".$res->ID."' ";
		if ((int) $res->ID == (int) $r0[0]->stellvertreter)
			$user1_select_html .="selected='selected'";
		$user1_select_html .=">".$res->user_nicename."</option>\n";
	}


	// anzeigen wenn der user admin des tippspiels ist
	if ( $is_admin )
		$out .= '<b>'.__("Du bist Tippspiel-Administrator.","wpcs").'</b><br />';

	// ausgabe des aktuellen punktestandes und des ranges
	$rank = get_ranking();

	$i=0;
	$j=1;
	$pointsbefore= -1;
	foreach ($rank as $row) {
		// platzierung erhoehen, wenn punkte sich veraendern
		if ($row->points != $pointsbefore) {
			$i += $j;
			$j=1;
		} else
			$j += 1;

		if ($row->userid == $uid)
			$out .= "<div><b>".__("Du hast insgesamt","wpcs")." ".$row->points. " ".__("Punkte und bist aktuell auf Platz","wpcs")." $i.</b></div>";
		// gruppenwechsel versorgen
		$pointsbefore = $row->points;
	}

	$out.="<hr/>";

	// formularkopf
	$out .="<form method='post' action='#'>\n";
	$out .= "<div class='submit' style='text-align:right'>";

	// new add nonce field if possible
	if ( function_exists( 'wp_nonce_field' )) {
		$out .= wp_nonce_field('wpcs-usertipp-update',"_wpnonce",true,false);
		$out .= "&nbsp;";
	}

	// wenn als stellvertreter unterwegs, dann hidden field mitgeben
	// um beim speichern zu erkennen fuer wen gespeichert wird
	if ($userdata->ID != $uid)
		$out .= "<input type='hidden' name='cs_stellv' value='$uid' />";

	$out .= "<input type='submit' class='wpcs-button' name='update' value='".__("Änderungen speichern","wpcs")."' /></div>";
	
	// persönliche Einstellungen
	$out .= "<h2>".__("Einstellungen","wpcs")."</h2>\n";
	$out .= "<table>\n";

	$out .= "<tr>";
	if ( ! $cs_stellv_schalter )
		$out .= "<td>".__("Stellvertreter:","wpcs")." <select name='stellvertreter'>".$user1_select_html."</select></td>";
	else
		$out .= "<td>&nbsp;<input type='hidden' name='stellvertreter' value='-' /></td>";

	$out .= "<td><input type='checkbox' name='mailservice' value='1' ";
	$out .= ($r0[0]->mailservice==1?'checked="checked"':'') ." /> ".__("Mailerinnerung","wpcs")."<br />";
	$out .= "<input type='checkbox' name='mailreceipt' value='1' ";
	$out .= ($r0[0]->mailreceipt==1?'checked="checked"':'') ." /> ".__("Mailbestätigung","wpcs")."</td></tr>";

	if (get_option("cs_pts_champ") > 0) {
		$out .='<tr><td style="text-align:center" colspan="2">'.__("Sieger-Tipp","wpcs").": ";
		// 	weltmeistertipp kann nur bis tunierbeginn abgegeben werden
		// ermittle aktuell blog zeit
		$blog_now =  current_time('mysql',false);

		$sql="select min(matchtime) as mintime from $cs_match";
		$mr = $wpdb->get_row($sql);

		if ( $blog_now > $mr->mintime ) {
			$out .= '<select name="championshow" disabled="disabled">'.$team1_select_html.'</select>';
			$out .= '<input type="hidden" name="champion" value="'.$r0[0]->champion.'" /></td></tr>';
		} else
			$out .= '<select name="champion">'.$team1_select_html.'</select></td></tr>';
	}
	$out .= "</table>\n";


	// Spielübersicht Vorrunde
	$iconpath = get_option("siteurl") . "/wp-content/plugins/wp-championship/icons/";

	// sortierbare tabelle nur im tunier modus
	if ( get_option('cs_modus') == 1 ) {
		if (!$cs_tipp_sort >= 1) $cs_tipp_sort=1;
		$out .= "<script type='text/javascript'><!--\njQuery(document).ready(function() { jQuery('#ptab').tablesorter({sortList:[[". --$cs_tipp_sort .",0]],headers:{1:{sorter:false},3:{sorter:false}}}); }); jQuery(document).ready(function() { jQuery('#ftab').tablesorter({sortList:[[0,0]],headers:{1:{sorter:false},3:{sorter:false}}}); });\n//--></script>\n";
	}
	// collapse / expand für den bundesliga modus
	if ( get_option('cs_modus') == 2 )
		$out .= "<script type='text/javascript'><!--\njQuery(document).ready(function() { var toggleMinus = '".site_url(PLUGINDIR . "/wp-championship/arrow_down.jpg")."'; var togglePlus = '".site_url(PLUGINDIR . "/wp-championship/arrow_right.jpg")."'; var AsubHead = jQuery('tbody th:first-child'); AsubHead.prepend('<img src=\"' + toggleMinus + '\" alt=\"collapse this section\" />'); jQuery('img', AsubHead).addClass('clickable') .click(function() { var toggleSrc = jQuery(this).attr('src'); if ( toggleSrc == toggleMinus ) { jQuery(this).attr('src', togglePlus) .parents('tr').siblings().fadeOut('fast'); } else{ jQuery(this).attr('src', toggleMinus) .parents('tr').siblings().fadeIn('fast'); }; }); jQuery('.clickable').trigger('click'); jQuery('img','#currspieltag').trigger('click'); });\n//--></script>\n";


	$out .= "<br /><h2>".__("Vorrundenspiele","wpcs")."</h2>\n";
	$out .= "<table id='ptab' class='tablesorter' ><thead><tr>\n";
	//$out .= '<th scope="col" style="text-align: center">Spiel-Nr.</th>'."\n";
	if ( get_option('cs_modus') == 1 and !$cs_col_group)
		$out .= '<th scope="col" style="text-align: center">'.$cs_label_group.'</th>'."\n";
	if (!$cs_col_icon1) $out .= '<th >'.$cs_label_icon1.'</th>'."\n";
	if (!$cs_col_match) $out .= '<th scope="col" style="text-align: center">'.$cs_label_match."</th>"."\n";
	if (!$cs_col_icon2) $out .= '<th >'.$cs_label_icon2.'</th>'."\n";
	if (!$cs_col_location) $out .= '<th scope="col" style="text-align: center">'.$cs_label_location.'</th>'."\n";
	if (!$cs_col_time) $out .= '<th id="p1stsort" scope="col" style="text-align: center">'.$cs_label_time.'</th>'."\n";
	if (!$cs_col_tip) $out .= '<th style="text-align:center">'.$cs_label_tip.'</th>';
	if ($cs_goalsum > -1 and $cs_goalsum_auto==0)
		$out .= '<th style="text-align:center">'.__("Summe<br />Tore","wpcs").'</th>';
	if (!$cs_col_points)
		$out .= '<th style="text-align:center">'.$cs_label_points.'</th></tr>';

	$out .= '</thead>'."\n";
	if (get_option("cs_modus")==1)
		$out .= '<tbody>'."\n";

	// match loop
	// hole match daten
	if (get_option("cs_modus")==1)
		$sql="select a.mid as mid,b.groupid as groupid,b.tid as tid1, b.name as team1,b.icon as icon1, c.tid as tid2, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round in ('V','F') order by origtime;";
	else
		$sql="select a.mid as mid,b.groupid as groupid,b.tid as tid1, b.name as team1,b.icon as icon1, c.tid as tid2, c.name as team2,c.icon as icon2,a.location as location,date_format(a.matchtime,'%d.%m<br />%H:%i') as matchtime,a.matchtime as origtime,a.result1 as result1,a.result2 as result2,a.winner as winner,a.round as round, a.spieltag as spieltag from $cs_match a inner join $cs_team b on a.tid1=b.tid inner join $cs_team c on a.tid2=c.tid where a.round = 'V' order by spieltag,origtime;";
	$results = $wpdb->get_results($sql);

	// hole tipps des users
	$sql="select * from  $cs_tipp where userid=$uid";
	$results2 = $wpdb->get_results($sql);
	// und lege die tipps im array _POST ab
	foreach ($results2 as $res) {
		if ($res->result1!=-1)
			$_POST[ 'gt1_'.$res->mid ] = $res->result1;
		if ($res->result2 != -1)
			$_POST[ 'gt2_'.$res->mid ] = $res->result2;
		if ($res->result3 != -1)
			$_POST[ 'gt3_'.$res->mid ] = $res->result3;

		// setze -1 felder auf leer wenn diese keinen fehler ausgeloest haben
		if (isset($_POST[ 'gt1_'.$res->mid ]) and $_POST[ 'gt1_'.$res->mid ] == -1 and ! array_key_exists('gt1_'.$res->mid,$errlist))
			$_POST[ 'gt1_'.$res->mid ] = "";
		if (isset($_POST[ 'gt2_'.$res->mid ]) and $_POST[ 'gt2_'.$res->mid ] == -1 and ! array_key_exists('gt2_'.$res->mid,$errlist))
			$_POST[ 'gt2_'.$res->mid ] = "";
		if (isset($_POST[ 'gt3_'.$res->mid ]) and $_POST[ 'gt3_'.$res->mid ] == -1 and ! array_key_exists('gt3_'.$res->mid,$errlist))
			$_POST[ 'gt3_'.$res->mid ] = "";


		$_POST[ 'pt_'.$res->mid ] = $res->points;
	}

	$lastmatchround='';
	$bl_lastspieltag='';
	$bl_sql1 = "select min(spieltag) as mst from $cs_match where result1=-1;";
	$bl_res1 = $wpdb->get_row($bl_sql1);
	$bl_currspieltag = $bl_res1->mst;
	
	if (!isset($errlist))
		$errlist = array();
	
	foreach($results as $res) {

		if ($lastmatchround =='V' and $res->round=='F') {
			$out .= '</tbody></table>'."<p>&nbsp;</p>\n";
			$out .= "<h2>".__("Finalrunde","wpcs")."</h2>\n";
			$out .= "<table id='ftab' class='tablesorter'><thead><tr>\n";

			$out .= '<th scope="col" style="text-align: center">'.__("Spielnr.","wpcs").'</th>'."\n";
			if (!$cs_col_icon1) $out .= '<th >'.$cs_label_icon1.'</th>'."\n";
			if (!$cs_col_match) $out .= '<th scope="col" style="text-align: center">'.$cs_label_match."</th>"."\n";
			if (!$cs_col_icon2) $out .= '<th >'.$cs_label_icon2.'</th>'."\n";
			if (!$cs_col_location) $out .= '<th scope="col" style="text-align: center">'.$cs_label_location.'</th>'."\n";
			if (!$cs_col_time) $out .= '<th id="p1stsort" scope="col" style="text-align: center">'.$cs_label_time.'</th>'."\n";
			if (!$cs_col_tip) $out .= '<th style="text-align:center">'.$cs_label_tip.'</th>';
			if ($cs_goalsum > -1 and $cs_goalsum_auto==0)
				$out .= '<th style="text-align:center">'.__("Summe<br />Tore","wpcs").'</th>';
			if (!$cs_col_points) $out .= '<th style="text-align:center">'.$cs_label_points.'</th></tr>';

			$out .= '</thead>'."\n";
	 	$out .= '<tbody>'."\n";
		}

		// im bundesligamodus werden die spieltage durch eine untertitelzeile getrennt
		// für das collapse/expand feature
		if (get_option("cs_modus")==2 && $bl_lastspieltag < $res->spieltag) {
			if ( $bl_lastspieltag !='')
				$out .= "</tbody>\n";
			$bl_idext = ($bl_currspieltag == $res->spieltag?"id='currspieltag'":"");
			$out .= "<tbody><tr><th style='text-align:left' colspan='7' ".$bl_idext.">".$res->spieltag.". Spieltag</th></tr>\n";
		}

		// start des spiels als unix timestamp
		$match_start = $res->origtime;
		// start des spiels in der browser timezone als unix timestamp
		$match_local_start = strtotime($res->origtime) + $timediff;
		// tooltip nur anzeigen, wenn die zeit unterschiedlich ist
		$match_tooltip = "";
		if ($timediff != 0 )
			$match_tooltip = "title='Spielbeginn (lokal):".strftime("%d.%m %H:%M", $match_local_start)."'";
		$out .= "<tr>";
		if ( get_option('cs_modus') == 1 and (!isset($cs_col_group) or !$cs_col_group)) {
			if (get_option('cs_hovertable') == 1 and $res->round == "V") {
				$hid = "cs_hovertable_" . $hovertable_count;
				$hovertable_count++;
				$hlink = plugins_url('cs_groupstats.php',__FILE__)."?groupid=".($res->round == "V" ? $res->groupid : $res->mid);
				$out .= "<td style='text-align:center'><div><a href='$hlink' id='$hid'  >".($res->round == "V" ? $res->groupid : $res->mid)."</a></div></td>";
			} else
				$out .= "<td style='text-align:center'>".($res->round == "V" ? $res->groupid : $res->mid)."</td>";
		}
		if (!$cs_col_icon1) {
			if ($res->icon1!="")
				$out .= "<td style='text-align:center'><img class='csicon' alt='icon1' src='".$iconpath . $res->icon1."' /></td>";
			else
				$out .= "<td>&nbsp;</td>";
		}
		if (!$cs_col_match) {
			$out .= "<td style='text-align:center'>";
			if (get_option('cs_hovertable') == 1) { 
				$hid = "cs_hovertable_" . $hovertable_count;
				$hovertable_count++;
				$hlink = plugins_url('cs_matchstats.php',__FILE__)."?teamid=".$res->tid1;
				$out .= "<a href='$hlink' id='$hid'  >".($res->round=='V' ? $res->team1:team2text($res->team1)) . "</a> - ";
			} else
				$out .= ($res->round=='V' ? $res->team1:team2text($res->team1)) . " - ";
			
			if (get_option('cs_hovertable') == 1) {
				$hid = "cs_hovertable_" . $hovertable_count;
				$hovertable_count++;
				$hlink = plugins_url('cs_matchstats.php',__FILE__)."?teamid=".$res->tid2;
				$out .= "<a href='$hlink' id='$hid'  >".($res->round=='V' ? $res->team2:team2text($res->team2)) . "</a></td>";
			} else
				$out .= ($res->round=='V' ? $res->team2:team2text($res->team2)) . "</td>";
		}
		if (!$cs_col_icon2) {
			if ($res->icon2!="" and !$cs_col_icon2)
				$out .= "<td style='text-align:center'><img class='csicon' alt='icon2' src='".$iconpath . $res->icon2."' /></td>";
			else
				$out .= "<td>&nbsp;</td>";
		}
		if (!$cs_col_location) $out .= "<td style='text-align:center'>".$res->location."</td>";
		if (!$cs_col_time)     $out .= "<td style='text-align:center' ".$match_tooltip." >".$res->matchtime."</td>";
		$out .= "<td style='text-align:center'>";

		
		// fehlerklasse setzen, wenn erforderlich
		if (array_key_exists('gt1_'.$res->mid,$errlist)) {
			$errclass = " cs_inputerror ";
		} else {
			$errclass="";
		}
		
		$leftsidetipp = (isset($_POST['gt1_'.$res->mid]) && $_POST['gt1_'.$res->mid]!=-1 || array_key_exists('gt1_'.$res->mid,$errlist)?$_POST['gt1_'.$res->mid]:"");
		if ($res->result1!=-1 or $blog_now > $match_start or
				($res->round=='V' and get_option('cs_lock_round1'))) {
			//$out .= $_POST['gt1_'.$res->mid]." : ";
			$out .= $leftsidetipp." : ";
		} else {
			//$out .= "<input class='cs_entry $errclass' name='gt1_".$res->mid."' id='gt1_".$res->mid."' type='text' value='".(isset($_POST['gt1_'.$res->mid])?$_POST['gt1_'.$res->mid]:"")."' size='1' maxlength='2' />";
			$out .= "<input class='cs_entry $errclass' name='gt1_".$res->mid."' id='gt1_".$res->mid."' type='text' value='".$leftsidetipp."' size='1' maxlength='2' />";
		}
		// fehlerklasse setzen, wenn erforderlich
		if (array_key_exists('gt2_'.$res->mid,$errlist) or array_key_exists('gt1_'.$res->mid,$errlist))
			$errclass = " cs_inputerror ";
		else
			$errclass="";

		$rightsidetipp = (isset($_POST['gt2_'.$res->mid])  && $_POST['gt2_'.$res->mid]!=-1 || array_key_exists('gt2_'.$res->mid,$errlist)?$_POST['gt2_'.$res->mid]:"");
		if ($res->result2 != -1 or $blog_now > $match_start or
				($res->round=='V' and get_option('cs_lock_round1'))) {
			//$out .= $_POST['gt2_'.$res->mid];
			$out .= $rightsidetipp;
		} else {
			//$out .= " : <input class='cs_entry $errclass' name='gt2_".$res->mid."' id='gt2_".$res->mid."' type='text' value='".(isset($_POST['gt2_'.$res->mid])?$_POST['gt2_'.$res->mid]:"")."' size='1' maxlength='2' />";
			$out .= " : <input class='cs_entry $errclass' name='gt2_".$res->mid."' id='gt2_".$res->mid."' type='text' value='".$rightsidetipp."' size='1' maxlength='2' />";
		}
		$out .= "<br />";

		// der admin darf ergebnisse erfassen, alle anderen duerfen sie nur sehen
		if ( $is_admin ) {
			// fehlerklasse und wert setzen
			if (array_key_exists('rt1_'.$res->mid,$errlist)) {
				$errclass = " cs_inputerror ";
				$rt1_value = $_POST['rt1_'.$res->mid]; // alten eingabewert anzeigen
			} else {
				$errclass="";
				$rt1_value = ($res->result1==-1 ? "-" : $res->result1);
			}
			$out .= "<input class='cs_entry $errclass' name='rt1_".$res->mid."' id='rt1_".$res->mid."' type='text' size='1' maxlength='2' value='$rt1_value' /> : ";

			// fehlerklasse setzen, wenn erforderlich
			if (array_key_exists('rt2_'.$res->mid,$errlist)) {
				$errclass = " cs_inputerror ";
				$rt2_value = $_POST['rt2_'.$res->mid]; // alten eingabewert anzeigen
			} else {
				$errclass="";
				$rt2_value = ($res->result2==-1 ? "-" : $res->result2);
			}
			$out .= "<input class='cs_entry $errclass' name='rt2_".$res->mid."' id='rt2_".$res->mid."' type='text' size='1' maxlength='2' value='$rt2_value' />";
				
			$out .= "</td>";
		} else
			$out .= ($res->result1==-1 ? "-" : $res->result1) . ":" . ($res->result2==-1 ? "-" : $res->result2) . "</td>";

		if ($cs_goalsum > -1 and $cs_goalsum_auto==0) {			
			$gt3_value = $_POST['gt3_'.$res->mid];
			if (array_key_exists('gt3_'.$res->mid,$errlist)) {
				$errclass = " cs_inputerror ";
			} else {
				$errclass="";
			}
			
			if ($res->result2 != -1 or $blog_now > $match_start)
				$out .= "<td>".$_POST['gt3_'.$res->mid]."</td>";
			else
				$out .= "<td><input class='cs_entry $errclass' name='gt3_".$res->mid."' id='gt3_".$res->mid."' type='text' size='1' maxlength='2' value='$gt3_value' /></td> ";
		}
		
		
		$out .= "<td style='text-align:center'>".(isset($_POST['pt_'.$res->mid]) && $_POST['pt_'.$res->mid] != -1 ? $_POST['pt_'.$res->mid]:"-" )."</td>";
		$out .= "</tr>\n";

		// gruppenwechsel versorgen
		$lastmatchround  = $res->round;
		$bl_lastspieltag = $res->spieltag;
	}
	$out .= '</tbody></table>'."\n&nbsp;";
	$out .="</form>";
	//
	// ausgabe javascript fuer hovertable funktion
	//
	if (get_option("cs_hovertable") == 1) {
		$out .= "<script type='text/javascript'>";
		$out .= 'jQuery(document).ready(function() {';
		for ($i=0;$i<$hovertable_count;$i++) {
			$out .= 'jQuery("#cs_hovertable_'.$i.'").tooltip({cssClass: "tooltip-red"});'."\n";
		}
		$out .= "});</script>";
	}
	
	return $out;
}


?>