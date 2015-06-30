var currentVisible = "employmentstatus";

function showDiv(divName){
	//first we hide all profiledivs
	$(".policydiv").hide();
	$("#"+divName).fadeIn();
}