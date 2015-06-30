function submitValue(whatTable,whatField,whatValue,userGUID){
	var request = $.ajax({
	  url: "ajax_submit_field.php",
	  type: "POST",
	  data: {"sTable" : whatTable, "sField": whatField, "sValue" : whatValue, "UID" : userGUID},
	  dataType: "json"
	});
	request.done(function(data) {
				
	});
}