function toggle_lost()
{
    document.getElementById("losttab").className = "now";
    document.getElementById("alltab").className = "";
    document.getElementById("foundtab").className = "";

    document.getElementById("lost").className = "visible";
    document.getElementById("all").className = "invisible";
    document.getElementById("found").className = "invisible";

}
function toggle_all()
{
    document.getElementById("losttab").className = "";
    document.getElementById("alltab").className = "now";
    document.getElementById("foundtab").className = "";
    
    document.getElementById("lost").className = "invisible";
    document.getElementById("all").className = "visible";
    document.getElementById("found").className = "invisible";

}
function toggle_found()
{
    document.getElementById("losttab").className = "";
    document.getElementById("alltab").className = "";
    document.getElementById("foundtab").className = "now";

    document.getElementById("lost").className = "invisible";
    document.getElementById("all").className = "invisible";
    document.getElementById("found").className = "visible";

}
function check()
{
	var op1 = document.getElementById("opLost")
	if ( op1.className = "unchecked" )
		op1.classNmae = "checked";
	else
		op1.className = "unchecked";

	var op2 = document.getElementById("opFound")
	if ( op2.className = "unchecked" )
		op2.classNmae = "checked";
	else
		op2.className = "unchecked";

}