<?php

class etMember{
	public $userGUID;
	public $firstName;
	public $lastName;
	public $acknowledge_employment_status = false;
	public $acknowledge_company_policies = false;
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
			"SELECT firstName, lastName, email, acknowledge_employment_status, acknowledge_company_policies ".
			"FROM users ".
			"WHERE userGUID = '".$this->userGUID."'";
			if($result = $link->query($sql)){
				while($row = $result->fetch_assoc()){
					$this->firstName = $row["firstName"];
					$this->lastName = $row["lastName"];
					$this->email = $row["email"];
					$this->acknowledge_employment_status = $row["acknowledge_employment_status"] == 1?true:false;
					$this->acknowledge_company_policies = $row["acknowledge_company_policies"] == 1?true:false;
				}
			}
		}
		
		return $retval;
	}
	

}

?>