=== wp-championship ===
Contributors: tuxlog
Donate link: http://www.tuxlog.de/
Tags: championship,guessing, game, soccer, world, 2010, wordpress, plugin
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 2.1

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
 + shows some stats for admin and users
 + sql for the EM2008 championship in switzerland and austria
 + sql for the WM2010 championship in south-africa

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

	1.  Optional Update to v1.6: Please remember to deactivate and activate the plugin once for database updates

== Frequently Asked Questions ==
= Where can I get further information about the plugin? =

There are several resources to visit:

* [The german wp-championship v1.5. post][german15] 
* [Information up to v1.4 of wp-championship][german14]
* [A quick reference for wp-championship][wpcsref] 


[german15]: http://www.tuxlog.de/wordpress/2010/wp-championship-v1-5-fur-die-wm2010/ "German wp-championship post v1.5"
[german14]: http://www.tuxlog.de/wordpress/2008/wp-championship/ "wp-championship post up to v1.4"
[wpcsref]:  http://www.tuxlog.de/wordpress/2010/wp-championship-v1-5-quickreferenz/ "quick reference for wp-championship"

== Screenshots ==
1. wp-championship stats
2. wp-championship tipp dialog

== Changelog ==

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
