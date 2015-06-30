<?php

class etPerson{
	
	public $userGUID;
	public $typeID;
	public $accessLevel;
	public $firstName;
	public $lastName;
	public $email;
	public $password;
	public $emailOptin;
	public $repData;
	public $useCookie;
	public $isLoggedIn;
	public $lastLogin;
	public $profileCreated;
	public $activationCode;
	public $isActivated;
	
	function __construct($userGUID = ""){
		global $link;
		$this->userGUID = $userGUID;
		$this->typeID = 0;
		$this->useCookie = true;   //In future we will grab this from a config table in the DB
		$this->isLoggedIn = false;
		$this->lastLogin = "";	
		$this->profileCreated = "";
		$this->emailOptin = 0;
		$this->activationCode = "";
		$this->isActivated = false;

		if(strlen($userGUID) > 0){
			$this->getPersonData($userGUID);
		}
		
		//echo "Starting userGUID is ".$userGUID."<br />";
	}
	
	function getPersonData($userGUID = ""){
		global $link;
		if(strlen($userGUID) > 0){
			$sql = 
			"SELECT usr.*, ".
			"(SELECT MAX(activityDate) FROM userActivityLog ual WHERE activityID = 3 AND GUID = '".$this->userGUID."') AS lastLogin, ".
			"(SELECT activityDate FROM userActivityLog ual WHERE activityID = 1 AND GUID = '".$this->userGUID."') AS profileCreated, ".
			"IFNULL((SELECT COUNT(GUID) FROM userActivityLog ual WHERE activityID = 2 AND GUID = '".$this->userGUID."'),0) AS profileActivated ".
			"FROM users usr ".
			"WHERE usr.userGUID = '".$this->userGUID."'";
			//echo $sql."<br />";
			if($result = $link->query($sql)){
				while($row = $result->fetch_assoc()){
					$this->firstName = $row["firstName"];
					$this->lastName = $row["lastName"];
					$this->email = $row["email"];
					$this->password = $row["password"];
					$this->emailOptin = $row["emailOptin"];
					$this->typeID = $row["typeID"];
					$this->lastLogin = $row["lastLogin"];
					$this->profileCreated = $row["profileCreated"];
					$this->activationCode = $row["activationCode"];
					$this->isActivated = is_numeric($row["profileActivated"]) && $row["profileActivated"] > 0?true:false;
				}
			}
		}
	}
	
	function addDataToDB(){
		global $link, $etUser,$formMsg;
		$retval = false;
		$isUpdate = false;
		$retval = "";
		
		if(strlen($this->firstName) > 0){
			if(strlen($this->userGUID) > 0){
				$isUpdate = true;	
			}else{
				//First we run a quick check to see if we are being double submitted
				$sql = 	"SELECT userGUID FROM users ".
						"WHERE email = '".$link->real_escape_string($this->email)."'";
				//echo $sql."<br />";
				
				if($result = $link->query($sql)){
					//We found a row so it is probably safe to assume that this should be an update
					//First we have to set the ClientID to the ID we found
					//Presumably the rest of the information has already been populated from the POST data so we won't worry about it
					if($result->num_rows > 0){
						$row = $result->fetch_assoc();
						$this->userGUID = $row["userGUID"];	
						$isUpdate = true;
					}
				}
				
			}
			if($isUpdate){
				$sql = 	"UPDATE users ".
						"SET firstName = '".$link->real_escape_string($this->firstName)."', ".
						"lastName = '".$link->real_escape_string($this->lastName)."', ".
						"email = '".$link->real_escape_string($this->email)."', ".
						"password = '".$link->real_escape_string($this->password)."', ".
						"emailOptin = ".$link->real_escape_string($this->emailOptin)." ".
						"WHERE userGUID = '".$link->real_escape_string($this->userGUID)."'";
				if($link->query($sql)) {
					if($link->affected_rows > 0){
						//We successfully updated so now we add that information to the userActivityLog
						$sql = 
						"INSERT INTO userActivityLog ".
						"SET GUID = '".$link->real_escape_string($this->userGUID)."', ".
						"activityID = 5, ". //5 is an Update
						"activityDate = NOW()";
						if($link->query($sql)){
							$retval = true;
						}else{
							//TODO - Add error handling
							if(strlen($link->error) > 0){
								echo "Error: ".$link->error."<br />";
							}else{
								$retval = true;
							}
						}
					}else{
						//TODO - Add error handling 
						if(strlen($link->error) > 0){
							echo "Error: ".$link->error."<br />";
						}else{
							$retval = true;
						}
					}
				}else{
					echo "Error: ".$link->error."<br />".$sql."<br />";
				}
			}else{
				$tmpGUID = $this->generateGUID(10);
				$tmpActivationCode = $this->generateGUID(6);
				$sql = 	"INSERT INTO users ".
						"SET userGUID = '".$tmpGUID."', ".
						"firstName = '".$link->real_escape_string($this->firstName)."', ".
						"lastName = '".$link->real_escape_string($this->lastName)."', ".
						"email = '".$link->real_escape_string($this->email)."', ".
						"password = '".$link->real_escape_string($this->password)."', ".
						"emailOptin = ".$link->real_escape_string($this->emailOptin).", ".
						"activationCode = '".$link->real_escape_string($tmpActivationCode)."', ".
						"typeID = 4 ";
				//echo $sql."<br />";
				if($link->query($sql)) {
					if($link->affected_rows > 0){
						//We successfully Inserted so now we add that information to the userActivityLog
						$this->userGUID = $tmpGUID;
						$this->activationCode = $tmpActivationCode;
						$sql = 
						"INSERT INTO userActivityLog ".
						"SET GUID = '".$link->real_escape_string($this->userGUID)."', ".
						"activityID = 1, ". //1 is an Insert
						"activityDate = NOW()";
						if($link->query($sql)){
							$this->setSessionVariables();
							$retval = true;
						}else{
							//TODO - Add error handling
							if(strlen($link->error) > 0){
								echo "Error: ".$link->error."<br />";
							}else{
								$retval = true;
							}
						}
					}else{
						//TODO - Add error handling 
						if(strlen($link->error) > 0){
							echo "Error: ".$link->error."<br />";
						}else{
							$retval = true;
						}
					}
				}else{
					echo "Error: ".$link->error."<br />";
				}
			}	
		}		
		return $retval;	
	}
	
	function doLogin($identifier,$pword){
		global $link,$msg;
		$retval = false;
		$sql = 	//"SELECT ppl.personID, ppl.firstName, ppl.lastName, ppl.typeID, ppl.confirmStatus ".
					"SELECT userGUID ".
					"FROM users ".
					"WHERE email = '".$link->real_escape_string($identifier)."' ".
					"AND password = '".$link->real_escape_string($pword)."' ".
					"AND userStatus = 'ACTIVE'";
		//echo $sql."<br />";
		if($login = $link->query($sql)){
			if($login->num_rows > 0){
				$row = $login->fetch_assoc();
				$this->userGUID = $row["userGUID"];
				//echo "Now userGUID is ".$this->userGUID."<br />";
				if(strlen($this->userGUID) > 0){
					$this->getPersonData($this->userGUID);
					$this->setSessionVariables();
				}
				$sql = 
				"INSERT INTO userActivityLog ".
				"SET GUID = '".$link->real_escape_string($this->userGUID)."', ".
				"activityID = 3, ". //5 is a Login
				"activityDate = NOW()";
				if($link->query($sql)){
					$retval = true;
				}else{
					//TODO - Add error handling
					if(strlen($link->error) > 0){
						echo "Error: ".$link->error."<br />";
					}else{
						$retval = true;
					}
				}
				$retval = true; 
			}else{
				$msg .=
				"We could not log you in with the email address and password you supplied.<br />".
				"Please try again<br />";
			}
		}else{
			//$db->debug();
		}	
		return $retval;
	}
	
	function doActivate($identifier,$pword,$activationCode){
		global $link;
		$retval = false;
		$sql = 	//"SELECT ppl.personID, ppl.firstName, ppl.lastName, ppl.typeID, ppl.confirmStatus ".
					"SELECT userGUID ".
					"FROM users ".
					"WHERE email = '".$link->real_escape_string($identifier)."' ".
					"AND password = '".$link->real_escape_string($pword)."' ".
					"AND activationCode = '".$link->real_escape_string($activationCode)."'";
		//echo $sql."<br />";
		if($login = $link->query($sql)){
			$row = $login->fetch_assoc();
			$this->userGUID = $row["userGUID"];
			//echo "Now userGUID is ".$this->userGUID."<br />";
			
			
			if(strlen($this->userGUID) > 0){
				$sql = 
				"INSERT INTO userActivityLog ".
				"SET GUID = '".$link->real_escape_string($this->userGUID)."', ".
				"activityID = 2, ". //5 is a Login
				"activityDate = NOW()";
				if($link->query($sql)){
					$retval = true;
				}else{
					//TODO - Add error handling
					if(strlen($link->error) > 0){
						echo "Error: ".$link->error."<br />";
					}else{
						$retval = true;
					}
				}
				$this->getPersonData($this->userGUID);
				$this->setSessionVariables();
			}
			
			$sql = 
			"INSERT INTO userActivityLog ".
			"SET GUID = '".$link->real_escape_string($this->userGUID)."', ".
			"activityID = 3, ". //3 is a Login
			"activityDate = NOW()";
			if($link->query($sql)){
				$retval = true;
			}else{
				//TODO - Add error handling
				if(strlen($link->error) > 0){
					echo "Error: ".$link->error."<br />";
				}else{
					$retval = true;
				}
			}
			$retval = true; 
		}else{
			//$db->debug();
		}	
		return $retval;
	}
	
	function setSessionVariables(){
		global $_SESSION;
		$_SESSION["et_userGUID"] = $this->userGUID;
		$_SESSION["et_firstname"] = $this->firstName;
		$_SESSION["et_lastname"] = $this->lastName;
		$_SESSION["et_membersince"] = $this->profileCreated;
		$_SESSION["et_last_login"] = $this->lastLogin;
		$_SESSION["et_access"] = $this->typeID;
	}
	
	function clearSessionVariables(){
		global $_SESSION;
		unset($_SESSION["et_userGUID"]);
		unset($_SESSION["et_firstname"]);
		unset($_SESSION["et_lastname"]);
		unset($_SESSION["et_membersince"]);
		unset($_SESSION["et_last_login"]);
		unset($_SESSION["et_access"]);
	}
	
	function generateGUID($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	function forwardToPage($goToPage=""){
		if(strlen($goToPage) > 0){
			header("location:".$goToPage);
		}else{
			//echo "TypeID is ".$this->typeID."<br />";
			switch($this->typeID){
			case 1:
			case 2:
				//echo "Going to postings.php<br />";
				header("location:postings.php");
				break;
			case 3:
			case 6:
			case 7:
				header("location:clientsHome.php");
				break;
			case 4:
			case 8:	
				header("location:myprofile.php");
				break;
			default:
				header("location:login.php");	
		}
		}
	}
	
	function profileIncomplete(){
		global $link;
		$retval = 0;
		
		if($this->typeID == 4){
			//First we run a check to see if the user has started their profile
			/*
			$sql = 
			"SELECT ual.index ".
			"FROM userActivityLog ual ".
			"INNER JOIN activities act ON ual.activityID = act.activityID ".
			"WHERE ual.GUID = '".$this->userGUID."' ".
			"AND act.activityLabel = 'Started Profile'";
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
				//There is a User Activity Log entry that indicates the User has started to complete their profile so we run the check and send any message back
					
				}
			}
			*/
			$sql =
			"SELECT ff.fieldID AS fieldID, ".
			//"ff.formID AS formID, ".
			"CASE ".
			"	WHEN ff.formID = 3 THEN 4 ".
			"	WHEN ff.formID = 4 THEN 5 ".
			"	ELSE ff.formID ".
			"END AS formID, ".
			"ff.filterName AS theLabel, sortOrder AS sortOrder ".
			"FROM formFields ff ". 
			"WHERE NOT EXISTS ( ". 
			"	SELECT fieldID FROM formCandidateDetailsValues fv ". 
			"	WHERE idNum = '".$this->userGUID."' ". 
			"	AND fieldID = ff.fieldID ".
			") ". 
			"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ". 
			"AND ff.mandatory = 1 ".

			"UNION ".

			"SELECT 'RESUME' AS fieldID, '3' AS formID, 'Resume' AS theLabel, 1 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'RESUME' AND userGUID = '".$this->userGUID."') < 1 ".

			"UNION ".

			"SELECT 'HEADSHOT' AS fieldID, '6' AS formID, 'Head Shot' AS theLabel, 1 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'HEADSHOT' AND userGUID = '".$this->userGUID."') < 2 ".

			"UNION ".

			"SELECT 'BODYSHOT' AS fieldID, '6' AS formID,  'Body Shot' AS theLabel, 1 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'BODYSHOT' AND userGUID = '".$this->userGUID."') < 2 ".
			
			"UNION ".
			
			"SELECT ff.fieldID AS fieldID, ".
			//"ff.formID AS formID, ".
			"CASE ".
			"	WHEN ff.formID = 3 THEN 4 ".
			"	WHEN ff.formID = 4 THEN 5 ".
			"	ELSE ff.formID ".
			"END AS formID, ".
			"ff.filterName AS theLabel, sortOrder AS sortOrder ".
			"FROM formFields ff ".
			"WHERE NOT EXISTS (".
			"    SELECT fieldID FROM formCandidateDetailsValues fv ".
			"    WHERE idNum = '".$this->userGUID."' ".
			"    AND fieldID = ff.fieldID ".
			") ".
			"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ".
			"AND ff.fieldParent IS NOT NULL ".
			"AND (ff.mandatory = 'CONDITIONAL') ".
			"AND INSTR(ff.child_mandatory_value,(SELECT fieldValue FROM formCandidateDetailsValues WHERE idNum = '".$this->userGUID."' AND fieldID = ff.fieldParent)) > 0 ".
			
			"ORDER BY formID LIMIT 1";
			//echo $sql."<br />";
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
					$row = $result->fetch_assoc();
					$retval = $row["formID"];  //This gives us a way to determine which piece of the profile form is incomplete and send the user directly there.
				}
			}
		}
		
		return $retval;
	}
	
	function getIncompleteFields(){
		global $link;
		$retval = "";
		if($this->typeID == 4){
			$sql =
			"SELECT ff.fieldID AS fieldID, 
			CASE 
				WHEN ff.formID = 3 THEN 4
				WHEN ff.formID = 4 THEN 5
				ELSE ff.formID
			END AS formID, 
			ff.filterName AS theLabel ".
			"FROM formFields ff ". 
			"WHERE NOT EXISTS ( ". 
			"	SELECT fieldID FROM formCandidateDetailsValues fv ". 
			"	WHERE idNum = '".$this->userGUID."' ". 
			"	AND fieldID = ff.fieldID ".
			") ". 
			"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ". 
			"AND ff.mandatory = 1 ".

			"UNION ".

			"SELECT 'RESUME' AS fieldID, '3' AS formID, 'Resume' AS theLabel ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'RESUME' AND userGUID = '".$this->userGUID."') < 1 ".

			"UNION ".

			"SELECT 'HEADSHOT' AS fieldID, '6' AS formID, 'Head Shot' AS theLabel ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'HEADSHOT' AND userGUID = '".$this->userGUID."') < 2 ".

			"UNION ".

			"SELECT 'BODYSHOT' AS fieldID, '6' AS formID,  'Body Shot' AS theLabel ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'BODYSHOT' AND userGUID = '".$this->userGUID."') < 2 ".

			"ORDER BY formID LIMIT 1";
			
			//echo $sql."<br />";
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
					$retval = $result;
				}
			}
		}
		return $retval;
	}
}

?>