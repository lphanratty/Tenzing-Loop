<?php
//Validation functions
function isValidLength($val, $minlength=1){
	$retval = true;
	if(strlen(trim($val)) < $minlength){
		$retval = false;	
	}
	return $retval;	
}

function isValidEmail($email){
	//return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	//$okay = preg_match('/^[A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{2,4}$/',$email); 
	$okay = preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email);
	if($okay){
		return true;
	}else{
		return false;
	}
	//return preg_match("[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

function isValidPhone($phone){
	//$okay = preg_match('/^(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/',$phone);
	$okay = preg_match('/^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/',$phone);
	if($okay){
		return true;
	}else{
		return false;
	}	
}

function matchConfirm($val1,$val2){
	$retval = false;
	if(strcmp(trim($val1), trim($val2)) === 0){
		$retval = true;	
	}
	/*	
	$retval = true;
	if($val1 !== $val2){
		$retval = false;	
	}	
	*/
	return $retval;
}
function isNumeric($val,$minval=0,$maxval=0){
	$retval=true;
	if(!is_numeric($val)){
		$retval = false;	
	}elseif($val < $minval){
		$retval = false;		
	}elseif($maxval > 0 && $val > $maxval){
		$retval = false;		
	}
	return $retval;
}

function isValidDate($data){
	if (date('Y-m-d', strtotime($data)) == $data) {
        return true;
    } else {
        return false;
    }
}

function numericStripped($num){
	$okstring = "0123456789.";
}

function alreadyExists($table,$fieldname,$val, $addsql=""){
	global $db;
	$retval = false;
	$sql = "SELECT * FROM ".$table." WHERE ".$fieldname." = '".$val."'";
	if(strlen($addsql) > 0){
		$sql .= " ".$addsql;
	}
	if($fieldval = $db->get_row($sql)){
		$retval = true;	
	}
	/*
	if($result = mysql_query($sql)){
		if(mysql_num_rows($result) > 0){
			$retval = true;	
		}	
	}
	*/
	return $retval;	
}
function addCity($cityname,$provid){
	$ZeroProvid = 0;
	$retval = 0;
	$sql = "SELECT * FROM cities ".
		   "WHERE city_name = '".$cityname."' ".
		   "ORDER BY province_id";
	if($result = mysql_query($sql)){
		if(mysql_num_rows($result) > 0){
			while($row = mysql_fetch_assoc($result)){
				if($row["province_id"] == 0){
						$ZeroProvid = $row["city_id"];
				}
				if($provid = $row["province_id"] && $provid > 0){
					$retval = $row["city_id"];	
				}	
			}
			if($retval == 0 && $ZeroProvid > 0){
				$retval = $ZeroProvid;	
			}	
		}else{
			$sql = "INSERT INTO cities ".
				   "SET city_name = '".$cityname."', ".
				   "province_id = ".$provid;
			if(mysql_query($sql)){
				if(mysql_affected_rows() == 1){
					$retval = mysql_insert_id();	
				}	
			}	
		}	
	}
	return $retval;
}

function generateSelectList($fieldname,$table,$valfield,$textfield,$addSQL="",$addselectrow=false,$valtomatch="",$novaluetype="STRING",$css_class="",$css_style="",$js=""){
	global $db;
	$retval = "";
	//echo "valfield is ".$valfield."<br />";
	$js_text = ($js != ""?" ".$js." ":"");
	$css_text = ($css_class != ""?" class=\"".$css_class."\"":"");
	$css_text .= ($css_style != ""?" style=\"".$css_style."\"":"");
	if($valtomatch == ""){
		$valtomatch = (strtoupper($novaluetype) == "STRING"?"":0);	
	}
	$sql = 	"SELECT * FROM ".$table.
			(strlen($addSQL) > 0?" ".$addSQL." ":"");
	//echo $sql."<br />";
	if($result = $db->get_results($sql)){
		$retval .= "<select name=\"".$fieldname."\" id=\"".$fieldname."\"".$js_text.$css_text.">\n";
		if($addselectrow){
			if(strtoupper($novaluetype) == "STRING"){
				$retval .= "<option value=\"\">--Please Select--</option>\n";
			}else if(strtoupper($novaluetype) == "NUMBER"){
				$retval .= "<option value=0>--Please Select--</option>\n";
			}
		}
		foreach($result as $row){
			//echo "valfield value is ".$row->$valfield."<br />";
			$selecttext = ($row->$valfield == $valtomatch?" SELECTED ":"");
			$retval .= "<option value=".$row->$valfield.$selecttext.">".$row->$textfield."</option>\n"; 		
		}
		$retval .= "</select>";
	}else{
		//$db->debug();
		$retval .= "<select name=\"".$fieldname."\" id=\"".$fieldname."\"".$js_text.$css_text.">\n";
		$retval .= "<option value=\"\">No Value Found</option>\n";
		$retval .= "</select>";	
	}
	
	return $retval;
}

function generatePassword($length=6,$level=2){

   list($usec, $sec) = explode(' ', microtime());
   srand((float) $sec + ((float) $usec * 100000));

   $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
   $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

   $password  = "";
   $counter   = 0;

   while ($counter < $length) {
     $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

     // All character must be different
     if (!strstr($password, $actChar)) {
        $password .= $actChar;
        $counter++;
     }
   }

   return $password;
}

function generateYearsSelect($numyears, $matchval, $offsetBack=0){
	$retval = "";
	$startYear = ($offsetBack == 0?date("Y"):date("Y") - $offsetBack);
	for($i=$startYear;$i < $startYear + $numyears;$i++){
		$retval .= "<option value=".$i.($matchval == $i?" selected ":"").">".$i."</option>\r\n";
	}
	return $retval;
}

function generateMonthSelect($matchval,$fullname=false){
	$retval = "";
	for($i=1;$i<13;$i++){
		$monthname = date(($fullname===true?"F":"M"), mktime(0, 0, 0, $i, 1, 2000));
		$monthval = date("m", mktime(0, 0, 0, $i, 1, 2000));
		$retval .= "<option value=".$monthval.($matchval == $monthval?" selected ":"").">".$monthname."</option>\r\n";
	}
	return $retval;
}

function generateDaySelect($matchval,$fullname=false){
	$retval = "";
	for($i=1;$i<32;$i++){
		$dayname = date(($fullname===true?"jS":"d"), mktime(0, 0, 0, 1, $i, 2000));
		$dayval = date("d", mktime(0, 0, 0, 1, $i, 2000));
		$retval .= "<option value=".$dayval.($matchval == $dayval?" selected ":"").">".$dayname."</option>\r\n";
	}
	return $retval;
}

function generateHourSelect($matchval="",$hourtostart=8,$intervalInMinutes = 15, $militaryTime=true){
	$retval = "";
	for($i=$hourtostart;$i<24;$i++){
		if($i >= 24){
			$newhour = $i - 24;
			if($newhour == 0){
				$opthour = "0".$newhour;	
				$opttext = ($militaryTime?"00":"12:");
			}elseif($newhour < 10){
				$opthour = "0".$newhour;	
				$opttext = ($militaryTime?"0".$newhour:$newhour.":");
			}else{
				$opthour = $newhour;	
				$opttext = ($militaryTime?$newhour:$newhour.":");
			}
		}elseif($i <10){
			if($i == 0){
				$opttext = ($militaryTime?"00":"12:");
			}else{
				$opthour = "0".$i;	
				$opttext = ($militaryTime?"0".$i:$i.":");
			}
		}elseif($i > 12){
			$opthour = $i;
			$opttext = ($militaryTime?$i:($i-12).":");
		}else{
			$opthour = $i;
			$opttext = ($militaryTime?$i:$i.":");
		}
		for($t=0;$t < floor(60/$intervalInMinutes);$t++){
				$optval = $opthour.":".($t>0?($t*$intervalInMinutes):"00").":00";
				$retval .= "<option value=\"".$optval."\"".($matchval == $optval?" selected ":"").">".$opttext.($t>0?($t*$intervalInMinutes):"00");
			if($i >= 12 && $i < 24){
				$retval .= ($militaryTime?"</option>":" P.M.</option>")."\r\n";
			}else{
				$retval .= ($militaryTime?"</option>":" A.M.</option>")."\r\n";
			}
			
		}	
	}
	return $retval;
}

function generateProvinceSelect($provinceID){
	$retval = "";
	if(is_array($provinceID)){
		$isArray = true;
	}else{
		$isArray = false;
	}
	if($isArray){
		$retval .= 	
		"<option value=\"\">--- Select Province---</option>\r\n".
		"<option value=\"AB\"". (in_array("AB",$provinceID)?" selected ":"").">Alberta</option>\r\n".
		"<option value=\"BC\"". (in_array("BC",$provinceID)?" selected ":"").">British Columbia</option>\r\n".
		"<option value=\"MB\"". (in_array("MB",$provinceID)?" selected ":"").">Manitoba</option>\r\n".
		"<option value=\"NB\"". (in_array("NB",$provinceID)?" selected ":"").">New Brunswick</option>\r\n".
		"<option value=\"NL\"". (in_array("NL",$provinceID)?" selected ":"").">Newfoundland and Labrador</option>\r\n".
		"<option value=\"NT\"". (in_array("NT",$provinceID)?" selected ":"").">Northwest Territories</option>\r\n".
		"<option value=\"NS\"". (in_array("NS",$provinceID)?" selected ":"").">Nova Scotia</option>\r\n".
		"<option value=\"NU\"". (in_array("NU",$provinceID)?" selected ":"").">Nunavut</option>\r\n".
		"<option value=\"ON\"". (in_array("ON",$provinceID)?" selected ":"").">Ontario</option>\r\n".
		"<option value=\"PE\"". (in_array("PE",$provinceID)?" selected ":"").">Prince Edward Island</option>\r\n".
		"<option value=\"QC\"". (in_array("QC",$provinceID)?" selected ":"").">Quebec</option>\r\n".
		"<option value=\"SK\"". (in_array("SK",$provinceID)?" selected ":"").">Saskatchewan</option>\r\n".
		"<option value=\"YT\"". (in_array("YT",$provinceID)?" selected ":"").">Yukon</option>\r\n";
	}else{
		$retval .= 	
		"<option value=\"\">--- Select Province---</option>\r\n".
		"<option value=\"AB\"". ($provinceID == "AB"?" selected ":"").">Alberta</option>\r\n".
		"<option value=\"BC\"". ($provinceID == "BC"?" selected ":"").">British Columbia</option>\r\n".
		"<option value=\"MB\"". ($provinceID == "MB"?" selected ":"").">Manitoba</option>\r\n".
		"<option value=\"NB\"". ($provinceID == "NB"?" selected ":"").">New Brunswick</option>\r\n".
		"<option value=\"NL\"". ($provinceID == "NL"?" selected ":"").">Newfoundland and Labrador</option>\r\n".
		"<option value=\"NT\"". ($provinceID == "NT"?" selected ":"").">Northwest Territories</option>\r\n".
		"<option value=\"NS\"". ($provinceID == "NS"?" selected ":"").">Nova Scotia</option>\r\n".
		"<option value=\"NU\"". ($provinceID == "NU"?" selected ":"").">Nunavut</option>\r\n".
		"<option value=\"ON\"". ($provinceID == "ON"?" selected ":"").">Ontario</option>\r\n".
		"<option value=\"PE\"". ($provinceID == "PE"?" selected ":"").">Prince Edward Island</option>\r\n".
		"<option value=\"QC\"". ($provinceID == "QC"?" selected ":"").">Quebec</option>\r\n".
		"<option value=\"SK\"". ($provinceID == "SK"?" selected ":"").">Saskatchewan</option>\r\n".
		"<option value=\"YT\"". ($provinceID == "YT"?" selected ":"").">Yukon</option>\r\n";
	}
	return $retval;
}
function generateCountrySelect($countryID){
	$retval = "";
	$retval .= 	"<option value=\"\">--- Select Country---</option>\r\n".
					"<option value=\"CA\"". ($countryID == "CA"?" selected ":"").">Canada</option>\r\n".
					"<option value=\"US\"". ($countryID == "US"?" selected ":"").">United States</option>\r\n";
	return $retval;
}

function generateAdminEmailOptions($matchval){
	global $link;
	$retval = "";
	$sql = 
	"SELECT CONCAT(firstName,' ',lastName) AS adminName, userGUID ".
	"FROM users ".
	"WHERE typeID = 1";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			$retval .=
			"<option value=\"\">-- Select Reply Address --</option>\r\n";
			while($row = $result->fetch_assoc()){
				$retval .=
				"<option value=\"".$row["userGUID"]."\"".(strlen($matchval) > 0 && strcasecmp($matchval,$row["userGUID"]) == 0?" selected ":"").">".$row["adminName"]."</option>\r\n";
			}
		}else{
			$retval .=
			"<option value=\"\"> No Administrators Found</option>\r\n";
		}
	}else{
		$retval .=
		"<option value=\"\"> No Administrators Found</option>\r\n";
	}
	return $retval;
}

function generateTypeSelect($typeTable,$matchval){
	
}

function generateMultiSelect($fieldID, $matchvals){
	global $link;
	$retval = "";
	//echo "Matchvals are ".$matchvals."<br />";
	$arrSelected = explode("~",$matchvals);
	
	$sql = 
	"SELECT * FROM formFieldOptions ".
	"WHERE fieldID = ".$fieldID." ".
	"ORDER BY sortOrder, optionText";
	
	//echo $sql."<br />";
	
	if($result = $link->query($sql)) {
		$retval .= 
		"<option value=\"\"> -- Please Select -- </option>\r\n";
		while($row = $result->fetch_assoc()){
			//echo "Option Value is ".$row["optionValue"]." and matchval is ".$matchval."<br />";
			$selecttext = "";
			foreach($arrSelected as $matchval){
				if(strcasecmp($row["optionValue"],$matchval) == 0){
					$selecttext = " SELECTED ";
				}
			}
			$retval .= 
			"<option value=\"".$row["optionValue"]."\"".$selecttext.">".$row["optionText"]."</option>\r\n";
		}
	}
	return $retval;
}

function generateSelectOptions($fieldID, $matchval){
		global $link;
		$retval = "";
		
		$sql = 
		"SELECT * FROM formFieldOptions ".
		"WHERE fieldID = ".$fieldID." ".
		"ORDER BY sortOrder, optionText";
		
		//echo $sql."<br />";
		
		if($result = $link->query($sql)) {
			$retval .= 
			"<option value=\"\"> -- Please Select -- </option>\r\n";
			while($row = $result->fetch_assoc()){
				//echo "Option Value is ".$row["optionValue"]." and matchval is ".$matchval."<br />";
				$selecttext = (strcasecmp($row["optionValue"],$matchval) == 0?" SELECTED ":"");
				$retval .= 
				"<option value=\"".$row["optionValue"]."\"".$selecttext.">".$row["optionText"]."</option>\r\n";
			}
		}
		return $retval;
	}

function updateAddress($address1,$address2,$city,$provinceID,$countryID,$postCode,$addressID=0){
	global $db;
	if(strlen($address1) > 0){
		$sql = 	"SELECT * FROM Addresses ".
					"WHERE Address1 = '".$db->escape(trim($address1))."' ".
					"AND Address2 = '".$db->escape(trim($address2))."'";
		if($addrow = $db->get_row($sql)){
			$addressID = $addrow->AddressID;
		}else{
			$sql = 	"INSERT INTO Addresses ".
						"SET Address1 = '".$db->escape(trim($address1))."', ".
						"Address2 = '".$db->escape(trim($address2))."', ".
						"City = '".$db->escape(trim($city))."', ".
						"ProvinceCode = '".$db->escape(trim($provinceID))."', ".
						"CountryCode = '".$db->escape(trim($countryID))."', ".
						"PostalCode = '".$db->escape(trim($postCode))."'";
			if($db->query($sql)){
				$addressID = $db->insert_id;
			}else{
				if(!$EZSQL_ERROR){
					//Indicates an update whenre no data was changed
					$addressID = $addrow->AddressID;
				}else{
					//$db->debug();	
				}
			}
		}
	}
	return $addressID;
}

function updatePerson($firstName,$lastName,$email,$phone,$mobile,$password,$typeID,$personID=0){
	global $db;
	if($personID > 0){
		$sqlstart = "UPDATE People ".
		$sqlend = 	"WHERE personID = ".$personID;
	}else{
		//Double check to make sure this person doesn't already exist
		$sql = 	"SELECT personID FROM people ".
					"WHERE email = '".$db->escape(trim($email))."'";
		if($ppl = $db->get_row($sql)){
			$personID = $ppl->personID;
			$sqlstart = "UPDATE People ".
			$sqlend = "WHERE personID = ".$personID;
		}else{
			$sqlstart = "INSERT INTO People ";
			$sqlend = "";
		}
		$sql = $sqlstart.
				"SET firstName = '".$db->escape(trim($firstName))."', ".
				"lastName = '".$db->escape(trim($lastName))."', ".
				"email = '".$db->escape(trim($email))."', ".
				"phone = '".$db->escape(trim($phone))."', ".
				"mobile = '".$db->escape(trim($mobile))."', ".
				"password = '".$db->escape(trim($password))."', ".
				"typeID = '".$db->escape(trim($typeID))."'".
				$sqlend;
		if($db->query($sql)){
			if($personID == 0){
				$personID = $db->insert_id;
			}
		}
	}
	return $personID;
}

function createAccountPerson($personID,$accountID,$typeID){
	global $db;
	if(!alreadyExists("Accounts_People","personID",$personID, "AND AccountID = '".$accountID."'")){
		$sql = 	"INSERT INTO Accounts_People ".
					"SET personID = ".$personID.", ".
					"AccountID = '".$accountID."', ".
					"TypeID = ".$typeID;
		$db->query($sql);
	}
}

function createClientPerson($personID,$clientID,$typeID){
	global $db;
	if(!alreadyExists("Clients_People","personID",$personID, "AND clientID = '".$clientID."'")){
		$sql = 	"INSERT INTO Clients_People ".
					"SET personID = ".$personID.", ".
					"clientID = '".$clientID."', ".
					"TypeID = ".$typeID;
		$db->query($sql);
	}
}

function createRepPerson($personID,$typeID,$accountID){
	global $db;
	if(!alreadyExists("Reps","personID",$personID, "")){
		$sql = 	"INSERT INTO Reps ".
				"SET personID = ".$personID.", ".
				"accountID = '".$accountID."'";
		$db->query($sql);
	}
}
function dateToTimestamp($cDate,$format){
	if($format == "dd/mm/yyyy"){
		//mktim wants a mm/dd/yyyy format so we have to convert it
		$dateparts = explode("/",$cDate);
		$unix_ts = mktime(0,0,0,$dateparts[1],$dateparts[0],$dateparts[2]);	
	}else if($format == "mm/dd/yyyy"){
		$dateparts = explode("/",$cDate);
		$unix_ts = mktime(0,0,0,$dateparts[0],$dateparts[1],$dateparts[2]);	
	}else if($format == "yyyy-mm-dd"){
		$dateparts = explode("-",$cDate);
		$unix_ts = mktime(0,0,0,$dateparts[1],$dateparts[2],$dateparts[0]);	
	}
	return $unix_ts;
}

function timestampToDate($ts,$format){
	if($format == "dd/mm/yyyy"){
		$retdate = date("d/m/Y",$ts);
	}else if($format == "mm/dd/yyyy"){
		$retdate = date("m/d/Y",$ts);
	}else if($format == "yyyy-mm-dd"){
		$retdate = date("Y-m-d",$ts);	
	}
	return $retdate;
}

function formatDouble($inStr){
	$retval = "";
	$retval = preg_replace("/[^0-9.]/", "", $inStr);
	return $retval;	
}

function formatPhone($inStr){
	$retval = "";
	$newStr = preg_replace("/[^0-9]/", "", $inStr);
	$charCount = strlen($newStr);
	if($charCount == 10){
		$retval = "(".substr($newStr,0,3).") ".substr($newStr,3,3)."-".substr($newStr,6);	
	}else if($charCount == 11){
		$retval = substr($newStr,0,1)." (".substr($newStr,1,3).") ".substr($newStr,4,3)."-".substr($newStr,7);	
	}else if($charCount == 7){
		$retval = substr($newStr,0,3)."-".substr($newStr,3);	
	}else{
		$retval = $inStr;	
	}
	return $retval;
}
?>