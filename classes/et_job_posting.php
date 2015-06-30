<?php

class etJobPosting{

	public $postingID;
	public $postingStatus;
	public $programName;
	public $province;
	public $cities;
	public $timing;
	public $rolesRequired;
	public $postingDetails;
	public $adminOwner;
	public $adminEmail;
	
	function __construct($postingID = 0){
		$this->postingID = $postingID;
		if($this->postingID > 0){
			$this->getPostingData();
		}else{
			$this->setDefaults();
		}
	}
	
	function getPostingData(){
		global $link;
		if($this->postingID > 0){
			$sql = 
			"SELECT pst.*, usr.email ".
			"FROM jobPostings pst ".
			"LEFT JOIN users usr ON pst.adminOwner = usr.userGUID ".
			"WHERE pst.postingID = ".$this->postingID;
			//echo $sql."<br />";
			if($posting = $link->query($sql)){
				if($posting->num_rows > 0){
					$thispost = $posting->fetch_assoc();
					$this->postingStatus = (strlen($thispost["postingStatus"]) > 0?$thispost["postingStatus"]:"ACTIVE");
					$this->programName = $thispost["programName"];
					$this->province = $thispost["province"];
					$this->cities = $thispost["cities"];
					$this->timing = $thispost["programTiming"];
					$this->rolesRequired = $thispost["rolesRequired"];
					$this->postingDetails = $thispost["postingDetails"];
					$this->adminOwner = $thispost["adminOwner"];
					$this->adminEmail = $thispost["adminEmail"];
				}
			}
		}
	}
	
	function setDefaults(){
		$this->postingStatus = "ACTIVE";
		$this->programName = "";
		$this->province = "";
		$this->cities = "";
		$this->timing = "";
		$this->rolesRequired = "";
		$this->postingDetails = "";
		$this->adminOwner = "";
		$this->adminEmail = "";
	}
	
	function displayForm(){
		global $etUser,$formMsg;
		$retval = "";
		$retval .=
		"	<fieldset>\r\n".
		"		<legend>".(strlen($this->programName) > 0?$this->programName:"Create Job Posting")."</legend>\r\n".
		"			<div style=\"font-weight:bold;color:red;\">\r\n".
		(strlen($formMsg) > 0?"<p>\r\n".$formMsg."\r\n</p>\r\n":"").
		"			</div>\r\n".
		"			<label for=\"program_name\">Program Name</label>\r\n".
		"			<input type=\"text\" name=\"program_name\" id=\"program_name\" class=\"style_text_field\" value=\"".(strlen($this->programName) > 0?$this->programName:"Program Name")."\" onfocus=\"clearField('program_name', 'Program Name');\" /><br />\r\n".
		"			<label for=\"posting_status\">Status</label>\r\n".
		"			<select name=\"posting_status\" id=\"posting_status\" class=\"style_select\">\r\n".
		"				<option value=\"\">-- Select Status --</option>\r\n".
		"				<option value=\"ACTIVE\"".(strcasecmp($this->postingStatus,"ACTIVE") == 0?" selected ":"").">Active</option>\r\n".
		"				<option value=\"INACTIVE\"".(strcasecmp($this->postingStatus,"INACTIVE") == 0?" selected ":"").">Inactive</option>\r\n".
		"			</select>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"province\">Province</label>\r\n".
		"			<select name=\"province\" id=\"province\" class=\"style_select\">\r\n".
		"			".generateProvinceSelect($this->province).
		"			</select>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"cities\">Cities</label>\r\n".
		"			<input type=\"text\" name=\"cities\" id=\"cities\" class=\"style_text_field\" value=\"".(strlen($this->cities) > 0?$this->cities:"City or Cities")."\" onfocus=\"clearField('cities', 'City or Cities');\" /><br />\r\n".
		"			<label for=\"program_timing\">Timing</label>\r\n".
		"			<input type=\"text\" name=\"program_timing\" id=\"program_timing\" class=\"style_text_field\" value=\"".(strlen($this->timing) > 0?$this->timing:"Program Timing")."\" onfocus=\"clearField('program_timing', 'Program Timing');\" /><br />\r\n".
		"			<label for=\"required_roles\">Roles Needed</label>\r\n".
		"			<input type=\"text\" name=\"required_roles\" id=\"required_roles\" class=\"style_text_field\" value=\"".(strlen($this->rolesRequired) > 0?$this->rolesRequired:"Required Roles")."\" onfocus=\"clearField('required_roles', 'Required Roles');\" /><br />\r\n".
		"			<label for=\"details\">Posting Details</label>\r\n".
		"			<textarea name=\"details\" id=\"details\" class=\"style_text_area\">".(strlen($this->postingDetails) > 0?$this->postingDetails:"")."</textarea>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"admin_email\">Reply Email</label>\r\n".
		"			<select name=\"admin_owner\" id=\"admin_owner\" class=\"style_select\">\r\n".
		"			".generateAdminEmailOptions((strlen($this->adminOwner) > 0?$this->adminOwner:$etUser->userGUID)).
		"			</select>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
		"			<input type=\"hidden\" name=\"PID\" value=".$this->postingID." />\r\n".
		"	</fieldset>\r\n".
		"	<div style=\"width:422px;text-align:right;\">\r\n".
		"		<input type=\"submit\" class=\"btn_blue\" style=\"margin-right:0px;\" value=\"".($this->postingID > 0?"Update":"Create")." Posting\" />\r\n".
		"	</div>";
		
		return $retval;
	}
	
	function displayData(){
		global $etUser,$formMsg,$link;
		$retval = "";

		$retval .=
		"	<fieldset>\r\n".
		"		<legend>".(strlen($this->programName) > 0?$this->programName:"Create Job Posting")."</legend>\r\n".
		"			<div style=\"font-weight:bold;color:red;\">\r\n".
		(strlen($formMsg) > 0?"<p>\r\n".$formMsg."\r\n</p>\r\n":"").
		"			</div>\r\n";
		if($etUser->typeID < 4){
			$retval .=
			"	<div style=\"width:422px;text-align:right;border:0px solid red;\">\r\n".
			"		<p>\r\n".
			"		<a href=\"".$_SERVER["PHP_SELF"]."?PID=".$this->postingID."&ACT=DEL\" class=\"btn_blue\" style=\"margin-right:0px;\">Delete Job Posting</a>\r\n".
			"		<a href=\"createJobPosting.php?PID=".$this->postingID."\" class=\"btn_blue\" style=\"margin-right:0px;\">Edit Job Posting</a>\r\n".
			"		</p>\r\n".
			"	</div>\r\n".
			"	<div class=\"clear\"></div>\r\n";
		}elseif($etUser->typeID == 4){
			$applicationDate = "";
			//Check to see if the user has already applied for this posting
			$sql = 
			"SELECT applicationDate ".
			"FROM applications ".
			"WHERE userGUID = '".$etUser->userGUID."' ".
			"AND postingID = ".$this->postingID;
			//$retval .= $sql."<br />";
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
					$row = $result->fetch_assoc();
					$applicationDate = $row["applicationDate"];
				}
			}
			if(strlen($applicationDate) > 0){
				$retval .=
				"	<div style=\"width:422px;text-align:left;\">\r\n".
				"		<strong>You applied for this posting on ".date("M jS, Y",strtotime($applicationDate))."</strong><br /><br />\r\n".
				"	</div>";
			}else{
				$retval .=
				"	<div style=\"width:422px;text-align:right;\">\r\n".
				//"		<a href=\"mailto:".$this->adminEmail."?subject=Application for Job Posting - ".$this->programName."\" class=\"btn_blue\" style=\"margin-right:0px;\">Apply</a>\r\n".
				"		<a href=\"".$_SERVER["PHP_SELF"]."?PID=".$this->postingID."&ACT=APPLY\" class=\"btn_blue\" style=\"margin-right:0px;\">Apply</a>\r\n".
				"	</div>";
			}
			
		}
		$retval .=
		"			<label for=\"program_name\">Program Name</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->programName) > 0?$this->programName:"Program Name")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"province\">Province</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->province) > 0?$this->province:"Province")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"cities\">Cities</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->cities) > 0?$this->cities:"City or Cities")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"program_timing\">Timing</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->timing) > 0?$this->timing:"Program Timing")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"required_roles\">Roles Needed</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->rolesRequired) > 0?$this->rolesRequired:"Required Roles")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"details\">Posting Details</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->postingDetails) > 0?$this->postingDetails:"")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"			<label for=\"admin_email\">Reply Email</label>\r\n".
		"			<div class=\"display_text\">".(strlen($this->adminEmail) > 0?$this->adminEmail:"")."</div>\r\n".
		"			<div class=\"clear\"></div>\r\n".
		"	</fieldset>\r\n";
		
		return $retval;
	}
	
	function getAdminEmail(){
		global $link;
		if(strlen($this->adminOwner) > 0){
			$sql = 
			"SELECT email ".
			"FROM users ".
			"WHERE userGUID = '".$this->adminOwner."'";
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
					$row = $result->fetch_assoc();
					$this->adminEmail = $row["email"];
				}
			}
		}
	}
	
	function addDataToDB(){
		global $link, $etUser,$formMsg;
		$isUpdate = false;
		if($this->postingID > 0){
			$isUpdate = true;
		}else{
			if(strlen($this->programName) > 0 && strlen($this->rolesRequired) > 0){
				//check to see if the information is in the database and if so we get the postingID
				$sql = 
				"SELECT postingID ".
				"FROM jobPostings ".
				"WHERE UPPER(programName) = UPPER('".$this->programName."') ".
				"AND UPPER(rolesRequired) = UPPER('".$this->rolesRequired."') ".
				"AND UPPER(programTiming) = UPPER('".$this->timing."') ";
				
				if($result = $link->query($sql)){
					if($result->num_rows > 0){
						$isUpdate = true;
						$row = $result->fetch_assoc();
						$this->postingID = $row["postingID"];
					}
				}
			}
		}
		//Now we know if it is an update or not so we can proceed to put the data into the database
		
		if($isUpdate){
			$sql = 
			"UPDATE jobPostings ";
			$sqlend = 
			"lastUpdated = NOW(), ".
			"updatedBy = '".$etUser->userGUID."' ".
			"WHERE postingID = ".$this->postingID;
		}else{
			$sql = 
			"INSERT INTO jobPostings ";
			$sqlend = 
			"dateCreated = NOW(), ".
			"createdBy = '".$etUser->userGUID."'";
		}
		
		$sql .=
		"SET postingStatus = '".$link->real_escape_string($this->postingStatus)."', ".
		"programName = '".$link->real_escape_string($this->programName)."', ".
		"province = '".$link->real_escape_string($this->province)."', ".
		"cities = '".$link->real_escape_string($this->cities)."', ".
		"programTiming = '".$link->real_escape_string($this->timing)."', ".
		"rolesRequired = '".$link->real_escape_string($this->rolesRequired)."', ".
		"postingDetails = '".$link->real_escape_string($this->postingDetails)."', ".
		"adminOwner = '".$link->real_escape_string($this->adminOwner)."', ".
		"adminEmail = '".(strlen($this->adminEmail) > 0?$link->real_escape_string($this->adminEmail):$link->real_escape_string($etUser->email))."', ";
		
		$sql .= $sqlend;
		
		if($link->query($sql)){
			if($link->affected_rows > 0){
				//Insert or Update has been done.
				//If this is an INSERT (not an UPDATE) then we want to get the postingID
				if(!$isUpdate){
					$this->postingID = $link->insert_id;
				}
				$formMsg .=
				"The Job Posting has been successfully ".($isUpdate?"updated":"created")."<br />\r\n";
			}else{
				if(strlen($link->error) > 0){
					$formMsg .=
					"There was an error ".($isUpdate?"updating":"creating")." this Job Posting<br />\r\n".
					$link->error."<br />\r\n";
				}else{
					$formMsg .=
					"The Job Posting has been successfully ".($isUpdate?"updated":"created")."<br />\r\n";
				}
			}
		}else{
			if(strlen($link->error) > 0){
				$formMsg .=
				"There was an error ".($isUpdate?"updating":"creating")." this Job Posting<br />\r\n".
				$link->error."<br />\r\n".
				$sql."<br />";
			}else{
				$formMsg .=
				"There was an error ".($isUpdate?"updating":"creating")." this Job Posting<br />\r\n".
				$sql."<br />";
			}
		}
	}
	
	function deletePost(){
		global $link;
		$retval = false;
		if($this->postingID > 0){
			$sql = 
			"DELETE FROM jobPostings ".
			"WHERE postingID = ".$this->postingID;
			if($link->query($sql)){
				if($link->affected_rows > 0){
					$retval = true;
				}
			}
		}
		
		return $retval;
	}
}

?>