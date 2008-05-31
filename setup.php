<?php
/* This file is part of the wp-championship plugin for wordpress */

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

// this function installs the wp-championship database tables and
// sets up default values and options
function wp_championship_install()
{
  include("globals.php");
  $wpdb =& $GLOBALS['wpdb'];

  $sql = 'SHOW TABLES LIKE \''.$cs_table_prefix.'%\'';
  $results = $wpdb->query($sql);
  
  if ($results == 0)
    {
      // create tables
      // team table
      $sql = "create table ".$cs_table_prefix."team 
          (
            tid integer not null auto_increment,
            name varchar(40) NOT NULL,
            icon varchar(40) NOT NULL,
            groupid varchar(2) NOT NULL,
            qualified boolean NOT NULL,
            primary key(tid)
          )";

      $results = $wpdb->query($sql);
     
      // match table
      $sql = "create table ".$cs_table_prefix."match 
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
          )";

      $results = $wpdb->query($sql);
   
      // tipp table
      $sql = "create table ".$cs_table_prefix."tipp 
          (
            userid integer not null,
            mid integer not null,
            result1 integer not null, 
            result2 integer not null,
            tipptime datetime not null,
            points integer not null,
            primary key(userid,mid)
          )";

      $results = $wpdb->query($sql);

      // users table
      $sql = "create table ".$cs_table_prefix."users 
          (
            userid integer not null,
            admin bool not null,
            mailservice bool not null,
            stellvertreter int not null,
            champion int NOT NULL,
            championtime datetime NOT NULL
          )";

      $results = $wpdb->query($sql);  
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
  
  
  // Optionen / Parameter

  // Option: Anzahl der Gruppen in der Vorrunde; Werte: 1-12; 
  // Gibt die Anzahl der Gruppen in der Vorrunde an. Default: 8
  $cs_groups=get_option("cs_groups");
  if ($cs_groups == "") {
    $cs_groups="8";
    add_option("cs_groups",$cs_groups,"Number of groups","yes");
  };
  

  // Option: Punkte für Sieger, Wert: ganzzahlig numerisch, Default: 3
  $cs_pts_winner=get_option("cs_pts_winner");
  if ($cs_pts_winner == "") {
    $cs_pts_winner="3";
    add_option("cs_pts_winner",$cs_pts_winner,"Points for winning team","yes");
  };

  // Option: Punkte für Verlierer, Wert: ganzzahlig numerisch, Default: 0
  $cs_pts_looser=get_option("cs_pts_looser");
  if ($cs_pts_looser == "") {
    $cs_pts_looser="0";
    add_option("cs_pts_looser",$cs_pts_looser,"Points for winning team","yes");
  };  
  
  // Option: Punkte für Unentschieden , Wert: ganzzahlig numerisch, Default: 1
  $cs_pts_deuce=get_option("cs_pts_deuce");
  if ($cs_pts_deuce == "") {
    $cs_pts_deuce="1";
    add_option("cs_pts_deuce",$cs_pts_deuce,"Points for deuce","yes");
  };
  
  // Option: Anzahl der Teams in der Finalrunde; Werte: 16, 8, 4; Default: 16; 
  // Wenn die Option auf den Wert 16 eingestellt wird, startet die Finalrunde 
  // im Achtelfinale, bei 8 im Viertelfinale und bei 4 im Halbfinale
  $cs_final_teams=get_option("cs_final_teams");
  if ($cs_final_teams == "") {
    $cs_final_teams="16";
    add_option("cs_final_teams",$cs_final_teams,"Number of teams in alround","yes");
  };

  // Option: Anzahl der Teams pro Gruppe, die maximal in die 
  // Finalrunde kommen, Default:2
  $cs_group_teams= get_option("cs_group_teams");
  if ($cs_group_teams == "") {
    $cs_group_teams="2";
    add_option("cs_group_teams",$cs_group_teams,"Number of teams per group joining the finalround","yes");
  };
  
  // Option: Punkte für richtigen Tipp, Wert: ganzzahlig numerisch, Default:3
  $cs_pts_tipp= get_option("cs_pts_tipp");
  if ($cs_pts_tipp == "") {
    $cs_pts_tipp="1";
    add_option("cs_pts_tipp",$cs_pts_tipp,"Points for wright tipp","yes");
  };
  
  // Option: Punkte für richtige Tendenz, Wert: ganzzahlig numerisch, Default:1
  $cs_pts_tendency=get_option("cs_pts_tendency");
  if ($cs_pts_tendency == "") {
    $cs_pts_tendency="1";
    add_option("cs_pts_tendency",$cs_pts_tendency,"Points wright tendency","yes");
  };

  // Option: Punkte für richtige Tendenz und Tordifferenz, Wert: ganzzahlig 
  // numerisch, Default:5  
  $cs_pts_supertipp=get_option("cs_pts_supertipp");
  if ($cs_pts_supertipp == "") {
    $cs_pts_supertipp="5";
    add_option("cs_pts_supertipp",$cs_pts_supertipp,"Points for wright supertipp","yes");
  };

  // Option: Punkte für richtigen Champion, Wert: ganzzahlig numerisch, 
  // Default: 20
   $cs_pts_champ=get_option("cs_pts_champ");
  if ($cs_pts_champ == "") {
    $cs_pts_champ="1";
    add_option("cs_pts_champ",$cs_pts_champ,"Points for wright champion tipp","yes");
  };
}

function wp_championship_deinstall()
{
  include("globals.php");
  $wpdb =& $GLOBALS['wpdb'];

  // to prevent misuse :-)
  return;

  $sql = 'SHOW TABLES LIKE \''.$cs_table_prefix.'%\'';
  $results = $wpdb->query($sql);
  
  if ($results != 0)
    {
      // drop tables
      // team table
      $sql = "drop table ".$cs_table_prefix."team;";
      $results = $wpdb->query($sql);
      // match table
      $sql = "drop table ".$cs_table_prefix."match;";
      $results = $wpdb->query($sql);
      // tipp table
      $sql = "drop table ".$cs_table_prefix."tipp;";
      $results = $wpdb->query($sql); 
      // users table
      $sql = "drop table ".$cs_table_prefix."users;";
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
}


?>