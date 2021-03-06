<?php
/* This file is part of the wp-championship plugin for wordpress */

/*  Copyright 2007-2011  Hans Matzen  (email : webmaster at tuxlog.de)

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

// this function installs the wp-championship database tables and
// sets up default values and options
function wp_championship_install()
{
  include("globals.php");
  global $wpdb;
  $wpdb->show_errors(false);
  
  // add charset & collate like wp db class
  $charset_collate = '';
  
  if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
    if ( ! empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if ( ! empty($wpdb->collate) )
      $charset_collate .= " COLLATE $wpdb->collate";
  }
  
  $sql = "SHOW TABLES LIKE '$cs_team'";
  $results = ($wpdb->get_var($sql) == $cs_team); 
  
  if ($results == 0)
    {
      // create tables
      // team table
      $sql = "create table ".$cs_team." 
          (
            tid integer not null auto_increment,
            name varchar(40) NOT NULL,
            icon varchar(40) NOT NULL,
            groupid varchar(2) NOT NULL,
            qualified boolean NOT NULL,
            penalty integer NOT NULL,
            primary key(tid)
          ) $charset_collate;";
      
      $results = $wpdb->query($sql);
    }

  $sql = "SHOW TABLES LIKE '$cs_match'";
  $results = ($wpdb->get_var($sql) == $cs_match); 

  if ($results == 0)
    {   
      // match table
      $sql = "create table ".$cs_match." 
          (
            mid integer not null auto_increment,
            round char(1),
            tid1 varchar(8) not null,
            tid2 varchar(8) not null,
            location varchar(80) NOT NULL,
            matchtime datetime NOT NULL,
            result1 integer not null, 
            result2 integer not null,
            winner bool NOT NULL,
            primary key(mid)
          ) $charset_collate;";
      
      $results = $wpdb->query($sql);
    }

  $sql = "SHOW TABLES LIKE '$cs_tipp'";
  $results = ($wpdb->get_var($sql) == $cs_tipp); 
  
  if ($results == 0)
    {  
      // tipp table
      $sql = "create table ".$cs_tipp." 
          (
            userid integer not null,
            mid integer not null,
            result1 integer not null, 
            result2 integer not null,
            tipptime datetime not null,
            points integer not null,
            primary key(userid,mid)
          ) $charset_collate;";
      
      $results = $wpdb->query($sql);
    }

  $sql = "SHOW TABLES LIKE '$cs_users'";
  $results = ($wpdb->get_var($sql) == $cs_users); 

  if ($results == 0)
    {
      // users table
      $sql = "create table ".$cs_users." 
          (
            userid integer not null,
            admin bool not null,
            mailservice bool not null,
            stellvertreter int not null,
            champion int NOT NULL,
            championtime datetime NOT NULL
          ) $charset_collate;";
      
      $results = $wpdb->query($sql); 
    

      // add admin as tippspiel admin if necessary
      $sql = "select count(*) as c from ".$cs_users." where userid=1;";
      $resadmin = $wpdb->get_row($sql); 
      if ($resadmin->c == 0) {
	$sql = "insert into ".$cs_users." values
          ( 1, 1, 0, 0, 0, '0000-00-00 00:00:00');";
	$results = $wpdb->query($sql);  
      }
    }

  // -----------------------------------------------------------------
  // U P D A T E - table structure v0.8
  // -----------------------------------------------------------------
  $sql="select ptid1 from $cs_match;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
    // add columns for pseudo teamids
    $sql="alter table $cs_match add column ptid1 int NOT NULL after winner";
    $results = $wpdb->query($sql);

    $sql="alter table $cs_match add column ptid2 int NOT NULL after ptid1";
    $results = $wpdb->query($sql);

    $sql="update $cs_match inner join $cs_team on tid1=tid set ptid1=tid1 where name like '#%';";
    $results = $wpdb->query($sql);
    $sql="update $cs_match inner join $cs_team on tid2=tid set ptid2=tid2 where name like '#%';";
    $results = $wpdb->query($sql);
    
  }
  
  // -----------------------------------------------------------------
  // U P D A T E - table structure v1.6
  // -----------------------------------------------------------------
  $sql="select result3 from $cs_tipp;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for point sum tip
      $sql="alter table $cs_tipp add column result3 int NOT NULL after result2";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_tipp set result3 = -1;";
      $results = $wpdb->query($sql);
  }
  
  // -----------------------------------------------------------------
  // U P D A T E - table structure v2.5
  // -----------------------------------------------------------------
  $sql="select spieltag from $cs_match;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for spieltag
      $sql="alter table $cs_match add column spieltag int NOT NULL after round";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_match set spieltag=-1;";
      $results = $wpdb->query($sql);
  } 

  $sql="select rang from $cs_users;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for rang = platzierung
      $sql="alter table $cs_users add column rang int NOT NULL after championtime";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_users set rang=-1;";
      $results = $wpdb->query($sql);
  }
 
  // -----------------------------------------------------------------
  // U P D A T E - table structure v2.9
  // -----------------------------------------------------------------
  $sql="select shortname from $cs_team;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for shortname
      $sql="alter table $cs_team add column shortname varchar(5) NOT NULL after name";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_team set shortname=substring(name,1,5);";
      $results = $wpdb->query($sql);
  } 

  $sql="select mailreceipt from $cs_users;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for mailreceipt
      $sql="alter table $cs_users add column mailreceipt bool NOT NULL after mailservice";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_users set mailreceipt=0;";
      $results = $wpdb->query($sql);
  } 

  $sql="select tippgroup from $cs_users;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for tippgroup
      $sql="alter table $cs_users add column tippgroup varchar(20) NOT NULL after rang";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_users set tippgroup='';";
      $results = $wpdb->query($sql);
  } 
  
  // -----------------------------------------------------------------
  // U P D A T E - table structure v3.9
  // -----------------------------------------------------------------
  $sql="select penalty from $cs_team;";
  $results = $wpdb->query($sql);  
  
  if ($results == 0) {
      // add columns for penalty
      $sql="alter table $cs_team add column penalty integer NOT NULL after qualified";
      $results = $wpdb->query($sql);
      
      $sql="update $cs_team set penalty=0;";
      $results = $wpdb->query($sql);
  } 
  
  //
  // Optionen / Parameter
  //

  // Option: Anzahl der Gruppen in der Vorrunde; Werte: 1-12; 
  // Gibt die Anzahl der Gruppen in der Vorrunde an. Default: 8
  $cs_groups=get_option("cs_groups");
  if ($cs_groups == "") {
    $cs_groups="8";
    add_option("cs_groups",$cs_groups,"","yes");
  };
  

  // Option: Punkte für Sieger, Wert: ganzzahlig numerisch, Default: 3
  $cs_pts_winner=get_option("cs_pts_winner");
  if ($cs_pts_winner == "") {
    $cs_pts_winner="3";
    add_option("cs_pts_winner",$cs_pts_winner,"","yes");
  };

  // Option: Punkte für Verlierer, Wert: ganzzahlig numerisch, Default: 0
  $cs_pts_looser=get_option("cs_pts_looser");
  if ($cs_pts_looser == "") {
    $cs_pts_looser="0";
    add_option("cs_pts_looser",$cs_pts_looser,"","yes");
  };  
  
  // Option: Punkte für Unentschieden , Wert: ganzzahlig numerisch, Default: 1
  $cs_pts_deuce=get_option("cs_pts_deuce");
  if ($cs_pts_deuce == "") {
    $cs_pts_deuce="1";
    add_option("cs_pts_deuce",$cs_pts_deuce,"","yes");
  };
  
  // Option: Anzahl der Teams in der Finalrunde; Werte: 16, 8, 4; Default: 16; 
  // Wenn die Option auf den Wert 16 eingestellt wird, startet die Finalrunde 
  // im Achtelfinale, bei 8 im Viertelfinale und bei 4 im Halbfinale
  $cs_final_teams=get_option("cs_final_teams");
  if ($cs_final_teams == "") {
    $cs_final_teams="16";
    add_option("cs_final_teams",$cs_final_teams,"","yes");
  };

  // Option: Anzahl der Teams pro Gruppe, die maximal in die 
  // Finalrunde kommen, Default:2
  $cs_group_teams= get_option("cs_group_teams");
  if ($cs_group_teams == "") {
    $cs_group_teams="2";
    add_option("cs_group_teams",$cs_group_teams,"","yes");
  };
  
  // Option: Punkte für richtigen Tipp, Wert: ganzzahlig numerisch, Default:3
  $cs_pts_tipp= get_option("cs_pts_tipp");
  if ($cs_pts_tipp == "") {
    $cs_pts_tipp="1";
    add_option("cs_pts_tipp",$cs_pts_tipp,"","yes");
  };
  
  // Option: Punkte für richtige Tendenz, Wert: ganzzahlig numerisch, Default:1
  $cs_pts_tendency=get_option("cs_pts_tendency");
  if ($cs_pts_tendency == "") {
    $cs_pts_tendency="1";
    add_option("cs_pts_tendency",$cs_pts_tendency,"","yes");
  };

  // Option: Punkte für richtige Tendenz und Tordifferenz, Wert: ganzzahlig 
  // numerisch, Default:5  
  $cs_pts_supertipp=get_option("cs_pts_supertipp");
  if ($cs_pts_supertipp == "") {
    $cs_pts_supertipp="5";
    add_option("cs_pts_supertipp",$cs_pts_supertipp,"","yes");
  };

  // Option: Punkte für richtigen Champion, Wert: ganzzahlig numerisch, 
  // Default: 20
   $cs_pts_champ=get_option("cs_pts_champ");
  if ($cs_pts_champ == "") {
    $cs_pts_champ="1";
    add_option("cs_pts_champ",$cs_pts_champ,"","yes");
  };

  // Option: Punkte für einseitig richtigen Tipp, Wert: ganzzahlig numerisch, 
  // Default: 0
   $cs_pts_oneside=get_option("cs_pts_oneside");
  if ($cs_pts_oneside == "") {
    $cs_pts_oneside="0";
    add_option("cs_pts_oneside",$cs_pts_oneside,"","yes");
  };

  // Option: Schwellwert für Summer der Tore Tipp, Wert: ganzzahlig numerisch, 
  // Default: 0
   $cs_goalsum=get_option("cs_goalsum");
  if ($cs_goalsum == "") {
    $cs_goalsum="-1";
    add_option("cs_goalsum",$cs_goalsum,"","yes");
  };

  // Option: Punkte für Summe der Tore, Wert: ganzzahlig numerisch, 
  // Default: 0
   $cs_pts_goalsum=get_option("cs_pts_goalsum");
  if ($cs_pts_goalsum == "") {
    $cs_pts_goalsum="0";
    add_option("cs_pts_goalsum",$cs_pts_goalsum,"","yes");
  };

  // Option: Stellvertreterfunktion abstellen, Wert: bool, Default: 0
  $cs_stellv_schalter=get_option("cs_stellv_schalter");
  if ($cs_stellv_schalter == "") {
    $cs_stellv_schalter="1";
    add_option("cs_stellv_schalter",$cs_stellv_schalter,"","yes");
  }; 

  // Option: Turniermodus, Wert: int, Default: 1
  $cs_modus=get_option("cs_modus");
  if ($cs_modus == "") {
    $cs_modus="1";
    add_option("cs_modus",$cs_modus,"","yes");
  };

  // Option: Floating Link einschalten, Wert: bool, Default: 1
  $cs_floating_link=get_option("cs_floating_link");
  if ($cs_floating_link == "") {
    $cs_floating_link="1";
    add_option("cs_floating_link",$cs_floating_link,"","yes");
  }; 

  // Option: Vorrunden-Tipps sperren, Wert: bool, Default: 0
  $cs_lock_round1=get_option("cs_lock_round1");
  if ($cs_lock_round1 == "") {
      $cs_lock_round1="0";
      add_option("cs_lock_round1",$cs_lock_round1,"","yes");
  }; 
 
  // Option: Platzierungstrend berechnen, Wert: bool, Default: 1
  $cs_rank_trend=get_option("cs_rank_trend");
  if ($cs_rank_trend == "") {
      $cs_rank_trend="1";
      add_option("cs_rank_trend",$cs_rank_trend,"","yes");
  }; 
  
  wp_schedule_event(time(), 'hourly', 'cs_mailreminder');

}

function wp_championship_deinstall()
{
  include("globals.php");
  $wpdb =& $GLOBALS['wpdb'];

  // entferne reminder hook
  wp_clear_scheduled_hook('cs_mailreminder');

  // to prevent misuse :-)
  return;

  $sql = 'SHOW TABLES LIKE \''.$cs_table_prefix.'%\'';
  $results = $wpdb->query($sql);
  
  if ($results != 0)
    {
      // drop tables
      // team table
      $sql = "drop table ".$cs_team.";";
      $results = $wpdb->query($sql);
      // match table
      $sql = "drop table ".$cs_match.";";
      $results = $wpdb->query($sql);
      // tipp table
      $sql = "drop table ".$cs_tipp.";";
      $results = $wpdb->query($sql); 
      // users table
      $sql = "drop table ".$cs_users.";";
      $results = $wpdb->query($sql);
    } 
  
  // remove options from wp_options
  delete_option("cs_final_teams");
  delete_option("cs_groups");
  delete_option("cs_pts_champ");
  delete_option("cs_pts_deuce");
  delete_option("cs_pts_looser");
  delete_option("cs_pts_winner");
  delete_option("cs_pts_supertipp");
  delete_option("cs_pts_tipp"); 
  delete_option("cs_pts_tendency");
  delete_option("cs_stellv_schalter");
  delete_option("cs_modus");
  delete_option("cs_goalsum");
  delete_option("cs_pts_goalsum");
  delete_option("cs_pts_oneside"); 

  // options 
  $fieldnames = array ("cs_label_group", "cs_col_group", "cs_label_icon1", "cs_col_icon1", "cs_label_match",
		       "cs_col_match", "cs_label_icon2", "cs_col_icon2", "cs_label_location", "cs_col_location",
		       "cs_label_time", "cs_col_time", "cs_label_tip", "cs_col_tip", "cs_label_points",
		       "cs_col_points", "cs_label_place", "cs_col_place", "cs_label_player", "cs_col_player",
		       "cs_label_upoints", "cs_col_upoints", "cs_label_trend", "cs_label_steam",
		       "cs_col_steam", "cs_label_smatch", "cs_col_smatch", "cs_label_swin", "cs_col_swin", 
		       "cs_label_stie", "cs_col_stie", "cs_label_sloose", "cs_col_sloose", "cs_label_sgoal",
		       "cs_col_sgoal", "cs_label_spoint", "cs_col_spoint", "cs_tipp_sort");
  foreach($fieldnames as $fn)
      delete_option($fn);
}
?>