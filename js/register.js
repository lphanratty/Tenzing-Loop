$(document).ready(function() {
	//$("#submitbtn").prop('disabled', true);
	$("#submitbtn").hide();
	$("#register_person *").filter(":input").blur(function(){
		checkRegisterFields();
	});
});

function checkRegisterFields(){
	var allComplete = true;
	$("#register_person *").filter(":input").each(function(){
		if($(this).hasClass("mandatory")){
			if($(this).val().length == 0){
				allComplete = false;
				//alert($(this).attr("name") + " is incomplete");
				return false;
			}
		}
	});
	if(allComplete){
		//$("#submitbtn").prop('disabled', false);
		$("#submitbtn").show();
	}else{
		$("#submitbtn").hide();
	}
}