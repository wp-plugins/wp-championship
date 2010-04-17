=== wp-championship ===
Contributors: tuxlog
Donate link: http://www.tuxlog.de/
Tags: championship,guessing, game, soccer, world, 2010, wordpress,plugin
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 1.5

wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer 

== Description ==
wp-championship is a plugin for wordpress letting you play a guessing game of a tournament e.g. soccer 

Features:
* define number of groups, and points given to the winner, looser of each match
* define teams and a team specific icon
* define matches finalround and pre-final round
* for each user you can set a substitute
* sends mails about current game status (if wanted)
* define game admin to edit match results   
* shows some stats for admin and users
* sql for the EM2008 championship in switzerland and austria
* sql for the WM2010 championship in south-africa

Credits:
Thanks go to all who support this plugin, with  hints and suggestions for improv
ment and especially to Andy Chapman for doing a lot of tests



== Installation ==
	
	1.  Upload to your plugins folder, usually
	    `wp-content/plugins/`, keeping the directory structure intact
	    (i.e. `wp-championship.php` should end up in
	    `wp-content/plugins/wp-championship/`).

	2.  Activate the plugin on the plugin screen.

	3.  Visit the configuration page (wp-championship) to
            configure your guessing game

== Changelog ==

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
