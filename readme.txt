=== wp-championship ===
Tags: championship,
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 1.0

wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer 

== Description ==
/*
Plugin Name: wp-championship
Plugin URI: http://www.tuxlog.de
Description:  wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer
Version: 1.0
Author: Hans Matzen <webmaster at tuxlog dot de>
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

== credits ==
   Andy Chapman			testing a lot

== features ==
   + define number of groups, and points given to the winner, looser 
     of each match
   + define teams and a team specific icon
   + define matches finalround and pre-final round
   + for each user you can set a substitute
   + sends mails about current game status (if wanted)
   + define game admin to edit match results   
   + shows some stats for admin and users
   + sql for the EM2008 championship in sitzerland and austria

== requirements ==
   + PHP >=4.3
   + Wordpress >2.5.x

== installation ==
	
	1.  Upload to your plugins folder, usually
	    `wp-content/plugins/`, keeping the directory structure intact
	    (i.e. `wp-championship.php` should end up in
	    `wp-content/plugins/wp-championship/`).

	2.  Activate the plugin on the plugin screen.

	3.  Visit the configuration page (wp-championship) to
            configure your guessing game

== history ==
2008-05-22 v0.6	initial alpha release

2008-05-27 v0.7 extended data validation for input fields (tipps and results), 
	        prepare for translation, added .pot file, 
		corrected spelling errors

2008-05-31 v0.8 read correct wordpress table prefix,
	   	added possibility to remove wp-championship db tables,
		the finals will now be calculated each time a results is changed,
		you can overrule pre-elimination classification manual by setting
		the standing in the match dialog,
		when creating a new user in user dialog check if user allready exists
2008-06-01 v0.9 fixed mistake in em2008.sql, fixed problem creating matches

2008-06-11 v1.0	send mails only when admin is entering results (not when 
		admin entered tipps), added mailservice trigger in admin 
		dialog, corrected order in group classification, consider 
		wordpress timezone for time checking, store only new or 
		changed tipps, corrected pulldown menu for champion tipp 
		in user dialog, fixed points calculation for tendency and 
		tied games (when no tipp was entered points for tied games 
		were added)
