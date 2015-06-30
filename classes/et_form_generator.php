<?php

class etForminator{

	public $formID;
	public $formName;
	public $formSQL;
	public $arrFormFields;
	public $idNum;  //This is the IDNum in the Values tables - identifies User GUID or Posting Number, etc.
	
	function __construct($formID = 0, $idNum = ""){
		if($formID > 0){
			$this->formID = $formID;
			$this->formName = "";
			$this->idNum = $idNum;
			if($this->formID > 0 && strlen($this->idNum) > 0){
				$this->generateFormSQL();
			}
			$this->arrFormFields = "";
		}
	}
	
	function generateFormSQL(){
		$this->formSQL = 
		"SELECT frm.formTable, frm.formName, frm.formLabel, ".
		"ff.fieldID, ff.fieldTypeID, ff.formID, ff.fieldLabel, ff.sortOrder, ff.mandatory, ff.validation, ff.cssDisplay, ff.jsCode, ff.jsMask, ff.defaultValue, ff.filterName, ".
		(strlen($this->idNum) > 0?"fv.submissionID, fv.fieldValue, ":"").
		"fft.formFieldTypeID, fft.formFieldTypeName, fft.formFieldFormat ".
		"FROM forms frm ".
		"INNER JOIN formFields ff ON frm.formID = ff.formID ".
		"INNER JOIN formFieldTypes fft ON fft.formFieldTypeID = ff.fieldTypeID ".
		(strlen($this->idNum) > 0?"LEFT JOIN formCandidateDetailsValues fv ON (fv.fieldID = ff.fieldID AND fv.idNum = '".$this->idNum."') ":"").
		"WHERE frm.formID = ".$this->formID." ".
		"ORDER BY ff.sortOrder";
		
		//echo $this->formSQL."<br />";
	}
	
	function generateValuesSQL(){
		$this->formSQL = 
		"SELECT 
		ff.fieldID, ff.fieldTypeID, ff.formID, ff.sortOrder, ff.filterName, ff.fieldParent, 
		CONCAT(
			CASE 
				WHEN ff.fieldTypeID = 14 AND fv.fieldValue = 1 THEN ''
				WHEN ff.fieldTypeID = 14 AND fv.fieldValue = 0 THEN 'No'
				ELSE fv.fieldValue
			END,
		(SELECT CASE 
			WHEN GROUP_CONCAT(fv2.fieldValue ORDER BY ff2.sortOrder) IS NOT NULL 
				THEN CONCAT(
					CASE 
					  WHEN ff.fieldTypeID = 14 AND fv.fieldValue = 1 
					  THEN ''
					  ELSE ', '
					END, GROUP_CONCAT(
										CASE 
											WHEN ff2.fieldTypeID = 14 AND fv2.fieldValue = 1 THEN 'Yes'
											WHEN ff2.fieldTypeID = 14 AND fv2.fieldValue = 0 THEN 'No'
											ELSE fv2.fieldValue
										END
										ORDER BY ff2.sortOrder
									  )
							)
			ELSE
				''
			END
		FROM formCandidateDetailsValues fv2
		LEFT JOIN formFields ff2 ON fv2.fieldID = ff2.fieldID
		WHERE ff2.fieldParent = ff.fieldID
		AND ff2.fieldParent <> 31
		AND fv2.idNum = fv.idNum)) AS fieldValue 
		FROM forms frm 
		INNER JOIN formFields ff ON frm.formID = ff.formID 
		INNER JOIN formFieldTypes fft ON fft.formFieldTypeID = ff.fieldTypeID 
		LEFT JOIN formCandidateDetailsValues fv ON (fv.fieldID = ff.fieldID AND fv.idNum = '".$this->idNum."') 
		WHERE frm.formID = ".$this->formID." ".
		"AND (ff.fieldParent IS NULL OR ff.fieldParent = 31)
		ORDER BY frm.formID, ff.sortOrder";
		
		//echo $this->formSQL."<br /><br />";
	}
	
	function getFormFields($sqlType = "FIELDS"){
		global $link;
		$arrFields = "";
		if(strcasecmp($sqlType,"VALUES") == 0){
			$this->generateValuesSQL();
			//echo "Values SQL is ".$this->formSQL."<br />";
		}else{
			$this->generateFormSQL();
		}
		
		//echo "SQL is ".$this->formSQL."<br />";
		if($result = $link->query($this->formSQL)){
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					foreach($row AS $fldName=>$fldVal){
						//echo "Field Name is ".$fldName."<br />";
						if(strcasecmp($fldName,"fieldID") !== 0){
							$arrFields[$row["fieldID"]][$fldName] = $fldVal;
						}
					}
				}
			}
		} 
		return $arrFields;
	}
	
	function createField($fieldID,$fieldData){
		$retval = "";
		if(strcasecmp($fieldData["cssDisplay"],"none") == 0){
			$retval .=
			"<div class=\"".$fieldID."\"".(strlen($fieldData["fieldValue"]) == 0?" style=\"display:none;\" ":"").">\r\n";
		}
		
		if($fieldData["fieldTypeID"] == 1){   //Text Field
			
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .=
			//"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_select\" value=\"".$fieldData["fieldValue"]."\" /><br />\r\n";
			"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_text_field".($fieldData["mandatory"] == 1?" mandatory ":"")."\" ".
			"value=\"".(strlen($fieldData["fieldValue"]) > 0?$fieldData["fieldValue"]:$fieldData["defaultValue"])."\" ".
			"onfocus=\"clearField('fld_".$fieldID."', '".$fieldData["defaultValue"]."');\" onblur = \"addFieldDefault('fld_".$fieldID."','".$fieldData["defaultValue"]."');\" /><br />\r\n";
		}elseif($fieldData["fieldTypeID"] == 2 || $fieldData["fieldTypeID"] == 3){ //Multi-Select field
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .=
			"<select name=\"fld_".$fieldID.($fieldData["fieldTypeID"] == 3?"[]":"")."\" id=\"fld_".$fieldID."\" class=\"style".($fieldData["fieldTypeID"] == 3?"_multi":"")."_select".($fieldData["mandatory"] == 1?" mandatory ":"")." fld_".$fieldID."\" ".
			($fieldData["fieldTypeID"] == 3?"multiple ":"").($fieldData["fieldTypeID"] == 3?" size=5 ":"").">\r\n";
			if($fieldData["fieldTypeID"] == 2){
				$retval .= $this->generateSelectOptions($fieldID, $fieldData["fieldValue"]);
			}else{
				$retval .= $this->generateMultiSelect($fieldID, $fieldData["fieldValue"]);
			}
			$retval .= 
			"</select>\r\n".
			"<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 4){ //Radio Buttons
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n";
			$retval .= $this->generateRadioButtons($fieldID, $fieldData["fieldValue"]);
			$retval .= 
			"<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 6){ //TEXTAREA
			/*
			$retval .= 
			"<label for=\"fld_".$fieldData["fieldID"]."\" style=\"width:95%;text-align:left;\">".$fieldData["fieldLabel"]."</label>\r\n".
			"<div class=\"clear\"></div>\r\n"; 
			*/
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n";
			$retval .=
			"<textarea name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_text_area\">".$fieldData["fieldValue"]."</textarea>\r\n".
			"<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 9){ //Phone field ADD Formatting/Mask info
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .=
			"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_text_field phone".($fieldData["mandatory"] == 1?" mandatory ":"")."\" value=\"".$fieldData["fieldValue"]."\" /><br />\r\n";
		}elseif($fieldData["fieldTypeID"] == 10){ //POSTCODE field - Should be shorter
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n";
			$retval .=
			"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_text_field".($fieldData["mandatory"] == 1?" mandatory ":"")." postcode\" style=\"width:80px;\" value=\"".$fieldData["fieldValue"]."\" /><br />\r\n";
		}elseif($fieldData["fieldTypeID"] == 13){   //Province Field
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n";
			$retval .= 
			"<select name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_select province".($fieldData["mandatory"] == 1?" mandatory ":"")."\">\r\n";
			$retval .= $this->generateProvinceSelect($fieldData["fieldValue"]);
			$retval .=
			"</select>\r\n".
			"<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 14){   //YESNO
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\" >".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\" >".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n";
			$retval .= 
			"<input type=\"radio\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."_Yes\" class=\"fld_".$fieldID."\" value=\"1\" ".(strcasecmp($fieldData["fieldValue"],"1") == 0?" checked ":"").">Yes ".
			"<input type=\"radio\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."_No\" class=\"fld_".$fieldID."\" value=\"0\" ".(strcasecmp($fieldData["fieldValue"],"0") == 0?" checked ":"").">No ".
			"\r\n<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 15){   //SINGLECHECK
			$retval .= 
			"<input type=\"checkbox\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" value=1".($fieldData["mandatory"] == 1?" class=\"mandatory\" ":"")."style=\"float:left;margin-right:5px;\" > <div>".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</div> ".
			"<div class=\"clear\"></div>\r\n";
		}elseif($fieldData["fieldTypeID"] == 16){  //TYPEDATE
			//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .= "<label for=\"fld_".$fieldID."\">".($fieldData["mandatory"] == 1?" <span class=\"mand\">*</span> ":"").$fieldData["fieldLabel"]."</label>\r\n"; 
			$retval .=
			//"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_select\" value=\"".$fieldData["fieldValue"]."\" /><br />\r\n";
			"<input type=\"text\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."\" class=\"style_text_field".($fieldData["mandatory"] == 1?" mandatory ":"")." typedate\" ".
			"value=\"".(strlen($fieldData["fieldValue"]) > 0?$fieldData["fieldValue"]:$fieldData["defaultValue"])."\" ".
			//"onfocus=\"clearField('fld_".$fieldID."', '".$fieldData["defaultValue"]."');\" onblur = \"addFieldDefault('fld_".$fieldID."','".$fieldData["defaultValue"]."');\" /><br />\r\n";
			"onfocus=\"this.value = this.value=='".$fieldData["defaultValue"]."'?'':this.value;\" onblur=\"this.value = this.value=='____/__/__'?'".$fieldData["defaultValue"]."':this.value;\" /><br />\r\n";
		}
		if(strlen($fieldData["jsCode"]) > 0){
			if(stripos($fieldData["jsCode"],"$(document)") !== false){
				$retval .=
				"\r\n<script type=\"text/javascript\">".$fieldData["jsCode"]."</script>\r\n";
			}else{
				$retval .=
				"\r\n<script type=\"text/javascript\">\r\n".
				"	$(\".fld_".$fieldID."\")".$fieldData["jsCode"]."\r\n";
				if(stripos($fieldData["jsCode"],".change") !== false){
					$retval .=
					"$(document).ready(function() {\r\n".
					"	$(\".fld_".$fieldID."\").trigger(\"change\");\r\n".
					"});\r\n";
				}
				$retval .=
				"</script>\r\n";
			}
		}
		if(strlen($fieldData["jsMask"]) > 0){
			$retval .=
			"\r\n<script type=\"text/javascript\">$(\"#fld_".$fieldID."\").mask(\"".$fieldData["jsMask"]."\")</script>\r\n";
		}
		if(strlen($fieldData["cssDisplay"]) > 0 && strcasecmp($fieldData["cssDisplay"],"none") == 0){
			$retval .=
			"</div>\r\n";
		}
		
		return $retval;
	}
	
	function createTextDisplay($fieldID,$fieldData){
		$retval = "";
		//$retval .= "<label for=\"fld_".$fieldData["fieldID"]."\">".$fieldData["filterName"]."</label>\r\n"; 
		$retval .= "<label for=\"fld_".$fieldID."\">".$fieldData["filterName"]."</label>\r\n";
		$retval .=
		"<div id=\"fld_".$fieldID."\" class=\"display_text\">\r\n".
		$fieldData["fieldValue"]."\r\n".
		"</div>\r\n";
		$retval .=
		"<div class=\"clear\"></div>\r\n";
		return $retval;
	} 
	
	function submitValue($fieldID,$fieldData,$fieldValue){
		global $link;
		$retval = false;
		if(strlen($this->idNum) > 0){
			if(is_array($fieldValue)){
				$i = 1;
				foreach($fieldValue as $tmpValue){
					$newValue .= $tmpValue.($i < count($fieldValue)?"~":"");
					$i++;
				}
			}else{
				$newValue = $fieldValue;
			}
			
			if(strlen($fieldData["submissionID"]) > 0 && strlen($newValue) > 0){
				$sql = 
				"UPDATE ".$fieldData["formTable"]." ".
				"SET idNum = '".$this->idNum."', ".
				"fieldID = ".$fieldID.", ".
				"fieldValue = '".$link->real_escape_string($newValue)."' ".
				"WHERE submissionID = ".$fieldData["submissionID"];
			}elseif(strlen($fieldData["submissionID"]) > 0 && strlen($newValue) == 0){
				//Empty Value for the field so we delete it
				$sql =
				"DELETE FROM ".$fieldData["formTable"]." ".
				"WHERE submissionID = ".$fieldData["submissionID"];
			}elseif(strlen($fieldData["submissionID"]) == 0 && strlen($newValue) > 0){
				//No Submission ID AND a value for the field so we INSERT it
				$sql =
				"INSERT INTO ".$fieldData["formTable"]." ".
				"SET idNum = '".$this->idNum."', ".
				"fieldID = ".$fieldID.", ".
				"fieldValue = '".$link->real_escape_string($newValue)."'";
			}
			
			//echo $sql."<br />";
			if(strlen($sql) > 0){
				if($link->query($sql)) {
					if($link->affected_rows > 0){
						$retval = true;
					}else{
						if(strlen($link->error) > 0){
							echo "Error: ".$link->error."<br />".$sql."<br />";
						}else{
							$retval = true;
						}
					}
				}else{
					if(strlen($link->error) > 0){
						echo "Error: ".$link->error."<br />".$sql."<br />";
					}else{
						$retval = true;
					}
				}
			}else{
				$retval = true;
			}
		}
		
		return $retval;
	}
	
	function generateProvinceSelect($provinceID){
		$retval = "";
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
	
	function generateMultiSelect($fieldID, $matchvals){
		global $link;
		$retval = "";
		
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
	
	function generateCitySelect($cityIDs, $citySQL = ""){
		global $link;
		$retval = "";
		if(strlen($cityIDs) > 0){
			
		}
		if(strlen($citySQL) == 0){
			$citySQL = 
			"SELECT * FROM formOptionsCities ".
			"ORDER BY cityName";
			if($result = $link->query($citySQL)) {
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						$retval .=
						"<option value=\"".$row["cityID"]."\" ";
					}
				}
			}
		}
		
		
		return $retval;
	}
	
	function generateRadioButtons($fieldID, $matchval){
		global $link;
		$retval = "";
		$sql = 
		"SELECT * FROM formFieldOptions ".
		"WHERE fieldID = ".$fieldID." ".
		"ORDER BY sortOrder, optionText";
		if(is_numeric($fieldID) && $fieldID > 0){
			if($result = $link->query($sql)) {
				while($row = $result->fetch_assoc()){
					$retval .=
					"<input type=\"radio\" name=\"fld_".$fieldID."\" id=\"fld_".$fieldID."_".$row["optionValue"]."\" class=\"fld_".$fieldID."\" value=\"".$row["optionValue"]."\" ".
					(strcasecmp($matchval,$row["optionValue"]) == 0?" checked ":"").">".$row["optionText"]." ";
				}
			}
		}
		return $retval;
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
	
	/**************************** Validation Routines ***********************************************/
	
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
	
	
	/**************************** End Validation Routines *******************************************/
}


?>