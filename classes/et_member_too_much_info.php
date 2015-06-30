<?php

class etMember{
	public $userGUID;
	public $firstName;
	public $lastName;
	public $email;
	public $memberData;
	
	function __construct($userGUID = ""){
		if(strlen($userGUID) > 0){
			$this->userGUID = $userGUID;
			$this->memberData = $this->getMemberData($userGUID);
			//echo "<br /><br />";
			//var_dump($this->memberData);
			//echo "<br /><br />";
		}
	}
	
	function getMemberData(){
		global $link;
		$retval = "";
		
		if(strlen($this->userGUID) > 0){
			$sql = 
			"SELECT firstName, lastName, email ".
			"FROM users ".
			"WHERE userGUID = '".$this->userGUID."'";
			if($result = $link->query($sql)){
				while($row = $result->fetch_assoc()){
					$this->firstName = $row["firstName"];
					$this->lastName = $row["lastName"];
					$this->email = $row["email"];
				}
				//Now that we have the member personal info we go get their Candidate form values
				//Initially we ONLY want fields that have NULL as the parentField value
				$sql = 
				"SELECT frm.formTable, frm.formName, ".
				"ff.fieldID, ff.fieldTypeID, ff.formID, ff.sortOrder, ff.filterName, ff.fieldParent, ".
				"fv.submissionID, fv.fieldValue, ".
				"FROM forms frm ".
				"INNER JOIN formFields ff ON frm.formID = ff.formID ".
				"INNER JOIN formFieldTypes fft ON fft.formFieldTypeID = ff.fieldTypeID ".
				"LEFT JOIN formCandidateDetailsValues fv ON (fv.fieldID = ff.fieldID AND fv.idNum = '".$this->userGUID."') ".
				"WHERE (frm.formID = 2 ".
				"OR frm.formID = 3 ".
				"OR frm.formID = 4) ".
				"AND fieldParent IS NULL ".
				"ORDER BY frm.formID, ff.sortOrder";
				//echo "<br /><br />".$sql."<br /><br />";
				if($result = $link->query($sql)){
					//First we populate the return array with all fields that do not have a parent
					//Next we will get all the data from child fields and append it to the parent array element
					while($row = $result->fetch_assoc()){
						if($row["formFieldTypeID"] == 14){
							$retval[$row["filterName"]] = ($row["fieldValue"] == 1?"Yes":"No");
						}else{
							$retval[$row["filterName"]] = $row["fieldValue"];
						}
					}
				}
				
				if($result = $link->query($sql)){
					//First we populate the return array with all fields that do not have a parent
					//Next we will get all the data from child fields and append it to the parent array element
					while($row = $result->fetch_assoc()){
						if($row["formFieldTypeID"] == 14){
							$retval[$row["parentFilter"]] .= ($row["fieldValue"] == 1?"Yes":"No");
						}else{
							$retval[$row["parentFilter"]] .= " ".$row["fieldValue"];
						}
					}
				}
				
			}
		}
		
		return $retval;
	}
	

}

?>