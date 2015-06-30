function deleteFile(fileType,linkID,filePath,userGUID){
	var request = $.ajax({
	  url: "ajax_delete_file.php",
	  type: "POST",
	  data: {"fType" : fileType, "LID": linkID, "fPath" : filePath, "UID" : userGUID},
	  dataType: "json"
	});
	request.done(function(data) {
		if(fileType == "RESUME"){
			$("#resumelist").html(data.msg);
			$(".ajax-file-upload-statusbar").hide();
			$("#resumeuploaderholder").show();
		}else if(fileType == "HEADSHOT"){
			$("#headshots").html(data.msg);
			$(".ajax-file-upload-statusbar").hide();
			if(data.numCurrentFiles < 2){
				$("#headshotuploaderholder").show();
			}
		}else if(fileType == "BODYSHOT"){
			$("#bodyshots").html(data.msg);
			$(".ajax-file-upload-statusbar").hide();
			if(data.numCurrentFiles < 2){
				$("#bodyshotuploaderholder").show();
			}
		}
		
	});
}

