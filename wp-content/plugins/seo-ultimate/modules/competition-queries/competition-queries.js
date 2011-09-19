function su_competition_queries_show_step2(type, showminimal) {
	document.getElementById('methodtype').innerHTML=type;
	
	if (showminimal)
		document.getElementById('minimal-checkbox').style.visibility='visible';
	else
		document.getElementById('minimal-checkbox').style.visibility='hidden';
}