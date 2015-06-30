<?php
include_once("init.php");
include_once("classes/et_person.php");
include_once("classes/et_member.php");
include_once("classes/et_form_generator.php");
include_once("include/profile_functions.php");
if(isset($_REQUEST["GUID"])){
	$GUID = $_REQUEST["GUID"];
}else{
	$GUID = $_SESSION["et_userGUID"];
}
$member = new etMember($GUID);
setcookie("numProfileWarnings", "", time()-3600);
//Here is where we set the sql to grab the form values
//This code concatenates the original field PLUS any fields (from formFields) that has a "fieldParent"
//value equal to the original field
//We are ONLY getting fields in which the "fieldParent" value is NULL as we don't want to generate values
//for each child field
$contactinfo = new etForminator(1,$GUID);
$contactData = $contactinfo->getFormFields("VALUES");
$profileinfo = new etForminator(2,$GUID);
$profileData = $profileinfo->getFormFields("VALUES");
$workinfo = new etForminator(3,$GUID);
$workData = $workinfo->getFormFields("VALUES");
$promotionalinfo = new etForminator(4,$GUID);
$promotionalData = $promotionalinfo->getFormFields("VALUES");
$jscript = "js/profile.js|js/ajax_delete.js";
$pagescript = 
"	showDiv('personalinfo');\r\n".
//"	eraseCookie('numProfileWarnings');\r\n".
"	$(\"#myprofilelink\").addClass(\"currentpagelink\");\r\n".
"	$(\"#personalinfolink\").click(function(){\r\n".
"		showDiv('personalinfo');\r\n".
"	});\r\n".
"	$(\"#workinfolink\").click(function(){\r\n".
"		showDiv('workinfo');\r\n".
"	});\r\n".
"	$(\"#resumelink\").click(function(){\r\n".
"		showDiv('resumeinfo');\r\n".
"	});\r\n".
"	$(\"#profilepictureslink\").click(function(){\r\n".
"		showDiv('picsinfo');\r\n".
"	});\r\n".
getUploaderSettings($etUser->userGUID);
include("header.php");
?>
<div id="page_menu">
	<a id="personalinfolink" class="page_menu_link">Personal Info</a>
	<a id="resumelink" class="page_menu_link">Resume</a>
	<a id="workinfolink" class="page_menu_link">Work Info</a>
	<a id="profilepictureslink" class="page_menu_link">Pictures</a>
	<?php
		if($etUser->typeID < 4){
			echo "<a id=\"deleteuser\" href=\"deleteUser.php?GUID=".$member->userGUID."\" class=\"page_menu_link\" style=\"color:red;font-weight:bold;margin-left:50px;\">Delete this User</a>";
		}
	?>
</div>
<div class="clear"></div>

<div id="personalinfo" class="profilediv">
	<?php
		if($etUser->typeID < 4){
			echo 
			"<p>\r\n".
				$member->firstName." has ".($member->acknowledge_employment_status === false?"NOT ":"")." acknowledged understanding the Employment Status Policies<br />\r\n".
				$member->firstName." has ".($member->acknowledge_company_policies === false?"NOT ":"")." acknowledged understanding the Company Policies<br />\r\n".
			"</p>\r\n";
		}
	?>
	<label>Name</label>
	<div class="display_text">
		<?php echo $member->firstName." ".$member->lastName; ?>
	</div>
	<div class="clear"></div>
	<label>Email</label>
	<div class="display_text">
		<?php echo "<a href=\"mailto:".$member->email."\">".$member->email."</a>\r\n";?>
	</div>
	<div class="clear"></div>
	<?php
		foreach($contactData as $fieldID=>$fieldData){
			echo $contactinfo->createTextDisplay($fieldID,$fieldData);
		}
		foreach($profileData as $fieldID=>$fieldData){
			echo $profileinfo->createTextDisplay($fieldID,$fieldData);
		}
	?>
	<p>
		<a href="profileUpdate.php?SEC=2">Edit or Update Info:</a> If your info changes please make sure to let us know 
	</p>
</div>
<div id="resumeinfo" class="profilediv">
	<?php echo getResumeInfo($GUID); ?>
</div>
<div id="workinfo" class="profilediv">
	<?php
		echo "<h3>General</h3>\r\n";
		foreach($workData as $fieldID=>$fieldData){
			echo $workinfo->createTextDisplay($fieldID,$fieldData);
		}
	?>
	<p>
		<a href="profileUpdate.php?SEC=4">Edit or Update Info:</a> If your experience changes please make sure to let us know 
	</p>
	<?php
		echo "<h3>Promotional History</h3>\r\n";
		foreach($promotionalData as $fieldID=>$fieldData){
			echo $promotionalinfo->createTextDisplay($fieldID,$fieldData);
		}
	?>
	<p>
		<a href="profileUpdate.php?SEC=5">Edit or Update Info:</a> If your experience changes please make sure to let us know 
	</p>
</div>
<div id="picsinfo" class="profilediv">
	<?php echo getMemberPics($GUID); ?>
</div>
<?php
include("footer.php");
?>

