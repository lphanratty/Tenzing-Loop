<?php
include_once("init.php");
//include_once("classes/et_person.php");
include_once("classes/et_form_generator.php");
include_once("include/profile_functions.php");
//echo "User GUID is ".$etUser->userGUID."<br />";
$currentSection = (isset($_REQUEST["SEC"])?$_REQUEST["SEC"]:2);
$resetCookie = (isset($_REQUEST["RC"])?$_REQUEST["RC"]:0);
$jscript = "js/profile.js|js/ajax_delete.js";
if($resetCookie == 1){
	$pagescript = 
	//"	alert('Resetting Cookie');\r\n".
	//"	eraseCookie('numProfileWarnings');\r\n".
	"	numWarnings = 0;\r\n".
	"	checkProfileComplete('".$etUser->userGUID."',".$etUser->typeID.");\r\n";
}else{
	$pagescript = "";
}
$pagescript .= 
"	var settings = {\r\n".
"    url: \"ajax_upload.php\",\r\n".
"    method: \"POST\",\r\n".
"    returnType: \"json\",\r\n".
"	 dynamicFormData: function()\r\n".
"	 {\r\n".
"		var data ={ GUID:\"".$etUser->userGUID."\"}\r\n".
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
"					GUID:\"".$etUser->userGUID."\",\r\n".
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
"					GUID:\"".$etUser->userGUID."\",\r\n".
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
//$profileIncomplete = $etUser->profileIncomplete();

if($profileIncomplete){
	$pagescript .=
	"checkProfileComplete('".$etUser->userGUID."',".$etUser->typeID.");\r\n";
}

if(isset($_POST["isSubmitted"])){
	//First we check to see if the user has already started completing their profile
	//If not we add a record to User Activity Log to indicate they have started
	$sql = 
	"SELECT ual.index ".
	"FROM userActivityLog ual ".
	"INNER JOIN activities act ON ual.activityID = act.activityID ".
	"WHERE ual.GUID = '".$etUser->userGUID."' ".
	"AND act.activityLabel = 'Started Profile'";
	if($result = $link->query($sql)){
		if($result->num_rows == 0){
			//There is no User Activity Log entry that indicates the User has started to complete their profile so we add one
			$sql = 
			"INSERT INTO userActivityLog ".
			"SET GUID = '".$etUser->userGUID."', ".
			"activityID = (SELECT activityID FROM activities WHERE activityLabel = 'Started Profile'), ". //5 is a Login
			"activityDate = NOW()";
			$link->query($sql);
		}
	}
	if($currentSection == 2){
		$forminator = new etForminator($currentSection,$etUser->userGUID);
		$formFields = $forminator->getFormFields();
	}elseif($currentSection == 4 || $currentSection == 5){
		$forminator = new etForminator(($currentSection - 1),$etUser->userGUID);
		$formFields = $forminator->getFormFields();
	}elseif($currentSection == 6){
		$forminator = new etForminator(6,$etUser->userGUID);
	}
	
	if($currentSection != 3 && $currentSection != 6){
		foreach($formFields as $fieldID=>$fieldData){
			if(!$forminator->submitValue($fieldID,$fieldData,$_POST["fld_".$fieldID])){
				$msg .= 
				"We could not enter data for ".$fieldData["fieldLabel"]."<br />\r\n";
			}
		}
	}
	if(isset($_POST["NEXT"])){
		$currentSection++;
		//echo "Current Section is ".$currentSection."<br />";
	}elseif(isset($_POST["PREV"])){
		$currentSection--;
	}elseif(isset($_POST["SAVE"])){
		$currentSection = 7;
	}
	//echo "Sending Current Section is ".$currentSection."<br />";
	//$content = getFormSection($currentSection, $etUser->userGUID);

}

if($currentSection < 7){
	//echo "Now Current Section is ".$currentSection."<br />";
	if($currentSection == 2){
		$forminator = new etForminator($currentSection,$etUser->userGUID);
		$formFields = $forminator->getFormFields();
		$content = getFormSection($currentSection, $etUser->userGUID);
	}elseif($currentSection == 3){
		$content = getFormSection($currentSection, $etUser->userGUID);
	}elseif($currentSection == 4 || $currentSection == 5){
		$forminator = new etForminator(($currentSection - 1),$etUser->userGUID);
		$formFields = $forminator->getFormFields();
		$content = getFormSection($currentSection, $etUser->userGUID);
	}elseif($currentSection == 6){
		$forminator = new etForminator(6,$etUser->userGUID);
		$content = getFormSection($currentSection, $etUser->userGUID);
	}
	
}else{
	header("location:myprofile.php");
	//echo "Going to profile<br />";
}

include("header.php");

echo $content;

include("footer.php");

function getFormSection($formSection, $userGUID){
	global $link, $forminator, $formFields, $profileIncomplete,$etUser;
	$retval = "";
	
	if($formSection == 2){
		$legend = "Let's Get Personal";
		$intro = 
		"<p style=\"margin-left:10px;width:90%;\">\r\n";
		if($profileIncomplete){
			$intro .=
			"	Welcome back.  Just a reminder that you are still in the process of completing your profile.<br /><br />\r\n".
			"	Once this is done you will be invited to officially join The Loop Agency and will have full access to the site which includes ".
			"	up to date job postings and promotional information.<br /><br />";
		}
		$intro .=
		"Let&#8217;s get to know you a bit better.<br />\r\n".
		"This process can take a couple of minutes to complete so make sure you are dressed comfortably, ".
		"have gone to the bathroom and have handy a PDF or word version of your resume as well as some ".
		"recent photographs of yourself.".
		"</p>\r\n";

	}elseif($formSection == 3){
		$legend = "Resume";
		$intro = 
		"<p style=\"margin-left:10px;width:90%;\">\r\n".
		"Please upload your most recent resume here".
		"</p>\r\n";
	}elseif($formSection == 4){
		$legend = "Work History";
		$intro = 
		"<p style=\"margin-left:10px;width:90%;\">\r\n".
		"<strong>A little more General</strong>".
		"</p>\r\n";
	}elseif($formSection == 5){
		$legend = "Work History";
		$intro = 
		"<p style=\"margin-left:10px;width:90%;\">\r\n".
		" <strong>A little more Promo Specific</strong>".
		"</p>\r\n";
	}elseif($formSection == 6){
		$legend = "Picture Ready";
		$intro = 
		"<p style=\"margin-left:10px;width:90%;\">\r\n".
		"	Why are we asking for your picture you may ask?  It&#8217;s because we want to show our clients who the face of their brand may be!<br /><br />\r\n".
		"	We don&#8217;t expect professional photos, but do have a few guidelines we ask that you follow<br />\r\n".
		"	<ul>\r\n".
		"		<li>Avoid photos with you drinking alcohol or smoking (anything.)</li>\r\n".
		"		<li>Avoid photos with other people</li>\r\n".
		"		<li>Avoid photos with you wearing sunglasses</li>\r\n".
		"		<li>We will not accept any photos that would be deemed inappropriate (that includes provocative photos)</li>\r\n".
		"		<li>We all love selfies but if you are going to submit one, make sure it&#8217;s of you not just the bathroom and your phone</li>\r\n".
		"		<li>Filter use:  We are a generation of photographers, we know what technology can do so we ask your photo be as natural as possible</li>\r\n".
		"		<li>Use only photos that are of good quality; clear, well lit and not fuzzy or pixilated</li>\r\n".
		"	</ul>\r\n".
		"	Please upload 4 photos of you that have been taken within the last 6 months\r\n".
		"	<ul>\r\n".
		"		<li>2 Head Shots (Again you don&#8217;t need anything professional just one that shows you more up close)</li>\r\n".
		"		<li>2 Full Length Photos</li>\r\n".
		"	</ul>\r\n".
		"</p>\r\n";
	}
	
	$retval .=
	//"<form name=\"rep_profile\" id=\"rep_profile\" action=\"".$_SERVER["PHP_SELF"]."?SEC=".$formSection."\" method=\"post\">\r\n".
	"<form name=\"rep_profile\" id=\"rep_profile\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">\r\n".
	"	<fieldset>\r\n".
	"		<legend>".$legend."</legend>\r\n".
	"		".$intro;
	
	if($formSection == 3){
		//$retval .= getResumeInfo($userGUID);
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
		
	}elseif($formSection == 2 || $formSection == 4 || $formSection == 5){
		//echo "Form Section in function is ".$formSection." looking for fields for ".($formSection - 1)."<br />";
		foreach($formFields as $fieldID=>$fieldData){
			$retval .= $forminator->createField($fieldID,$fieldData);
		} 
	}elseif($formSection == 6){
		$numCurrentHeadshots = 0;
		$numCurrentBodyshots = 0;
		$sql = 
		"SELECT linkID, filePath, fileCreated, dateCreated ".
		"FROM candidateFiles ".
		"WHERE fileType = 'HEADSHOT' ".
		"AND userGUID = '".$userGUID."' ".
		"ORDER BY dateCreated DESC ".
		"LIMIT 2 ";
		//$retval .= $sql."<br />";
		$retval .=
		"<div id=\"headshots\">\r\n";
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$numCurrentHeadshots = $result->num_rows;
				$retval .=
				"<table width=\"800\" border=\"0\">\r\n".
				"	<tr>\r\n".
				"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Head Shots</td>\r\n".
				"	</tr>\r\n".
				"	<tr>\r\n";
				while($row=$result->fetch_assoc()){
					$retval .= 
					"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
					"			<img src=\"".$row["filePath"]."\" style=\"width:200px;\" /><br />\r\n".
					(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M, Y",strtotime($row["dateCreated"]))."<br />\r\n").
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
		/*
		"<div>\r\n".
		"	Date Headshot Taken ".
		"	<select name=\"month_headshot_taken\" id=\"month_headshot_taken\">\r\n".
		"	<option value=\"\">Mon</option>\r\n".
		$forminator->generateMonthSelect("")." ".
		"	</select>\r\n"." ".
		"	<select name=\"year_headshot_taken\" id=\"year_headshot_taken\">\r\n".
		"	<option value=\"\">Year</option>\r\n".
		$forminator->generateYearsSelect(2, "", 1)." ".
		"	</select>\r\n"." ".
		"</div>\r\n".
		*/
		"<div id=\"headshotuploaderholder\" style=\"display:".($numCurrentHeadshots > 1?"none;":"block;")."\">\r\n".
		"<div>\r\n".
		"	Date Headshot Taken ".
		"	<select name=\"month_headshot_taken\" id=\"month_headshot_taken\">\r\n".
		"	<option value=\"\">Mon</option>\r\n".
		$forminator->generateMonthSelect("")." ".
		"	</select>\r\n"." ".
		"	<select name=\"year_headshot_taken\" id=\"year_headshot_taken\">\r\n".
		"	<option value=\"\">Year</option>\r\n".
		$forminator->generateYearsSelect(2, "", 1)." ".
		"	</select>\r\n"." ".
		"</div>\r\n".
		"<div id=\"headshotuploader\">Upload Head Shots</div>\r\n".
		"<div id=\"headshotstatus\"></div>\r\n".
		"</div>\r\n";
		
		$retval .= 
		"<div class=\"clear\"></div>\r\n";
		
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
				"<table width=\"800\" border=\"0\">\r\n".
				"	<tr>\r\n".
				"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Body Shots</td>\r\n".
				"	</tr>\r\n".
				"	<tr>\r\n";
				while($row=$result->fetch_assoc()){
					$retval .= 
					"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
					"			<img src=\"".$row["filePath"]."\" style=\"width:200px;\" /><br />\r\n".
					(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M, Y",strtotime($row["dateCreated"]))."<br />\r\n").
					"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('BODYSHOT','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n".
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
		"<div id=\"bodyshotuploaderholder\" style=\"display:".($numCurrentBodyshots > 1?"none;":"block;")."\">\r\n".
		"<div>\r\n".
		"	Date Bodyshot Taken ".
		"	<select name=\"month_bodyshot_taken\" id=\"month_bodyshot_taken\">\r\n".
		"	<option value=\"\">Mon</option>\r\n".
		$forminator->generateMonthSelect("")." ".
		"	</select>\r\n"." ".
		"	<select name=\"year_bodyshot_taken\" id=\"year_bodyshot_taken\">\r\n".
		"	<option value=\"\">Year</option>\r\n".
		$forminator->generateYearsSelect(2, "", 1)." ".
		"	</select>\r\n"." ".
		"</div>\r\n".
		"<div id=\"bodyshotuploader\">Upload Body Shots</div>\r\n".
		"<div id=\"bodyshotstatus\"></div>\r\n".
		"</div>\r\n";
	}
	$retval .=
	"	</fieldset>\r\n".
	"	<input type=\"hidden\" name=\"SEC\" value=\"".$formSection."\" />\r\n".
	"	<input type=\"hidden\" name=\"UID\" id=\"UID\" value=\"".$userGUID."\" />\r\n".
	"	<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
	"	<div style=\"width:82 2px;text-align:right;\">\r\n".
	getBottomNavLinks($formSection).
	//($formSection > 2?"		<input type=\"submit\" name=\"PREV\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"< Prev\" />\r\n":"").
	//"		<input type=\"submit\" name=\"SAVE\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n".
	//"		<input type=\"submit\" name=\"NEXT\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"Next >\" />\r\n".
	"	</div>\r\n".
	"</form>\r\n";
	return $retval;
}

function getBottomNavLinks($currentSection){
	$retval = "";
	switch($currentSection){
		case 2:
			$nextLink = "		<input type=\"submit\" name=\"NEXT\" class=\"btn_clear\" style=\"margin-right:0px;margin-left:0px;\" value=\"Next / Section 2: Resume\" /><br />\r\n";
			$prevLink = "";
			$saveLink = "		<input type=\"submit\" name=\"SAVE\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n";
			break;
		case 3:
			$nextLink = "		<input type=\"submit\" name=\"NEXT\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Next / Section 3: Work History A little more General\" /><br />\r\n";
			$prevLink = "		<input type=\"submit\" name=\"PREV\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Go Back  / Section 1: Let's get Personal\" /><br />\r\n";
			$saveLink = "		<input type=\"submit\" name=\"SAVE\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n";
			break;
		case 4:
			$nextLink = "		<input type=\"submit\" name=\"NEXT\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Next / Section 5: Work History A little more Promo Specific\" /><br />\r\n";
			$prevLink = "		<input type=\"submit\" name=\"PREV\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Go Back  / Section 2: Resume\" /><br />\r\n";
			$saveLink = "		<input type=\"submit\" name=\"SAVE\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n";
			break;
		case 5:
			$nextLink = "		<input type=\"submit\" name=\"NEXT\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Next / Section 6: Picture Ready\" /><br />\r\n";
			$prevLink = "		<input type=\"submit\" name=\"PREV\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Go Back  / Section 4: Work History A little more General\" /><br />\r\n";
			$saveLink = "		<input type=\"submit\" name=\"SAVE\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n";
			break;
		case 6:
			$nextLink = "		<input type=\"submit\" name=\"NEXT\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SUBMIT PROFILE\" /><br />\r\n";
			$prevLink = "		<input type=\"submit\" name=\"PREV\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"Go Back  / Section 5: Work History A little more Promo Specific\" /><br />\r\n";
			$saveLink = "";
			break;
	}
	$retval .= $nextLink.$prevLink.$saveLink;
	return $retval;
}

function getIncompleteFields($arrFields){
	global $link, $etUser;
	$retval = "";
	if($etUser->typeID == 4){
		$sql =
		"SELECT ff.fieldID AS fieldID, ff.formID AS formID, ff.filterName AS theLabel ".
		"FROM formFields ff ". 
		"WHERE NOT EXISTS ( ". 
		"	SELECT fieldID FROM formCandidateDetailsValues fv ". 
		"	WHERE idNum = '".$etUser->userGUID."' ". 
		"	AND fieldID = ff.fieldID ".
		") ". 
		"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ". 
		"AND ff.mandatory = 1 ".

		"UNION ".

		"SELECT 'RESUME' AS fieldID, '3' AS formID, 'You must upload a Resume' AS theLabel ".
		"FROM candidateFiles ".
		"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'RESUME' AND userGUID = '".$etUser->userGUID."') < 2 ".

		"UNION ".

		"SELECT 'HEADSHOT' AS fieldID, '6' AS formID, 'You must upload 2 Head Shots' AS theLabel ".
		"FROM candidateFiles ".
		"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'HEADSHOT' AND userGUID = '".$etUser->userGUID."') < 2 ".

		"UNION ".

		"SELECT 'BODYSHOT' AS fieldID, '6' AS formID,  'You must upload 2 Body Shots' AS theLabel ".
		"FROM candidateFiles ".
		"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'BODYSHOT' AND userGUID = '".$etUser->userGUID."') < 2 ".

		"ORDER BY formID";
		
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$retval .=
				"You're so close…but you are still missing the following information.<br /><br />";
				

				while($row = $result->fetch_assoc()){
					$retval .=
					"<strong>".$row["theLabel"]."</strong><br />";
				}
				$retval .=
				"<br />".
				"In order to have access to the full site including up to date listings of available jobs, you will need to provide this info.<br /><br />".
				"If you are not ready to provide, you can always save your current session and come back to your profile when ready to fill out and submit<br /><br />";
			}
		}
	}
	
	return $retval;
}
?>