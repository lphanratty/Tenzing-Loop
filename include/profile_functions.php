<?php
function getResumeInfo($userGUID){
	global $link, $etUser;
	$retval = "";
	$numCurrentResumes = 0;
	$sql = 
	"SELECT linkID, filePath, dateCreated ".
	"FROM candidateFiles ".
	"WHERE fileType = 'RESUME' ".
	"AND userGUID = '".$userGUID."' ".
	"ORDER BY dateCreated DESC ".
	"LIMIT 1";
	//$retval .= $sql."<br />";
	if($result = $link->query($sql)){
		$numCurrentResumes = $result->num_rows;
		$retval .= 
		"<div id=\"resumelist\">\r\n";
		if($result->num_rows > 0){
			if($row=$result->fetch_assoc()){
				$retval .= 
				"	<a href=\"".$row["filePath"]."\">Current Resume</a> Last Updated on ".date("M jS, Y",strtotime($row["dateCreated"])).
				"&nbsp;&nbsp; <INPUT TYPE=\"BUTTON\" class=\"btn_blue\" value=\"Delete Resume\" onclick=\"deleteFile('RESUME','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n";
			}
		}
		$retval .=
		"</div>\r\n";
	}else{
		$retval .= "Error<br />";
	}

	if(strcasecmp($userGUID,$etUser->userGUID) == 0){
		$retval .=
		"<div id=\"resumeuploaderholder\" style=\"display:".($numCurrentResumes > 0?"none;":"block;")."\">\r\n".
		"<div id=\"resumeuploader\">Upload Resume</div>\r\n".
		"<div id=\"status\"></div>\r\n".
		"</div>\r\n";
	}
	return $retval;
}

function getUploaderSettings($userGUID){
	$retval = "";
	$retval .=
	"	var settings = {\r\n".
	"    url: \"ajax_upload.php\",\r\n".
	"    method: \"POST\",\r\n".
	"    returnType: \"json\",\r\n".
	"	 dynamicFormData: function()\r\n".
	"	 {\r\n".
	"		var data ={ GUID:\"".$userGUID."\"}\r\n".
	"		return data;\r\n".
	"	 },\r\n".
	"    allowedTypes:\"doc,docx,pdf,rtf,txt\",\r\n".
	"    fileName: \"myresume\", \r\n".
	"    multiple: false,\r\n".
	"    showDone: false,\r\n".
	"    onSuccess:function(files,data,xhr) \r\n".
	"    {\r\n".
	"		//alert(\"Number of current files is \" + data.fileList);\r\n".
	"		if(data.currentFiles > 0){\r\n".
	"			$(\"#resumelist\").html(data.fileList);\r\n".	
	"			$(\"#resumeuploaderholder\").hide();\r\n".
	"		}else{\r\n".
	"			$(\"#resumeuploaderholder\").show();\r\n".
	"		}\r\n".
	"        $(\"#status\").html(\"<font color='green'>Upload is successful</font>\");\r\n".
	"\r\n".
	"    },\r\n".
	"    onError: function(files,status,errMsg)\r\n".
	"    { \r\n".
	"       	$(\"#status\").html(\"<font color='red'>Upload has Failed</font>\");\r\n".
	"    }\r\n".
	"}\r\n".
	"	var headshotsettings = {\r\n".
	"    url: \"ajax_upload.php\",\r\n".
	"    method: \"POST\",\r\n".
	"    returnType: \"json\",\r\n".
	//"	 autoSubmit: false,\r\n".
	"	 dynamicFormData: function()\r\n".
	"	 {\r\n".
	"		var data ={ \r\n".
	"					GUID:\"".$userGUID."\",\r\n".
	"					HEADSHOTMONTH:$('#month_headshot_taken').val(),\r\n".
	//"					HEADSHOTDAY:$('#day_headshot_taken').val(),\r\n".
	"					HEADSHOTYEAR:$('#year_headshot_taken').val()\r\n".
	"					}\r\n".
	"		return data;\r\n".
	"	 },\r\n".
	"    allowedTypes:\"jpg,png,gif\",\r\n".
	"    fileName: \"myheadshot\", \r\n".
	"    multiple:false,\r\n".
	"    showDone: false,\r\n".
	"	 onSelect:function(files)\r\n".
	"	{\r\n".
	"    	if($('#month_headshot_taken').val().length > 0 && $('#year_headshot_taken').val().length > 0){\r\n".
	"	    return true; //to allow file submission.\r\n".
	"		}else{\r\n".
	"			alert(\"Please select the Month and Year the photo was taken\");\r\n".
	"			return false;\r\n".
	"		}\r\n".
	"	},\r\n".
	"    onSuccess:function(files,data,xhr) \r\n".
	"    {\r\n".
	"		//alert(\"Number of current files is \" + data.fileList);\r\n".
	"		if(data.currentFiles > 0){\r\n".
	"			$(\"#headshots\").html(data.fileList);\r\n".
	"			$(\"#month_headshot_taken\").val('');\r\n".		
	"			$(\"#year_headshot_taken\").val('');\r\n".	
	"		}\r\n".	
	"		if(data.currentFiles > 1){\r\n".
	"			$(\"#headshotuploaderholder\").hide();\r\n".
	"		}else{\r\n".
	"			$(\"#headshotuploaderholder\").show();\r\n".
	"		}\r\n".
	"        $(\"#status\").html(\"<font color='green'>Upload is successful</font>\");\r\n".
	"\r\n".
	"    },\r\n".
	"    onError: function(files,status,errMsg)\r\n".
	"    { \r\n".
	"       	$(\"#status\").html(\"<font color='red'>Upload has Failed</font>\");\r\n".
	"    }\r\n".
	"}\r\n".
	"	var bodyshotsettings = {\r\n".
	"    url: \"ajax_upload.php\",\r\n".
	"    method: \"POST\",\r\n".
	"    returnType: \"json\",\r\n".
	"	 dynamicFormData: function()\r\n".
	"	 {\r\n".
	"		var data ={ \r\n".
	"					GUID:\"".$userGUID."\",\r\n".
	"					BODYSHOTMONTH:$('#month_bodyshot_taken').val(),\r\n".
	//"					BODYSHOTDAY:$('#day_bodyshot_taken').val(),\r\n".
	"					BODYSHOTYEAR:$('#year_bodyshot_taken').val()\r\n".
	"					}\r\n".
	"		return data;\r\n".
	"	 },\r\n".
	"    allowedTypes:\"jpg,png,gif\",\r\n".
	"    fileName: \"mybodyshot\", \r\n".
	"    multiple: false,\r\n".
	"    showDone: false,\r\n".
	"	 onSelect:function(files)\r\n".
	"	{\r\n".
	"    	if($('#month_bodyshot_taken').val().length > 0 && $('#year_bodyshot_taken').val().length > 0){\r\n".
	"	    	return true; //to allow file submission.\r\n".
	"		}else{\r\n".
	"			alert(\"Please select the Month and Year the photo was taken\");\r\n".
	"			return false;\r\n".
	"		}\r\n".
	"	},\r\n".
	"    onSuccess:function(files,data,xhr) \r\n".
	"    {\r\n".
	"		//alert(\"Number of current files is \" + data.fileList);\r\n".
	"		if(data.currentFiles > 0){\r\n".
	"			$(\"#bodyshots\").html(data.fileList);\r\n".
	"			$(\"#month_bodyshot_taken\").val('');\r\n".		
	"			$(\"#year_bodyshot_taken\").val('');\r\n".	
	"		}\r\n".	
	"		if(data.currentFiles > 1){\r\n".
	"			$(\"#bodyshotuploaderholder\").hide();\r\n".
	"		}else{\r\n".
	"			$(\"#bodyshotuploaderholder\").show();\r\n".
	"		}\r\n".
	"        $(\"#status\").html(\"<font color='green'>Upload is successful</font>\");\r\n".
	"\r\n".
	"    },\r\n".
	"    onError: function(files,status,errMsg)\r\n".
	"    { \r\n".
	"       	$(\"#status\").html(\"<font color='red'>Upload has Failed</font>\");\r\n".
	"    }\r\n".
	"}\r\n".
	"$(\"#resumeuploader\").uploadFile(settings);\r\n".
	"$(\"#headshotuploader\").uploadFile(headshotsettings);\r\n".
	"$(\"#bodyshotuploader\").uploadFile(bodyshotsettings);\r\n";
	return $retval;
}

function getMemberPics($userGUID){
	global $link, $etUser;
	$retval = "";
	$numCurrentHeadshots = 0;
	$numCurrentBodyshots = 0;
	$workinfo = new etForminator(3,$GUID);
	$sql = 
		"SELECT linkID, filePath, fileCreated, dateCreated ".
		"FROM candidateFiles ".
		"WHERE fileType = 'HEADSHOT' ".
		"AND userGUID = '".$userGUID."' ".
		"ORDER BY dateCreated DESC ".
		"LIMIT 3 ";

		$retval .=
		"<div id=\"headshots\">\r\n";
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$numCurrentHeadshots = $result->num_rows;
				$retval .=
				"<table style=\"max-width:800px;\" border=\"0\">\r\n".
				"	<tr>\r\n".
				"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Head Shots</td>\r\n".
				"	</tr>\r\n".
				"	<tr>\r\n";
				while($row=$result->fetch_assoc()){
					$retval .= 
					//"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
					"		<td width=\"250\" align=\"center\">\r\n".
					"			<img src=\"".$row["filePath"]."\" style=\"width:200px;\" /><br />\r\n".
					(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M jS, Y",strtotime($row["dateCreated"]))."<br />\r\n").
					"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('HEADSHOT','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n".
					"		</td>\r\n";
				}
				$retval .=
				"	</tr>\r\n".
				"</table>\r\n";
			}
		}else{
			$retval .= "Error<br />";
		}
		$retval .=
		"</div>\r\n";
		
		$retval .=
		"<div class=\"clear\"><br /></div>\r\n".
		"<div id=\"headshotuploaderholder\" style=\"display:".($numCurrentHeadshots > 1?"none;":"block;")."\">\r\n".
		"<div>\r\n".
		"	Date Headshot Taken ".
		"	<select name=\"month_headshot_taken\" id=\"month_headshot_taken\">\r\n".
		"	<option value=\"\">Mon</option>\r\n".
		$workinfo->generateMonthSelect("")." ".
		"	</select>\r\n"." ".
		"	<select name=\"year_headshot_taken\" id=\"year_headshot_taken\">\r\n".
		"	<option value=\"\">Year</option>\r\n".
		$workinfo->generateYearsSelect(2, "", 1)." ".
		"	</select>\r\n"." ".
		"</div>\r\n".
		"<div id=\"headshotuploader\">Upload Head Shots</div>\r\n".
		"<div id=\"headshotstatus\"></div>\r\n".
		"</div>\r\n";
		
		
		$retval .= 
		"<div class=\"clear\"><br /></div>\r\n";
		
		//Now we do the Body Shots
		$sql = 
		"SELECT linkID, filePath, fileCreated, dateCreated ".
		"FROM candidateFiles ".
		"WHERE fileType = 'BODYSHOT' ".
		"AND userGUID = '".$userGUID."' ".
		"ORDER BY dateCreated DESC ".
		"LIMIT 2 ";
		//$retval .= $sql."<br />";
		$retval .=
		"<div id=\"bodyshots\">\r\n";
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$numCurrentBodyshots = $result->num_rows;
				$retval .=
				"<div id=\"bodyshots\">\r\n".
				"<table style=\"max-width:800px;\" border=\"0\">\r\n".
				"	<tr>\r\n".
				"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Body Shots</td>\r\n".
				"	</tr>\r\n".
				"	<tr>\r\n";
				while($row=$result->fetch_assoc()){
					$retval .= 
					"		<td width=\"250\" align=\"center\">\r\n".
					"			<img src=\"".$row["filePath"]."\" style=\"width:200px;\" /><br />\r\n".
					(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M, Y",strtotime($row["dateCreated"]))."<br />\r\n").
					"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('BODYSHOT','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n".
					"		</td>\r\n";
				}
				$retval .=
				"	</tr>\r\n".
				"</table>\r\n".
				"</div>\r\n";
			}
		}else{
			$retval .= "Error<br />";
		}
		$retval .=
		"</div>\r\n";
		
		$retval .=
		"<div class=\"clear\"><br /></div>\r\n".
		"<div id=\"bodyshotuploaderholder\" style=\"display:".($numCurrentBodyshots > 1?"none;":"block;")."\">\r\n".
		"<div>\r\n".
		"	Date Bodyshot Taken ".
		"	<select name=\"month_bodyshot_taken\" id=\"month_bodyshot_taken\">\r\n".
		"	<option value=\"\">Mon</option>\r\n".
		$workinfo->generateMonthSelect("")." ".
		"	</select>\r\n"." ".
		"	<select name=\"year_bodyshot_taken\" id=\"year_bodyshot_taken\">\r\n".
		"	<option value=\"\">Year</option>\r\n".
		$workinfo->generateYearsSelect(2, "", 1)." ".
		"	</select>\r\n"." ".
		"</div>\r\n".
		"<div id=\"bodyshotuploader\">Upload Body Shots</div>\r\n".
		"<div id=\"bodyshotstatus\"></div>\r\n".
		"</div>\r\n";
		
	
	
	return $retval;
}
?>