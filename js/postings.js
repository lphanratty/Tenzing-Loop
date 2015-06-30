var currentVisible = "postings_active";

function showDiv(divName){
	//first we hide all profiledivs
	$(".profilediv").hide();
	$("#"+divName).fadeIn();
}