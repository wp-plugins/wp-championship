/*
  javascript functions for ajax effect in cs_stats
*/

/* cs_stats1 */

/*
  javascript for ajax like request to update the stats
  and corresponding data on the fly
*/

/* get the data for the new location */
function wpc_stats1_update() {
    
    var newday     = document.getElementById("wpc_stats1_selector").value;
    var tippgroup  = document.getElementById("wpc_stats1_tippgroup").value;
    var siteuri    = document.getElementById("wpc_selector_site").value; 
    
    jQuery.get(siteuri + "/cs_stats.php", 
	       { newday: newday, tippgroup: tippgroup, header: "0" , selector: "1" },
	       function(data){
		   jQuery("div#wpc-stats1-res").html(data);
	       });
}

/* cs_stats4 */
/* get the data for the new location */
function wpc_stats4_update() {
    
    var username  = document.getElementById("wpc_stats4_selector").value;
    var siteuri = document.getElementById("wpc_selector_site4").value; 
    
    jQuery.get(siteuri + "/cs_stats.php", 
	       { username: username, header: "0" , selector: "1" },
	       function(data){
		   jQuery("div#wpc-stats4-res").html(data);
	       });
}


/* cs_stats5 */
/* get the data for the new location */
function wpc_stats5_update() {
    
    var newday  = document.getElementById("wpc_stats5_selector").value; 
    var tippgroup  = document.getElementById("wpc_stats5_tippgroup").value;
    var siteuri = document.getElementById("wpc_selector_site").value; 
    
    jQuery.get(siteuri + "/cs_stats.php", 
	       { newday5: newday, tippgroup: tippgroup, header: "0", selector: "1" },
	       function(data){
		   jQuery("div#wpc-stats5-res").html(data);
	       });
}


/* to work around IE problems */

/* javascript to rebuild the onLoad event for triggering 
   the first wpc_update call */

//create onDomReady Event
window.onDomReady = initReady;

// Initialize event depending on browser
function initReady(fn)
{
    //W3C-compliant browser
    if(document.addEventListener) {
	document.addEventListener("DOMContentLoaded", fn, false);
    }
    //IE
    else {
	document.onreadystatechange = function(){readyState(fn);};
    }
}

//IE execute function
function readyState(func)
{
    // DOM is ready
    if(document.readyState == "interactive" || document.readyState == "complete")
    {
	func();
    }
}
