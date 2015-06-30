<?php

function submitApplication($postingID){
	global $link, $etUser;
	$returnMessage = "";
	if($postingID > 0){
		
		$sql = 
		"SELECT * FROM jobPostings jp ".
		"WHERE postingID = ".$postingID;
		
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				//echo "Program Name is ".$row["programName"]."<br />";
			}
		}
		
		$toArray = array($row["adminEmail"] => "Loop Agency Administrators");
		$replaceFields = array("[[firstName]]" => $etUser->firstName,
								"[[lastName]]" => $etUser->lastName,
								"[[postingName]]" => $row["programName"],
								"[[profileLink]]" => "<a href=\"http://www.theloopagency.ca/recruit/myprofile.php?GUID=".$etUser->userGUID."\">click here</a>"
								);
		$welcomeMsg = new etEmail("APPLICATION",$toArray,$replaceFields);
		if($welcomeMsg->sendEmails()){
			$returnMessage = 
			"Your application for ".$row["programName"]." has been successfully submitted<br />";
		}else{
			$returnMessage = 
			"We're sorry. An error has occurred in attempting to submit your application for ".$row["programName"]."<br /><br />".
			"Please notify one of our administrators about this error<br /><br />";
		}
		
		//Now we put the application into the database
		$sql = 
		"INSERT INTO applications ".
		"SET userGUID = '".$etUser->userGUID."', ".
		"postingID = ".$postingID.", ".
		"applicationDate = NOW()";
		$link->query($sql);
	}
	return $returnMessage;
}

?>