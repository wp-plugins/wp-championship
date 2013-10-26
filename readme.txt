=== wp-championship ===
Contributors: tuxlog
Donate link: http://www.tuxlog.de/
Tags: championship,guessing, game, soccer, world, 2012, wordpress, plugin
Requires at least: 2.7
Tested up to: 3.6.1
Stable tag: 4.1

wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer 

== Description ==
wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer 

Features:

 + define number of groups, and points given to the winner, looser of each match
 + define teams and a team specific icon
 + define matches finalround and pre-final round
 + for each user you can set a substitute
 + sends mails about current game status (if wanted)
 + define game admin to edit match results   
 + shows different stats for admin and users
 + sql for the EM2008 championship in switzerland and austria
 + sql for the WM2010 championship in south-africa
 + sql for the EM2012 championship in ukraine/polen
 + sql for German Bundesliga 2012/2013
 + sql for German Bundesliga 2013/2014
 
Credits:
Thanks go to all who support this plugin, with  hints and suggestions for improv
ment and especially to Andy Chapman for doing a lot of tests



== Installation ==
	
	1.  Upload to your plugins folder, usually
	    `wp-content/plugins/`, keeping the directory structure intact
	    (i.e. `wp-championship.php` should end up in
	    `wp-content/plugins/wp-championship/`).

	1.  Activate the plugin on the plugin screen.

	1.  Visit the configuration page (wp-championship) to
            configure your guessing game

	1.  Optional: load teams andmatches into your database (e.g. using phpmyadmin). SQL for the WM2010 can be found in the sql subdirectory

	1.  Optional Update to v1.6, v2.5, v2.9: Please remember to deactivate and activate the plugin once for database updates

== Frequently Asked Questions ==
= Where can I get further information about the plugin? =

There are several resources to visit:

* [The german wp-championship v1.5. post][german15] 
* [Information up to v1.4 of wp-championship][german14]
* [A quick reference for wp-championship][wpcsref] 


[german15]: http://www.tuxlog.de/wordpress/2010/wp-championship-v1-5-fur-die-wm2010/ "German wp-championship post v1.5"
[german14]: http://www.tuxlog.de/wordpress/2008/wp-championship/ "wp-championship post up to v1.4"
[wpcsref]:  http://www.tuxlog.de/wordpress/2013/wp-championship-quickreference-english/ "quick reference for wp-championship"

== Screenshots ==
1. wp-championship stats
2. wp-championship tipp dialog

== Changelog ==

= 2013-10-23 v4.1 =
* fixed adding a match to the final round
* fixed cs-stats5 results were shifted when player did not enter a tip for every match
* removed timezone calc for client side due to geoip service was shut down
* added english quickreference guide

= 2013-08-04 v4.0 =
* fixed setup because default admin entry was not correct
* added icons and sql for Bundesliga 2013/2014

= 2012-11-24 v3.9 =
* add the possibility to give each team a penalty in points

= 2012-10-09 v3.8 =
* load javascript in backend only on wp-championship pages
* add cs-stats7 Guesser of the month stats
* changed enqueue script to proper use with plugins_url

= 2012-07-06 v3.7 =
* added new option for goalsum tip you only hit if your tipp is euqal goalsum not equal or greater
* added sql and icons for Bundesliga 2012/2013
* added columnd points to stats4
* added stats6 to show all matches from one team
* extended tipp-aids in tippdialog with stats6 when hovering over the teamname

= 2012-06-25 v3.6 =
* fixed the widget display name 
* extended goal sum tipp to accept a sum of 0
* fixed goalsum tipp could only be entered for all games at once
* points for goalsum tipp where assigned when tipp was >= result, changed this to =
* tipps from other players are now shown directly after the match has started in stats4
* extended selection filter to select from match and player 
* fixed a bug in team stats, count of wins and loss

= 2012-06-10 v3.5 =
* fixed substitue list contained all usernames not only the players in tip dialog
* fixed wrong trend was shown at first result
* fixed a numbering issue with the widget
* fixed display average in the widget
* trend was lost when admin entered tips but no results
* mailreminder was not send due to a typo in date variable

= 2012-06-03 v3.4 =
* fixed problem with table sorting option
* fixed some typos in readme.txt

= 2012-06-01 v3.3 =
* fixed xmlrpc statistic 7 (all tables were displayed independent of the parameter)
* fixed first tipp when user was not in cs_users


= 2012-05-20 v3.2 =
* added hover table feature to tipp dialog (shows an ajax like group table when hovering over the group id)
* fixed uninitialized value fpr cs_sort_tipp

= 2012-05-18 v3.1 =
* fixed deprecated use of user_level (leads to many many php notices)
* fixed problem with entering a wrong tipp as first tipp (all tips were set to -1)
* fixed html error in confirmation mail
* removed deprecated parameter from add_option in setup.php (leads to many php notices during activation)
* clean up some php notices on the admin dialogs

= 2012-05-12 v3.0 =
* fixed button style in admin dialog
* added XMLRPC interface to wp-championship for use with smartphone apps
* fixed width of menu entry in wp admin menu
* fixed widget layout on twentyeleven
* adopt default css to twentyeleven
* added demo mode (activate in wp-championship.php)
* fixed an incompatibility with wordpress MU and register_activation/deactivation
* adopted to HTML5 for 3.3 compatibility
* added SQL for EM2012 (folder sql)
* fixed heading for location in tipp table
* fixed confirmation mail (teams and time were not displayed, for finals set team to n/a)
* fixed wrong message (Mail could not be sent...) when deactivating the mailreceipt
* added auto add feature to automatically add new users to guessing game (see admin dialog to activate)
* fixed mailservice receipt was only send for admins
* cleanup php warning (a bit)
* cleanup html validity
* fixed stats4 when all players were selected only part of the tipps was shown


= 2011-08-26 v2.9=
* added the possibility to select all users in cs-stats4
* fixed problem adding final match with bundesliga mode
* fixed problem with jquery 1.4.4 and trigger in wordpress 3.1
* added cs-stats5 an compact overview over one day
* fixed that field spieltag was deleted when editing match
* added shortname for teams for use in reports and stats
* added widget for display the current ranking
* fixed error in winner calculation in Bundesliga-mode
* added confirmation mail feature
* fix ranking when matches were deleted and old tipps were still present
* added group feature for players (every user can be member of a tippgroup, stats can be calculated only for one group or for all)
* fixed an incompatibility with FF6 and Wordpress adminbar

= 2010-11-08 v2.8 =
* fixed debug info dump
* fixed stats to work with non standard wp installations
* fixed pie chart calculating correct percentage
* added charset /collation to setup
* fixed wrong collation in ajax data transfer

= 2010-10-31 v2.7 =
* added label configuration admin dialog 
* conserve wp-championship.css during autoupdate
* added basic statistics (insert [cs-stats1], [cs-stats2], [cs-stats3] or [cs-stats4] into a page or post) see documentation for further information

= 2010-08-20 v2.6 =
* fixed collision with older wp-championship version and buddypress

= 2010-08-06 v2.5 =
* removed invalid table class from default css file
* added collapse/expand per day for Bundesligamodus
* added admin switch to lock round1-tipps generally
* added spieltag attribute to matches for mapping in liga-mode
* added trend barometer for ranking table and mailservice
* only output finalround on stats page when final matches exists

= 2010-07-09 v2.4 =
* fixed missing calculation of next match in finalround (match for third place) again :-(

= 2010-07-07 v2.3 =
* fixed invalid xhtml in tipp-dialog
* fixed missing calculation of next match in finalround (match for third place)

= 2010-06-20 v2.2 =
* fixed invalid xhtml tableheader in ranking mail
* fixed sql for result calculation in group tables

= 2010-06-11 v2.1 =
* fixed some translation tags
* fixed warning message for browser timezone when ip is localhost
* fixed WM2010 location of match #56 from Johannisburg to Kapstadt
* fix saving other tipps during a game was not possible

= 2010-06-01 v2.0 =
* fixed output of nonce field, was probably a collision with other plugins 
* fixed sort order on tipp dialog for finalround
* fixed points calculation for oneside tipp in conjunction with tendency tipp(> instead of >=)
* fixed goalsum tipp for tipps with no points yet
* fixed link to substitue was not correct with all permalink settings in wordpress

= 2010-05-19 v1.9 =
* fixed missing cr in mail header (which causes some mailservers to deny mailtransfer)
* fixed check for championtipp against current time, used server time instead of blog-time. this lead to problems if server and blog time are in different timezones

= 2010-05-09 v1.8 =
* fixed warning message when allow_url_fopen was Off

= 2010-05-08 v1.7 =
* added option for oneside tipp only hits if tendency is correct
* added auto goalsum tipp (tipp will be calcualted from result tipp (sum of goals)

= 2010-04-27 v1.6 =
* fixed layout in readme.txt
* added screenshots
* fixed, championtipp was not updated when no other field was changed (tippdialog)
* added error message when championtipp should be changed after first match start
* added feature to gain points for a only on one side correct tip
* added feature to gain points for a sum of goals/points during for each match
* fixed save user data when using substitutes
* added feature to gain points if one side of the tipp is exactly correct
* fixed button class on tipp page
* added switch to enable/disable floating link

= 2010-04-15 v1.5 =
* adopt to wordpress 2.9.2
* added sql for WM2010 (teams, matches)
* added mail reminder for upcoming matches
* added italian translation (thanks to Davide :-) )
* added sortable tables to tipp page
* added foldable tables to results page
* added tooltip to matchtime column showing the starttime based on browsers timezone
* added animated flags (thanks to Andy)
* extended input checking on tipp page
* mark not accepted values in red (tipp page)
* added auto-floating "Top of page" link
* make sure only one admin entry is added by default
* added menu icon - the worldcup :-)

= 2009-03-29 v1.4 =
* adding a first draft of english translation

= 2008-08-02 v1.3 =
* corrected a bit of incorrect xhtml
* fixed wrong timestamp for championtime
* check tipptime for championtime in case of injection 
* mark admin as tippspiel admin during install
* add switch to disable substitute feature
* added nonce check
* added championship modus for the german bundesliga
* extended classification boards with some stats

= 2008-06-18 v1.2 =
* added the possibility to define mixed finalround matches (from groups and match)
* fixed a problem to store user settings when no champion tipp was given
* fixed an error when using a substitute
* added separate trigger for recalculating points and finals in admin dialog

= 2008-06-16 v1.1 =
* fixed some spelling mistakes
* fixed xhtml for tipp page
* fixed problem with saving user options
* fixed html in admin dialog
* fixed sql error when updating finals
* corrected type error in team dialog

= 2008-06-11 v1.0 =
* send mails only when admin is entering results (not when admin entered tipps)
* added mailservice trigger in admin dialog
* corrected order in group classification
* consider wordpress timezone for time checking
* store only new or changed tipps
* corrected pulldown menu for champion tipp in user dialog
* fixed points calculation for tendency and tied games (when no tipp was entered points for tied games were added)

= 2008-06-01 v0.9 =
* fixed mistake in em2008.sql
* fixed problem creating matches

= 2008-05-31 v0.8=
* read correct wordpress table prefix
* added possibility to remove wp-championship db tables
* the finals will now be calculated each time a results is changed
* you can overrule pre-elimination classification manual by setting the standing in the match dialog
* when creating a new user in user dialog check if user allready exists

= 2008-05-27 v0.7=
* extended data validation for input fields (tipps and results)
* prepare for translation, added .pot file
* corrected spelling errors

= 2008-05-22 v0.6=
* initial alpha release
