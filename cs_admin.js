/*
  javascript for field validation and input support
*/

function calc_shortname() {
    var name      = document.getElementById("team_name").value;
    var shortname = document.getElementById("team_shortname").value;

    if (shortname == "")
	document.getElementById("team_shortname").value = name.substring(0,5);
}