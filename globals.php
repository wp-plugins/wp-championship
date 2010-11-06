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

$wpdb =& $GLOBALS['wpdb'];

// table prefix - change here once to get a different one
$cs_table_prefix="cs_";

// define tablenames as globals
$cs_match = $cs_table_prefix . "match";
$cs_team  = $cs_table_prefix . "team";
$cs_users = $cs_table_prefix . "users";
$cs_tipp  = $cs_table_prefix . "tipp";
$wp_users = $wpdb->prefix . "users"; 
?>
