var currentVisible = "personalinfo";
var numWarnings = readCookie('numProfileWarnings');
if (!numWarnings) {
	createCookie('numProfileWarnings','0',7);
	numWarnings = 0;
}
function showDiv(divName){
	//first we hide all profiledivs
	$(".profilediv").hide();
	$("#"+divName).fadeIn();
}

function checkProfileComplete(userGUID,userType){
	var request = $.ajax({
	  url: "ajax_check_profile_complete.php",
	  type: "POST",
	  data: {"UID" : userGUID,"UT" : userType},
	  dataType: "json"
	});
	request.done(function(data) {
		if(data.msg.length > 0 && numWarnings == 0){
			showMsgBox("MODAL",data.msg);
			createCookie('numProfileWarnings','1',7);
		}
	});
}

function deleteFieldValues(userGUID,fieldList){
	//alert("User GUID is " + userGUID);
	var request = $.ajax({
	  url: "ajax_delete_field_values.php",
	  type: "POST",
	  data: {"UID" : userGUID,"fList" : fieldList},
	  dataType: "json"
	});
	request.done(function(data) {
		if(data.msg.length > 0 && numWarnings == 0){
			
		}
	});
}