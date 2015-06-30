<?php
include_once("init.php");
include_once("include/inc_form_functions.php");
$userDetails = "";
$returnType = isset($_POST["returnType"])?$_POST["returnType"]:"DISPLAY";
$c_name = isset($_POST["c_name"])?$_POST["c_name"]:"";
$c_email = isset($_POST["c_email"])?$_POST["c_email"]:"";
$c_prov = isset($_POST["c_prov"])?$_POST["c_prov"]:"";
if(isset($_POST["c_city"])){
	if(strcasecmp($_POST["c_city"],"City1,City2") == 0){
		$c_city = "";
	}else{
		$c_city = $_POST["c_city"];
	}
}else{
	$c_city = "";
}
$c_gender = isset($_POST["c_gender"])?$_POST["c_gender"]:"";
$c_age_from = isset($_POST["c_age_from"])?$_POST["c_age_from"]:"";
$c_age_to = isset($_POST["c_age_to"])?$_POST["c_age_to"]:"";
$c_serving_license = isset($_POST["c_serving_license"])?$_POST["c_serving_license"]:"";
$c_food_handling = isset($_POST["c_food_handling"])?$_POST["c_food_handling"]:"";
$c_own_vehicle = isset($_POST["c_own_vehicle"])?$_POST["c_own_vehicle"]:"";
$c_languages = isset($_POST["c_languages"])?$_POST["c_languages"]:"";
$c_languages_other = isset($_POST["c_languages_other"])?$_POST["c_languages_other"]:"";
$c_height_feet = isset($_POST["c_height_feet"])?$_POST["c_height_feet"]:"";
$c_height_inches = isset($_POST["c_height_inches"])?$_POST["c_height_inches"]:"";
$c_hair_colour = isset($_POST["c_hair_colour"])?$_POST["c_hair_colour"]:"";
$c_uniform_size = isset($_POST["c_uniform_size"])?$_POST["c_uniform_size"]:"";
$c_sales_experience = isset($_POST["c_sales_experience"])?$_POST["c_sales_experience"]:"";
$c_work_children = isset($_POST["c_work_children"])?$_POST["c_work_children"]:"";
$c_food_beverage = isset($_POST["c_food_beverage"])?$_POST["c_food_beverage"]:"";
$c_technology = isset($_POST["c_technology"])?$_POST["c_technology"]:"";
$c_alcohol_sampling = isset($_POST["c_alcohol_sampling"])?$_POST["c_alcohol_sampling"]:"";
$c_data_collection = isset($_POST["c_data_collection"])?$_POST["c_data_collection"]:"";
$c_merchandising = isset($_POST["c_merchandising"])?$_POST["c_merchandising"]:"";
$c_acting = isset($_POST["c_acting"])?$_POST["c_acting"]:"";
$c_modeling = isset($_POST["c_modeling"])?$_POST["c_modeling"]:"";
$c_team_lead = isset($_POST["c_team_lead"])?$_POST["c_team_lead"]:"";
if(isset($_POST["isSubmitted"])){
	$sql =
	"SELECT usr.userGUID, CONCAT(usr.firstName,' ',usr.lastName) AS userName, ".
	"usr.email, ".
	"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Home Telephone Number' AND cv.idNum = usr.userGUID) AS homePhone, ".
	"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Mobile Telephone Number' AND cv.idNum = usr.userGUID) AS mobilePhone, ".
	"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Province' AND cv.idNum = usr.userGUID) AS Province, ".
	"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'City' AND cv.idNum = usr.userGUID) AS City, ".
	"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Gender' AND cv.idNum = usr.userGUID) AS Gender ".
	"FROM users usr ".
	"WHERE usr.userStatus = 'ACTIVE' ";  
	$sql .= (strlen($c_name) > 0?"AND (usr.firstName LIKE '%".$c_name."%' OR usr.lastName LIKE '%".$c_name."%' ) ":"");
	$sql .= (strlen($c_email) > 0?"AND usr.email LIKE '%".$c_email."%' ":"");
	if(is_array($c_prov)){
		$sql .=
		"AND ".(count($c_prov) > 1?"(":"");
		$numprovs = 0;
		foreach($c_prov as $prov){
			$numprovs++;
			$sql .=
			($numprovs > 1?"OR ":"").
			"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Province' AND cv.idNum = usr.userGUID) = '".$prov."' ";
		}
		$sql .=(count($c_prov) > 1?") ":" ");
	}
	if(strlen($c_city) > 0){
		$arrCities = explode(",",$c_city);
		$sql .=
		"AND ".(count($arrCities) > 1?"(":"");
		$numcities = 0;
		foreach($arrCities as $city){
			$numcities++;
			$sql .=
			($numcities > 1?"OR ":"").
			"((SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE (ff.filterName = 'Work Cities') AND cv.idNum = usr.userGUID) LIKE '%".trim($city)."%' ".
			"OR (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE (ff.filterName = 'City') AND cv.idNum = usr.userGUID) LIKE '%".trim($city)."%') ";
		}
		$sql .=(count($arrCities) > 1?") ":" ");
	}
	//Check for gender
	if(strlen($c_gender) > 0){  //Meaning we have a POST value for Gender filter
		if(strcasecmp($c_gender,"Both") != 0){  //Meaning that the value is either "Male" or "Female"
			$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Gender' AND cv.idNum = usr.userGUID) = '".trim($c_gender)."' ";
		}
	}
	if(strlen($c_age_from) > 0  && is_numeric($c_age_from)){
		$sql .=
			"AND (SELECT DATE_FORMAT(fieldValue, '%Y-%m-%d') FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Birthday' AND cv.idNum = usr.userGUID) < DATE(NOW() - INTERVAL ".$c_age_from." YEAR) ";
	}
	if(strlen($c_age_to) > 0 && is_numeric($c_age_to)){
		$sql .=
			"AND (SELECT DATE_FORMAT(fieldValue, '%Y-%m-%d') FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Birthday' AND cv.idNum = usr.userGUID) > DATE(NOW() - INTERVAL ".$c_age_to." YEAR) ";
	}
	if(strlen($c_serving_license) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Serving License' AND cv.idNum = usr.userGUID) = '".trim($c_serving_license)."' ";
	}
	if(strlen($c_food_handling) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Food Handling' AND cv.idNum = usr.userGUID) = '".trim($c_food_handling)."' ";
	}
	if(strlen($c_own_vehicle) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Have Vehicle' AND cv.idNum = usr.userGUID) = '".trim($c_own_vehicle)."' ";
	}
	if(is_array($c_languages)){
		echo "Languages is an array<br />";
		$sql .=
		"AND ".(count($c_languages) > 1?"(":"");
		$numlangs = 0;
		foreach($c_languages as $lang){
			$numlangs++;
			$sql .=
			($numlangs > 1?"OR ":"").
			"(SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Languages Spoken' AND cv.idNum = usr.userGUID) LIKE '%".$lang."%' ";
		}
		//Now we check to see if there is anything in the Languages (Other) field
		if(strlen($c_languages_other) > 0){
			$sql .=
				"OR (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'languageOther' AND cv.idNum = usr.userGUID) = '".trim($c_language_other)."' ";
			$numlangs++;
		}
		$sql .=(count($c_languages) > 1?") ":" ");
		//$sql .=(count($numlangs) > 1?") ":" ");
	}else{
		//Now we check to see if there is anything in the Languages (Other) field
		if(strlen($c_languages_other) > 0){
			$sql .=
				"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'languageOther' AND cv.idNum = usr.userGUID) LIKE '%".trim($c_languages_other)."%' ";
			$numlangs++;
		}
	}
	//Height Requirement
	if(strlen($c_height_feet) > 0  && is_numeric($c_height_feet)){
		$height_inches = ($c_height_feet * 12);
		if(strlen($c_height_inches) > 0  && is_numeric($c_height_inches)){
			$height_inches += $c_height_inches;
		}
		$sql .=
			"AND (SELECT ((MID(fieldValue,1,1) * 12) + MID(fieldValue,5,2)) FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Height' AND cv.idNum = usr.userGUID) >= ".$height_inches." ";
	}
	//Hair Colour
	if(strlen($c_hair_colour) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Hair Colour' AND cv.idNum = usr.userGUID) = '".trim($c_hair_colour)."' ";
	}
	//Uniform Size
	if(strlen($c_uniform_size) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Uniform' AND cv.idNum = usr.userGUID) = '".trim($c_uniform_size)."' ";
	}
	//Sales Experience
	if(strlen($c_sales_experience) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Sales Experience' AND cv.idNum = usr.userGUID) = '".trim($c_sales_experience)."' ";
	}
	//Experience working with Children
	if(strlen($c_work_children) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Experience with Children' AND cv.idNum = usr.userGUID) = '".trim($c_work_children)."' ";
	}
	//Food and Beverage Experience
	if(strlen($c_food_beverage) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Food and Beverage Experience' AND cv.idNum = usr.userGUID) = '".trim($c_food_beverage)."' ";
	}
	//Technology Experience
	if(strlen($c_technology) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Experience with Technology' AND cv.idNum = usr.userGUID) = '".trim($c_technology)."' ";
	}
	//Alcohol Sampling
	if(strlen($c_alcohol_sampling) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Alcohol Sampling Experience' AND cv.idNum = usr.userGUID) = '".trim($c_alcohol_sampling)."' ";
	}
	//Data Collection
	if(strlen($c_data_collection) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Data Collection Experience' AND cv.idNum = usr.userGUID) = '".trim($c_data_collection)."' ";
	}
	//Merchandising Experience
	if(strlen($c_merchandising) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Merchandising Experience' AND cv.idNum = usr.userGUID) = '".trim($c_merchandising)."' ";
	}
	//Acting Experience
	if(strlen($c_acting) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Acting Experience' AND cv.idNum = usr.userGUID) = '".trim($c_acting)."' ";
	}
	//Modeling Experience
	if(strlen($c_modeling) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'Model' AND cv.idNum = usr.userGUID) = '".trim($c_modeling)."' ";
	}
	//Team Lead
	if(strlen($c_team_lead) > 0){
		$sql .=
			"AND (SELECT fieldValue FROM formCandidateDetailsValues cv INNER JOIN formFields ff ON cv.fieldID = ff.fieldID WHERE ff.filterName = 'What Roles have you previously worked' AND cv.idNum = usr.userGUID) LIKE '%Team Lead%' ";
	}
	//echo $sql."<br />";
	if(strcasecmp($returnType,"FILE") == 0){
		if(exportSearchResults($sql)){
			$userDetails = getSearchResults($sql);
		}else{
			$returnType = "DISPLAY";
			$userDetails .=
			"		<p style=\"font-weight:bold;\">No matching candidates found</p>\r\n";
		}
	}else{
		$userDetails = getSearchResults($sql);
	}
}
if(strcasecmp($returnType,"DISPLAY") == 0){
	$content = displaySearchForm($userDetails);
	include("header.php");

	echo $content;

	include("footer.php");
}


function getSearchResults($sql){
	global $link;
	$userDetails = "";
	if($result = $link->query($sql)){
		$userDetails = 
		"<div>\r\n".
		"	<span style=\"font-weight:bold;font-size:14px;\">Search Results</span>\r\n".
		($result->num_rows > 0?"	<input type=\"button\" class=\"btn_blue\" value=\"Export To CSV\" onclick=\"$('#returnType').val('FILE');$('#candidate_search').submit();\" />\r\n":"").
		"<br /><br />\r\n".
		"</div>\r\n".
		"<div class=\"clear\"></div>\r\n";
		if($result->num_rows > 0){
			$userDetails .= 
			"<table class=\"tbl_bordered\" style=\"width:90%\">\r\n".
			"	<tr>\r\n".
			"		<th>Candidate Name</th>\r\n".
			"		<th>Email</th>\r\n".
			"		<th>Contact Number</th>\r\n".
			"		<th>Candidate Province</th>\r\n".
			"		<th>Candidate City</th>\r\n".
			"		<th>Gender</th>\r\n".
			"	</tr>\r\n";
			while($row = $result->fetch_assoc()){
				$userDetails .= 
				"	<tr>\r\n".
				"		<td><a href=\"myprofile.php?GUID=".$row["userGUID"]."\">".$row["userName"]."</a></td>\r\n".
				"		<td><a href=\"mailto:".$row["email"]."\">".$row["email"]."</a></td>\r\n".
				"		<td>".(strlen($row["homePhone"]) > 0?$row["homePhone"]." (Home)<br />":"").(strlen($row["mobilePhone"]) > 0?$row["mobilePhone"]." (Cell)":"")."</td>\r\n".
				"		<td>".(strlen($row["Province"]) > 0?$row["Province"]:"&nbsp;")."</td>\r\n".
				"		<td>".(strlen($row["City"]) > 0?$row["City"]:"&nbsp;")."</td>\r\n".
				"		<td>".(strlen($row["Gender"]) > 0?$row["Gender"]:"&nbsp;")."</td>\r\n".
				"	</tr>\r\n";
			}
			$userDetails .=
			"</table>\r\n";
			//$userDetails .= "<br />".$sql."<br />";
		}else{
			$userDetails .=
			"		<p style=\"font-weight:bold;\">No matching candidates found</p>\r\n";
			//$userDetails .= "<br />".$sql."<br />";
		}
	}else{
		$userDetails .=
		"		<p style=\"font-weight:bold;\">No matching candidates found</p>\r\n";
	}	
	
	return $userDetails;
}

function cleanData(&$str){ 
	if($str == 't') $str = 'TRUE'; 
	if($str == 'f') $str = 'FALSE'; 
	/*
	if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) { 
		$str = "'$str"; 
	} 
	*/
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

function exportSearchResults($sql){
	global $link;
	$retval = false;
	$filename = "Candidate_Search_Results_".date("Y-m-d").".csv";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			$retval = true;
			header("Content-Disposition: attachment; filename=\"".$filename."\""); 
			header("Content-Type: application/vnd.ms-excel;"); 
			$out = fopen("php://output", 'w'); 
			$flag = false;
			$colHeads = array("Candidate Name","Email","Contact Number","Candidate Province","Candidate City","Gender");
			fputcsv($out,$colHeads);
			while($row = $result->fetch_assoc()){
				unset($row["userGUID"]);
				
				$arrResult["cName"] = $row["userName"];
				$arrResult["cEmail"] = $row["email"];
				$arrResult["ContactNumbers"] = (strlen($row["homePhone"]) > 0?$row["homePhone"]." (Home) ":"").(strlen($row["mobilePhone"]) > 0?$row["mobilePhone"]." (Cell)":"");
				$arrResult["cProv"] = $row["Province"];
				$arrResult["cCity"] = $row["City"];
				$arrResult["cGender"] = $row["Gender"];
				array_walk($arrResult, 'cleanData'); 
				fputcsv($out, array_values($arrResult), ',', '"'); 
			}
			fclose($out);
		}
	}
	return $retval;
}

function displaySearchForm($userDetails){
	global 	$returnType,$c_name,$c_email,$c_prov,$c_city,$c_gender,$c_age_from,
			$c_age_to,$c_serving_license,$c_food_handling,$c_own_vehicle,$c_languages,$c_languages_other,$c_height_feet,
			$c_height_inches,$c_hair_colour,$c_uniform_size,$c_sales_experience,$c_work_children,$c_food_beverage,
			$c_technology,$c_alcohol_sampling,$c_data_collection,$c_merchandising,$c_acting,$c_modeling,$c_team_lead;
	$retval = "";
	
	$retval .=
	"<div style=\"width:400px;\"><h3>Candidate Search</h3></div>\r\n".
	"<form name=\"candidate_search\" id=\"candidate_search\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">\r\n".
	"	<fieldset>\r\n".
	"		<label for=\"c_name\">By Name(First,Last)</label>\r\n".
	"		<input type=\"text\" name=\"c_name\" id=\"c_name\" value=\"".$c_name."\" class=\"style_text_field\" />\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_name\">By Email</label>\r\n".
	"		<input type=\"text\" name=\"c_email\" id=\"c_email\" value=\"".$c_email."\" class=\"style_text_field\" />\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_prov\">Province</label>\r\n".
	"		<select name=\"c_prov[]\" id=\"c_prov\" class=\"style_multi_select\" size=\"5\" multiple/>\r\n".
	"			".generateProvinceSelect($c_prov)."\r\n".
	"		</select>\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_city\">City</label>\r\n".
	"		<input type=\"text\" name=\"c_city\" id=\"c_city\" value=\"".(strlen($c_city) > 0?$c_city:"City1,City2")."\" class=\"style_text_field\" onfocus=\"clearField('c_city', 'City1,City2');\" />\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_gender\">Gender</label>\r\n".
	"		<input type=\"radio\" name=\"c_gender\" value=\"Male\"".(strcasecmp($c_gender,"Male") == 0?" checked ":"").">Male \r\n".
	"		<input type=\"radio\" name=\"c_gender\" value=\"Female\"".(strcasecmp($c_gender,"Female") == 0?" checked ":"").">Female \r\n".
	"		<input type=\"radio\" name=\"c_gender\" value=\"Both\"".(strcasecmp($c_gender,"Both") == 0?" checked ":"").">Either\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_age_from\">Age Range</label>\r\n".
	"		From <input type=\"text\" name=\"c_age_from\" id=\"c_age_from\" value=\"".$c_age_from."\" class=\"style_text_field\" style=\"width:30px;\" /> years &nbsp;\r\n". 
	"		To <input type=\"text\" name=\"c_age_to\" id=\"c_age_to\" value=\"".$c_age_to."\" class=\"style_text_field\" style=\"width:30px;\" /> years &nbsp;\r\n". 
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_serving_license\">Serving License Required</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_serving_license\" id=\"c_serving_license\" value=\"1\" ".(strcasecmp($c_serving_license,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_food_handling\">Food Handling Required</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_food_handling\" id=\"c_food_handling\" value=\"1\"".(strcasecmp($c_food_handling,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_drivers_license\">Valid Drivers License Required</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_drivers_license\" id=\"c_drivers_license\" value=\"1\"".(strcasecmp($c_drivers_license,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_own_vehicle\">Access to own Vehicle Required</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_own_vehicle\" id=\"c_own_vehicle\" value=\"1\"".(strcasecmp($c_own_vehicle,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_languages\">Languages Spoken</label>\r\n".
	"		<select name=\"c_languages[]\" id=\"c_languages\" class=\"style_multi_select\" size=\"5\" multiple/>\r\n".
	"			".generateMultiSelect(18, (is_array($c_languages)?implode("~",$c_languages):$c_languages))."\r\n".
	"		</select>\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_languages_other\">Languages Spoken (Other)</label>\r\n".
	"		<input type=\"text\" name=\"c_languages_other\" id=\"c_languages_other\" value=\"".(strlen($c_languages_other) > 0?$c_languages_other:"")."\" class=\"style_text_field\" onfocus=\"clearField('c_languages_other', 'Languages (Other)');\" />\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_age_from\">Height Requirement</label>\r\n".
	"		<input type=\"text\" name=\"c_height_feet\" id=\"c_height_feet\" value=\"".$c_height_feet."\" class=\"style_text_field\" style=\"width:30px;\" /> ft &nbsp;\r\n". 
	"		<input type=\"text\" name=\"c_height_inches\" id=\"height_inches\" value=\"".$c_height_inches."\" class=\"style_text_field\" style=\"width:30px;\" /> inches &nbsp;\r\n". 
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_hair_colour\">Hair Colour</label>\r\n".
	"		<select name=\"c_hair_colour\" id=\"c_hair_colour\" class=\"style_select\"/>\r\n".
	"			".generateSelectOptions(16, $c_hair_colour)."\r\n".
	"		</select>\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_uniform_size\">Uniform Size</label>\r\n".
	"		<select name=\"c_uniform_size\" id=\"c_uniform_size\" class=\"style_select\"/>\r\n".
	"			".generateSelectOptions(13, $c_uniform_size)."\r\n".
	"		</select>\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_sales_experience\">Sales Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_sales_experience\" id=\"c_sales_experience\" value=\"1\"".(strcasecmp($c_sales_experience,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_work_children\">Experience working with Children</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_work_children\" id=\"c_work_children\" value=\"1\"".(strcasecmp($c_work_children,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_food_beverage\">Food and Beverage Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_food_beverage\" id=\"c_food_beverage\" value=\"1\"".(strcasecmp($c_food_beverage,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_technology\">Experience working with Technology</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_technology\" id=\"c_technology\" value=\"1\"".(strcasecmp($c_technology,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_alcohol_sampling\">Alcohol Sampling Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_alcohol_sampling\" id=\"c_alcohol_sampling\" value=\"1\"".(strcasecmp($c_alcohol_sampling,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_data_collection\">Data Collection Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_data_collection\" id=\"c_data_collection\" value=\"1\"".(strcasecmp($c_data_collection,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_merchandising\">Merchandising Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_merchandising\" id=\"c_merchandising\" value=\"1\"".(strcasecmp($c_merchandising,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_acting\">Acting Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_acting\" id=\"c_acting\" value=\"1\"".(strcasecmp($c_acting,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_modeling\">Modeling Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_modeling\" id=\"c_modeling\" value=\"1\"".(strcasecmp($c_modeling,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"		<label for=\"c_team_lead\">Team Lead Experience</label>\r\n".
	"		<input type=\"checkbox\" name=\"c_team_lead\" id=\"c_team_lead\" value=\"1\"".(strcasecmp($c_team_lead,"1") == 0?" checked ":"").">Yes\r\n".
	"		<div class=\"clear\"></div>\r\n".
	"	</fieldset>\r\n".
	"	<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
	"	<input type=\"hidden\" name=\"returnType\" id=\"returnType\" value=\"".$returnType."\" />\r\n".
	"	<div style=\"width:422px;text-align:right;\">\r\n".
	"		<a href=\"".$_SERVER["PHP_SELF"]."\" class=\"btn_blue\" style=\"float:left;\">Reset Search</a>\r\n".
	"		<input type=\"button\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"Search\" onclick=\"$('#returnType').val('DISPLAY'); $('#candidate_search').submit();\" />\r\n".
	"	</div>\r\n".
	"</form>\r\n".
	"<div class=\"clear\"><br /></div>\r\n".
	$userDetails;
	
	return $retval;
}	
?>