<?php
include_once("init.php");
//include_once("classes/et_person.php");
include_once("classes/et_form_generator.php");
//echo "User GUID is ".$etUser->userGUID."<br />";
$currentSection = (isset($_REQUEST["SEC"])?$_REQUEST["SEC"]:2);

if(isset($_POST["isSubmitted"])){
	/*
	foreach($_POST as $index=>$val){
		echo "POST: ".$index." = ".$val."<br />";
	}
	echo "User GUID is ".$etUser->userGUID."<br />";
	*/
	$forminator = new etForminator($currentSection,$etUser->userGUID);
	$formFields = $forminator->getFormFields();
	foreach($formFields as $fieldID=>$fieldData){
		if(!$forminator->submitValue($fieldID,$fieldData,$_POST["fld_".$fieldID])){
			$msg .= 
			"We could not enter data for ".$fieldData["fieldLabel"]."<br />\r\n";
		}
	}
	if(isset($_POST["NEXT"])){
		$currentSection++;
		//echo "Current Section is ".$currentSection."<br />";
	}elseif(isset($_POST["PREV"])){
		$currentSection--;
	}
	//echo "Current Section is ".$currentSection."<br />";
	$content = getFormSection($currentSection, $etUser->userGUID);
}else{
	$forminator = new etForminator($currentSection,$etUser->userGUID);
	$formFields = $forminator->getFormFields();
	$content = getFormSection($currentSection, $etUser->userGUID);
}
if($currentSection < 5){
	$forminator = new etForminator($currentSection,$etUser->userGUID);
	$formFields = $forminator->getFormFields();
	$content = getFormSection($currentSection, $etUser->userGUID);
}else{
	header("location:memberhome.php");
}

include("header.php");

echo $content;

include("footer.php");

function getFormSection($formSection, $userGUID){
	global $link, $forminator, $formFields;
	$retval = "";
	
	if($formSection == 2){
		$legend = "Let's Get Personal";
	}elseif($formSection == 3){
		$legend = "General Work History";
	}elseif($formSection == 4){
		$legend = "Promotional History";
	}
	
	
	
	$retval .=
	//"<form name=\"rep_profile\" id=\"rep_profile\" action=\"".$_SERVER["PHP_SELF"]."?SEC=".$formSection."\" method=\"post\">\r\n".
	"<form name=\"rep_profile\" id=\"rep_profile\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">\r\n".
	"	<fieldset>\r\n".
	"		<legend>".$legend."</legend>\r\n";
	foreach($formFields as $fieldID=>$fieldData){
				$retval .= $forminator->createField($fieldID,$fieldData);
			} 
	$retval .=
	"	</fieldset>\r\n".
	"	<input type=\"hidden\" name=\"SEC\" value=\"".$formSection."\" />\r\n".
	"	<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
	"	<div style=\"width:422px;text-align:right;\">\r\n".
	($formSection > 2?"		<input type=\"submit\" name=\"PREV\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"< Prev\" />\r\n":"").
	"		<input type=\"submit\" name=\"NEXT\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"Next >\" />\r\n".
	"	</div>\r\n".
	"</form>\r\n";
	return $retval;
}
?>