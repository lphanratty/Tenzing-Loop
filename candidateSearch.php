<?php
include_once("init.php");
include_once("include/inc_form_functions.php");
$userDetails = "";
$clearSessionSearch = isset($_GET["CS"]) && strcasecmp($_GET["CS"],"YES") == 0?true:false;
if($clearSessionSearch){
	clearSessionSearch();
}
$returnType = isset($_POST["returnType"])?$_POST["returnType"]:"DISPLAY";

$c_name = (isset($_SESSION["C_NAME"])?$_SESSION["C_NAME"]:"");
$c_email = (isset($_SESSION["C_EMAIL"])?$_SESSION["C_EMAIL"]:"");
$c_prov = (isset($_SESSION["C_PROV"])?$_SESSION["C_PROV"]:"");
if(isset($_SESSION["C_CITY"])){
	if(strcasecmp($_SESSION["C_CITY"],"City1,City2") == 0){
		$c_city = "";
	}else{
		$c_city = $_SESSION["C_CITY"];
	}
}else{
	$c_city = "";
}

//Get the list of form fields and their ID number
$sql = 
"SELECT fieldID, filterName ".
"FROM formfields ";
$formFields = array();
if($result = $link->query($sql)){
	if(mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)){
			$formFields[$row["filterName"]] = $row["fieldID"];
		}
	}
}

$c_gender = (isset($_SESSION["C_GENDER"])?$_SESSION["C_GENDER"]:"");
$c_age_from = (isset($_SESSION["C_AGE_FROM"])?$_SESSION["C_AGE_FROM"]:"");
$c_age_to = (isset($_SESSION["C_AGE_TO"])?$_SESSION["C_AGE_TO"]:"");
$c_serving_license = (isset($_SESSION["C_SERVING_LICENSE"])?$_SESSION["C_SERVING_LICENSE"]:"");
$c_food_handling = (isset($_SESSION["C_FOOD_HANDLING"])?$_SESSION["C_FOOD_HANDLING"]:"");
$c_own_vehicle = (isset($_SESSION["C_OWN_VEHICLE"])?$_SESSION["C_OWN_VEHICLE"]:"");
$c_drivers_license = (isset($_SESSION["C_DRIVERS_LICENSE"])?$_SESSION["C_DRIVERS_LICENSE"]:"");
$c_languages = (isset($_SESSION["C_LANGUAGES"])?$_SESSION["C_LANGUAGES"]:"");
$c_languages_other = (isset($_SESSION["C_LANGUAGES_OTHER"])?$_SESSION["C_LANGUAGES_OTHER"]:"");
$c_height_feet = (isset($_SESSION["C_HEIGHT_FEET"])?$_SESSION["C_HEIGHT_FEET"]:"");
$c_height_inches = (isset($_SESSION["C_HEIGHT_INCHES"])?$_SESSION["C_HEIGHT_INCHES"]:"");
$c_hair_colour = (isset($_SESSION["C_HAIR_COLOUR"])?$_SESSION["C_HAIR_COLOUR"]:"");
$c_uniform_size = (isset($_SESSION["C_UNIFORM_SIZE"])?$_SESSION["C_UNIFORM_SIZE"]:"");
$c_sales_experience = (isset($_SESSION["C_SALES_EXPERIENCE"])?$_SESSION["C_SALES_EXPERIENCE"]:"");
$c_work_children = (isset($_SESSION["C_WORK_CHILDREN"])?$_SESSION["C_WORK_CHILDREN"]:"");
$c_food_beverage = (isset($_SESSION["C_FOOD_BEVERAGE"])?$_SESSION["C_FOOD_BEVERAGE"]:"");
$c_technology = (isset($_SESSION["C_TECHNOLOGY"])?$_SESSION["C_TECHNOLOGY"]:"");
$c_alcohol_sampling = (isset($_SESSION["C_ALCOHOL_SAMPLING"])?$_SESSION["C_ALCOHOL_SAMPLING"]:"");
$c_data_collection = (isset($_SESSION["C_DATA_COLLECTION"])?$_SESSION["C_DATA_COLLECTION"]:"");
$c_merchandising = (isset($_SESSION["C_MERCHANDISING"])?$_SESSION["C_MERCHANDISING"]:"");
$c_acting = (isset($_SESSION["C_ACTING"])?$_SESSION["C_ACTING"]:"");
$c_modeling = (isset($_SESSION["C_MODELING"])?$_SESSION["C_MODELING"]:"");
$c_team_lead = (isset($_SESSION["C_TEAM_LEAD"])?$_SESSION["C_TEAM_LEAD"]:"");

//echo "Search Active is ".$_SESSION["SEARCH_ACTIVE"]."<br />";
if(isset($_POST["isSubmitted"]) || (isset($_SESSION["SEARCH_ACTIVE"]) && strcasecmp($_SESSION["SEARCH_ACTIVE"],"YES") == 0)){
	$_SESSION["SEARCH_ACTIVE"] = "YES";
	if(isset($_POST["isSubmitted"])){
	
		// Reset our variables to the POST variables
		$c_name = isset($_POST["c_name"])?$_POST["c_name"]:"";
		$c_email = isset($_POST["c_email"])?$_POST["c_email"]:"";
		$c_prov = isset($_POST["c_prov"])?$_POST["c_prov"]:"";
		if(isset($_POST["c_city"])){
			if(strcasecmp($_POST["c_city"],"City1,City2") == 0){
				$c_city = "";
			}else{
				$c_city = $_POST["c_city"];
			}
		}
		$c_gender = isset($_POST["c_gender"])?$_POST["c_gender"]:"";
		$c_age_from = isset($_POST["c_age_from"])?$_POST["c_age_from"]:"";
		$c_age_to = isset($_POST["c_age_to"])?$_POST["c_age_to"]:"";
		$c_serving_license = isset($_POST["c_serving_license"])?$_POST["c_serving_license"]:"";
		$c_food_handling = isset($_POST["c_food_handling"])?$_POST["c_food_handling"]:"";
		$c_own_vehicle = isset($_POST["c_own_vehicle"])?$_POST["c_own_vehicle"]:"";
		$c_drivers_license = isset($_POST["c_drivers_license"])?$_POST["c_drivers_license"]:"";
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


		$_SESSION["C_NAME"] = isset($_POST["c_name"])?$_POST["c_name"]:"";
		$_SESSION["C_EMAIL"] = isset($_POST["c_email"])?$_POST["c_email"]:"";
		$_SESSION["C_PROV"] = isset($_POST["c_prov"])?$_POST["c_prov"]:"";
		$_SESSION["C_CITY"] = isset($_POST["c_city"])?$_POST["c_city"]:"";
		$_SESSION["C_GENDER"] = isset($_POST["c_gender"])?$_POST["c_gender"]:"";
		$_SESSION["C_AGE_FROM"] = isset($_POST["c_age_from"])?$_POST["c_age_from"]:"";
		$_SESSION["C_AGE_TO"] = isset($_POST["c_age_to"])?$_POST["c_age_to"]:"";
		$_SESSION["C_SERVING_LICENSE"] = isset($_POST["c_serving_license"])?$_POST["c_serving_license"]:"";
		$_SESSION["C_FOOD_HANDLING"] = isset($_POST["c_food_handling"])?$_POST["c_food_handling"]:"";
		$_SESSION["C_OWN_VEHICLE"] = isset($_POST["c_own_vehicle"])?$_POST["c_own_vehicle"]:"";
		$_SESSION["C_DRIVERS_LICENSE"] = isset($_POST["c_drivers_license"])?$_POST["c_drivers_license"]:"";
		$_SESSION["C_LANGUAGES"] = isset($_POST["c_languages"])?$_POST["c_languages"]:"";
		$_SESSION["C_LANGUAGES_OTHER"] = isset($_POST["c_languages_other"])?$_POST["c_languages_other"]:"";
		$_SESSION["C_HEIGHT_FEET"] = isset($_POST["c_height_feet"])?$_POST["c_height_feet"]:"";
		$_SESSION["C_HEIGHT_INCHES"] = isset($_POST["c_height_inches"])?$_POST["c_height_inches"]:"";
		$_SESSION["C_HAIR_COLOUR"] = isset($_POST["c_hair_colour"])?$_POST["c_hair_colour"]:"";
		$_SESSION["C_UNIFORM_SIZE"] = isset($_POST["c_uniform_size"])?$_POST["c_uniform_size"]:"";
		$_SESSION["C_SALES_EXPERIENCE"] = isset($_POST["c_sales_experience"])?$_POST["c_sales_experience"]:"";
		$_SESSION["C_WORK_CHILDREN"] = isset($_POST["c_work_children"])?$_POST["c_work_children"]:"";
		$_SESSION["C_FOOD_BEVERAGE"] = isset($_POST["c_food_beverage"])?$_POST["c_food_beverage"]:"";
		$_SESSION["C_TECHNOLOGY"] = isset($_POST["c_technology"])?$_POST["c_technology"]:"";
		$_SESSION["C_ALCOHOL_SAMPLING"] = isset($_POST["c_alcohol_sampling"])?$_POST["c_alcohol_sampling"]:"";
		$_SESSION["C_DATA_COLLECTION"] = isset($_POST["c_data_collection"])?$_POST["c_data_collection"]:"";
		$_SESSION["C_MERCHANDISING"] = isset($_POST["c_merchandising"])?$_POST["c_merchandising"]:"";
		$_SESSION["C_ACTING"] = isset($_POST["c_acting"])?$_POST["c_acting"]:"";
		$_SESSION["C_MODELING"] = isset($_POST["c_modeling"])?$_POST["c_modeling"]:"";
		$_SESSION["C_TEAM_LEAD"] = isset($_POST["c_team_lead"])?$_POST["c_team_lead"]:"";																						
	}
	
	$sql =
	"SELECT usr.userGUID, CONCAT(usr.firstName,' ',usr.lastName) AS userName, ".
	"usr.email, cvHomePhone.fieldValue AS homePhone, cvMobilePhone.fieldValue AS mobilePhone, ".
	"cvProvince.fieldValue AS Province, cvCity.fieldValue AS City, cvGender.fieldValue AS Gender ".
	"FROM users usr ".
	"LEFT JOIN formCandidateDetailsValues cvHomePhone ON cvHomePhone.idNum = usr.userGUID AND cvHomePhone.fieldID = ".$formFields["Home Telephone Number"]." ".
	"LEFT JOIN formCandidateDetailsValues cvMobilePhone ON cvMobilePhone.idNum = usr.userGUID AND cvMobilePhone.fieldID = ".$formFields["Mobile Telephone Number"]." ".
	"LEFT JOIN formCandidateDetailsValues cvProvince ON cvProvince.idNum = usr.userGUID AND cvProvince.fieldID = ".$formFields["Province"]." ".
	"LEFT JOIN formCandidateDetailsValues cvCity ON cvCity.idNum = usr.userGUID AND cvCity.fieldID = ".$formFields["City"]." ".
	"LEFT JOIN formCandidateDetailsValues cvGender ON cvGender.idNum = usr.userGUID AND cvGender.fieldID = ".$formFields["Gender"]." ";
	
	if(strlen($c_city) > 0){
		$arrCities = explode(",",$c_city);
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvWorkCity ON cvWorkCity.idNum = usr.userGUID AND (cvWorkCity.fieldID = ".$formFields["Work Cities"]." OR cvWorkCity.fieldID = ".$formFields["City"].") ".
		"AND ".(count($arrCities) > 1?"(":"");
		$numcities = 0;
		foreach($arrCities as $city){
			$numcities++;
			$sql .=
			($numcities > 1?"OR ":"").
			"cvWorkCity.fieldValue LIKE '%".trim($city)."%' ";
		}
		$sql .=(count($arrCities) > 1?") ":" ");
	}
	if(is_array($c_prov)){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvProvMatch ON cvProvMatch.idNum = usr.userGUID AND cvProvMatch.fieldID = ".$formFields["Province"]." ".
		"AND ".(count($c_prov) > 1?"(":"");
		$numprovs = 0;
		foreach($c_prov as $prov){
			$numprovs++;
			$sql .=
			($numprovs > 1?"OR ":"").
			"cvProvMatch.fieldValue = '".$prov."' ";
		}
		$sql .=(count($c_prov) > 1?") ":" ");
	}
	if(strlen($c_own_vehicle) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvOwnVehicle ON cvOwnVehicle.idNum = usr.userGUID AND cvOwnVehicle.fieldID = ".$formFields["Have Vehicle"]." AND cvOwnVehicle.fieldValue = '".trim($c_own_vehicle)."' ";
	}
	//Check for gender
	if(strlen($c_gender) > 0){  //Meaning we have a POST value for Gender filter
		if(strcasecmp($c_gender,"Both") != 0){  //Meaning that the value is either "Male" or "Female"
			$sql .=
			"INNER JOIN formCandidateDetailsValues cvGenderMatch ON cvGenderMatch.idNum = usr.userGUID AND cvGenderMatch.fieldID = ".$formFields["Gender"]." AND cvGenderMatch.fieldValue = '".trim($c_gender)."' ";
		}
	}
	if(strlen($c_age_from) > 0  && is_numeric($c_age_from)){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvAgeFrom ON cvAgeFrom.idNum = usr.userGUID AND cvAgeFrom.fieldID = ".$formFields["Birthday"]." AND DATE_FORMAT(cvAgeFrom.fieldValue, '%Y-%m-%d') < DATE(NOW() - INTERVAL ".$c_age_from." YEAR) ";
	}
	if(strlen($c_age_to) > 0 && is_numeric($c_age_to)){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvAgeTo ON cvAgeTo.idNum = usr.userGUID AND cvAgeTo.fieldID = ".$formFields["Birthday"]." AND DATE_FORMAT(cvAgeTo.fieldValue, '%Y-%m-%d') > DATE(NOW() - INTERVAL ".$c_age_to." YEAR) ";
	}
	if(strlen($c_serving_license) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvServingLicense ON cvServingLicense.idNum = usr.userGUID AND cvServingLicense.fieldID = ".$formFields["Serving License"]." AND cvServingLicense.fieldValue = '".trim($c_serving_license)."' ";
	}
	if(strlen($c_food_handling) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvFoodHandling ON cvFoodHandling.idNum = usr.userGUID AND cvFoodHandling.fieldID = ".$formFields["Food Handling"]." AND cvFoodHandling.fieldValue = '".trim($c_food_handling)."' ";
	}
	if(is_array($c_languages)){
		//echo "Languages is an array<br />";
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvLang ON cvLang.idNum = usr.userGUID AND cvLang.fieldID = ".$formFields["Languages Spoken"]." ".
		"AND ".(count($c_languages) > 1?"(":"");
		$numlangs = 0;
		foreach($c_languages as $lang){
			$numlangs++;
			$sql .=
			($numlangs > 1?"OR ":"").
			"cvLang.fieldValue LIKE '%".trim($lang)."%' ";
		}
		$sql .=(count($c_languages) > 1?") ":" ");
		//Now we check to see if there is anything in the Languages (Other) field
		if(strlen($c_languages_other) > 0){
			$sql .=
			"INNER JOIN formCandidateDetailsValues cvLangOther ON cvLangOther.idNum = usr.userGUID AND cvLangOther.fieldID = ".$formFields["languageOther"]." AND cvLangOther.fieldValue LIKE '%".trim($c_languages_other)."%' ";
			$numlangs++;
		}
	}else{
		//Now we check to see if there is anything in the Languages (Other) field
		if(strlen($c_languages_other) > 0){
			$sql .=
			"INNER JOIN formCandidateDetailsValues cvLangOther ON cvLangOther.idNum = usr.userGUID AND cvLangOther.fieldID = ".$formFields["languageOther"]." AND cvLangOther.fieldValue LIKE '%".trim($c_languages_other)."%' ";
			$numlangs++;
		}
	}
	if(strlen($c_height_feet) > 0  && is_numeric($c_height_feet)){
		$height_inches = ($c_height_feet * 12);
		if(strlen($c_height_inches) > 0  && is_numeric($c_height_inches)){
			$height_inches += $c_height_inches;
		}
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvHeight ON cvHeight.idNum = usr.userGUID AND cvHeight.fieldID = ".$formFields["Height"]." AND ((MID(cvHeight.fieldValue,1,1) * 12) + MID(cvHeight.fieldValue,5,2)) >= ".$height_inches." ";
	}
	if(strlen($c_hair_colour) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvHairColour ON cvHairColour.idNum = usr.userGUID AND cvHairColour.fieldID = ".$formFields["Hair Colour"]." AND cvHairColour.fieldValue = '".trim($c_hair_colour)."' ";
	}
	//Uniform Size
	if(strlen($c_uniform_size) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvUniformSize ON cvUniformSize.idNum = usr.userGUID AND cvUniformSize.fieldID = ".$formFields["Uniform"]." AND cvUniformSize.fieldValue = '".trim($c_uniform_size)."' ";
	}
	//Sales Experience
	if(strlen($c_sales_experience) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvSalesExperience ON cvSalesExperience.idNum = usr.userGUID AND cvSalesExperience.fieldID = ".$formFields["Sales Experience"]." AND cvSalesExperience.fieldValue = '".trim($c_sales_experience)."' ";
	}
	if(strlen($c_work_children) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvWorkKids ON cvWorkKids.idNum = usr.userGUID AND cvWorkKids.fieldID = ".$formFields["Experience with Children"]." AND cvWorkKids.fieldValue = '".trim($c_work_children)."' ";
	}
	//Food and Beverage Experience
	if(strlen($c_food_beverage) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvFoodBeverage ON cvFoodBeverage.idNum = usr.userGUID AND cvFoodBeverage.fieldID = ".$formFields["Food and Beverage Experience"]." AND cvFoodBeverage.fieldValue = '".trim($c_food_beverage)."' ";
	}
	//Technology Experience
	if(strlen($c_technology) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvTechnology ON cvTechnology.idNum = usr.userGUID AND cvTechnology.fieldID = ".$formFields["Experience with Technology"]." AND cvTechnology.fieldValue = '".trim($c_technology)."' ";
	}
	//Alcohol Sampling
	if(strlen($c_alcohol_sampling) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvAlcoholSampling ON cvAlcoholSampling.idNum = usr.userGUID AND cvAlcoholSampling.fieldID = ".$formFields["Alcohol Sampling Experience"]." AND cvAlcoholSampling.fieldValue = '".trim($c_alcohol_sampling)."' ";
	}
	//Data Collection
	if(strlen($c_data_collection) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvData ON cvData.idNum = usr.userGUID AND cvData.fieldID = ".$formFields["Data Collection Experience"]." AND cvData.fieldValue = '".trim($c_data_collection)."' ";
	}
	//Merchandising Experience
	if(strlen($c_merchandising) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvMerchandising ON cvMerchandising.idNum = usr.userGUID AND cvMerchandising.fieldID = ".$formFields["Merchandising Experience"]." AND cvMerchandising.fieldValue = '".trim($c_merchandising)."' ";
	}
	//Acting Experience
	if(strlen($c_acting) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvActing ON cvActing.idNum = usr.userGUID AND cvActing.fieldID = ".$formFields["Acting Experience"]." AND cvActing.fieldValue = '".trim($c_acting)."' ";
	}
	//Modeling Experience
	if(strlen($c_modeling) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvModelling ON cvModelling.idNum = usr.userGUID AND cvModelling.fieldID = ".$formFields["Model"]." AND cvModelling.fieldValue = '".trim($c_modeling)."' ";
	}
	//Team Lead
	if(strlen($c_team_lead) > 0){
		$sql .=
		"INNER JOIN formCandidateDetailsValues cvTeamLead ON cvTeamLead.idNum = usr.userGUID AND cvTeamLead.fieldID = ".$formFields["What Roles have you previously worked"]." AND cvTeamLead.fieldValue LIKE '%Team Lead%' ";
	}
	$sql .=
	"WHERE usr.userStatus = 'ACTIVE' ";  
	$sql .= (strlen($c_name) > 0?"AND (usr.firstName LIKE '%".$c_name."%' OR usr.lastName LIKE '%".$c_name."%' ) ":"");
	$sql .= (strlen($c_email) > 0?"AND usr.email LIKE '%".$c_email."%' ":"");
	
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
	//echo $sql."<br />";
	
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
			$c_age_to,$c_serving_license,$c_food_handling,$c_own_vehicle,$c_drivers_license,$c_languages,$c_languages_other,$c_height_feet,
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
	"		<a href=\"".$_SERVER["PHP_SELF"]."?CS=YES\" class=\"btn_blue\" style=\"float:left;\">Reset Search</a>\r\n".
	"		<input type=\"button\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"Search\" onclick=\"$('#returnType').val('DISPLAY'); $('#candidate_search').submit();\" />\r\n".
	"	</div>\r\n".
	"</form>\r\n".
	"<div class=\"clear\"><br /></div>\r\n".
	$userDetails;
	
	return $retval;
}	

function clearSessionSearch(){
	$_SESSION["SEARCH_ACTIVE"] = "NO";
	$_SESSION["C_NAME"] = "";
	$_SESSION["C_EMAIL"] = "";
	$_SESSION["C_PROV"] = "";
	$_SESSION["C_CITY"] = "";
	$_SESSION["C_GENDER"] = "";
	$_SESSION["C_AGE_FROM"] = "";
	$_SESSION["C_AGE_TO"] = "";
	$_SESSION["C_SERVING_LICENSE"] = "";
	$_SESSION["C_FOOD_HANDLING"] = "";
	$_SESSION["C_OWN_VEHICLE"] = "";
	$_SESSION["C_DRIVERS_LICENSE"] = "";
	$_SESSION["C_LANGUAGES"] = "";
	$_SESSION["C_LANGUAGES_OTHER"] = "";
	$_SESSION["C_HEIGHT_FEET"] = "";
	$_SESSION["C_HEIGHT_INCHES"] = "";
	$_SESSION["C_HAIR_COLOUR"] = "";
	$_SESSION["C_UNIFORM_SIZE"] = "";
	$_SESSION["C_SALES_EXPERIENCE"] = "";
	$_SESSION["C_WORK_CHILDREN"] = "";
	$_SESSION["C_FOOD_BEVERAGE"] = "";
	$_SESSION["C_TECHNOLOGY"] = "";
	$_SESSION["C_ALCOHOL_SAMPLING"] = "";
	$_SESSION["C_DATA_COLLECTION"] = "";
	$_SESSION["C_MERCHANDISING"] = "";
	$_SESSION["C_ACTING"] = "";
	$_SESSION["C_MODELING"] = "";
	$_SESSION["C_TEAM_LEAD"] = "";
}
?>