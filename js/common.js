$(document).ready(function() {
	//$(function () {
	//	$(".style_text_field").click(function (e) {
	//		 alert(e.target.id);
	//	});
	//});
	$.mask.definitions['y'] = "[0-9]";
	$.mask.definitions['m'] = "[0-9]";
	$.mask.definitions['d'] = "[0-9]";
	$(".phone").mask("(999) 999-9999");
	$(".postcode").mask("a9a 9a9");
	$(".typedate").mask("yyyy/mm/dd");
	$("#closeModal").click(function() {
		hideMsgBox();
	});
	$( "#legallywork" ).change(function() {
		checkLegallyWork();
	});	
	if(window.location.pathname.indexOf("completeMyProfile.php") < 0){
		//alert("You are NOT on completeMyProfile " + window.location.pathname);
		createCookie('numProfileWarnings','0',7);
	}else{
		//alert("You ARE on completeMyProfile");
	}
});

function showMsgBox(msgType, msgText){
	if(msgType.toUpperCase() == "MODAL"){
		$("#msgBox").attr("class","msgBoxModal");
	}else{
		$("#msgBox").attr("class","msgBoxHelp");
	}
	
	$("#msgText").html(msgText);
	$("#msgBox").show();
}

function hideMsgBox(){
	$("#msgBox").hide();
	$("#msgText").html("");
	$("#msgBox").removeClass();
}

function clearField(fldID, matchVal){
	//alert("Value is " + $("#" + fldID).val());
	if(matchVal == "ALL" || matchVal == $("#" + fldID).val()){
		$("#" + fldID).val("");
	}
}

function addFieldDefault(fldID, defaultVal){
	if($("#" + fldID).val().length == 0){
		$("#" + fldID).val(defaultVal);
	}
}

function showField_orig(jsData){
	var arrData = jsData.split("-");
	//alert("index 0 is " + arrData[0] + " and index 1 is " + arrData[1]);
	if(arrData[0].toLowerCase().indexOf(arrData[1].toLowerCase()) >= 0){
		for(var i = 2; i < arrData.length;i++){
			$("."+arrData[i]).show();
		}
	}else{
		//alert("Hiding");
		for(var i = 2; i < arrData.length;i++){
			$("."+arrData[i]).hide();
		}
	}
}

function showField(jsData){
	var arrData = jsData.split("-");
	var fieldList = "";
	//alert("Match Value is " + arrData[1] + " My Name is " + arrData[0]);
	var showIt = false;
	var fieldID = arrData[0].substring(4);
	//alert("The ID is " + fieldID);
	var vals = $('[name^="' + arrData[0].replace("[]","") +'"]').val();
	
	if($.isArray(vals)){
		for (var i=0; i < vals.length; i++){
			//if(vals[i].toLowerCase() == arrData[1].toLowerCase() || (arrData[1].toUpperCase() == "YES" && vals[i] == 1)){
			if(arrData[1].toLowerCase().indexOf(vals[i].toLowerCase()) > -1  || (arrData[1].toUpperCase() == "YES" && vals[i] == 1)){
				showIt = true;
				//alert("showing");
			} 
		}
	}else{
		if ($('input[name^=' + arrData[0].replace("[]","") + ']').is(":radio") || $('input[name^=' + arrData[0].replace("[]","") + ']').is(":checkbox")) {
			//alert("I am a Radio Button");
			//if($('input[name=' + arrData[0] + ']:checked').val().toLowerCase() == arrData[1].toLowerCase() || (arrData[1].toUpperCase() == "YES" && $('input[name=' + arrData[0] + ']:checked').val() == 1)){
			if(arrData[1].toLowerCase().indexOf($('input[name^=' + arrData[0].replace("[]","") + ']:checked').val().toLowerCase()) > -1 || (arrData[1].toUpperCase() == "YES" && $('input[name^=' + arrData[0].replace("[]","") + ']:checked').val() == 1)){
				showIt = true;
				//alert("showing");
			} 
		}else{
			//alert("indexOf is " + arrData[1].toLowerCase() + " searching for " + $('[name=' + arrData[0] + ']').val().toLowerCase());
			//alert("Index is " + arrData[1].toLowerCase().indexOf($('[name=' + arrData[0] + ']').val().toLowerCase()));
			//if(arrData[1].toLowerCase().indexOf($('[name=' + arrData[0] + ']').val().toLowerCase()) > -1){
			if(arrData[1].toLowerCase().indexOf($('#' + arrData[0]).val().toLowerCase()) > -1){
				showIt = true;
			}
		}
	}
	if(showIt){
		for(var i = 2; i < arrData.length;i++){
			$("."+arrData[i]).show();
		}
	}else{
		for(var i = 2; i < arrData.length;i++){
			if(i > 2){
				fieldList = fieldList + "~";
			}
			fieldList = fieldList + arrData[i];
			//alert("fieldList is " + fieldList);
			$("."+arrData[i]).hide();
		}
		if ($('input[name^=' + arrData[0].replace("[]","") + ']').is(":radio")){
			//alert("I am a radio button");
			if($('input[name^=' + arrData[0].replace("[]","") + ']:checked').val() == 0){
				//alert("I am going to delete this!");
				deleteFieldValues($("#UID").val(),fieldList);
			}
		}
	}
	
}

function checkLegallyWork(){
	checkRegisterState();
	if($("#legallywork option:selected").text() == "No"){
		var msgText = 
		"Thank you so much for taking the time to visit our website and for " +
		"your interest in working with our company. Unfortunately you are not " +
		"able to complete an account and profile with us, as we are not able " +
		"to book you for upcoming work. You must be eligible to work in Canada " +
		"in order to work for us.";
		showMsgBox("MODAL", msgText);
	}
	
}

function checkRegisterState(){
	var isGood = false;
	//if($("#legallywork option:selected" ).val() == 1){
	if($("#legallywork").val() == 1){
		isGood = true;
	}
	if(!isGood){
		//$(".style_text_field").prop('disabled', true);
		$(".province").prop('disabled', true);
		$("input").prop('disabled', true);
	}else{
		$(".province").prop('disabled', false);
		$("input").prop('disabled', false);
	}
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

