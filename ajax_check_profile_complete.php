<?php
include_once("include/dblink.php");
$userGUID = isset($_REQUEST["UID"])?$_REQUEST["UID"]:"";
$userType = isset($_REQUEST["UT"])?$_REQUEST["UT"]:"";
$retval["GUID"] = $userGUID;
$retval["userType"] = $userType;
$retval["msg"] = "";
if($userType == 4){
	//First we run a check to see if the user has started their profile
	$sql = 
	"SELECT ual.index ".
	"FROM userActivityLog ual ".
	"INNER JOIN activities act ON ual.activityID = act.activityID ".
	"WHERE ual.GUID = '".$userGUID."' ".
	"AND act.activityLabel = 'Started Profile'";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			//There is a User Activity Log entry that indicates the User has started to complete their profile so we run the check and send any message back
			$sql =
			"SELECT ff.fieldID AS fieldID, 
			CASE 
				WHEN ff.formID = 3 THEN 4
				WHEN ff.formID = 4 THEN 5
				ELSE ff.formID
			END AS formID,
			ff.filterName AS theLabel, sortOrder AS sortOrder ".
			"FROM formFields ff ". 
			"WHERE NOT EXISTS ( ". 
			"SELECT fieldID FROM formCandidateDetailsValues fv ". 
			"WHERE idNum = '".$userGUID."' ". 
			"AND fieldID = ff.fieldID ".
			") ". 
			"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ". 
			"AND ff.mandatory = 1 ".

			"UNION ".

			"SELECT 'RESUME' AS fieldID, '3' AS formID, 'You must upload a Resume' AS theLabel, 1 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'RESUME' AND userGUID = '".$userGUID."') < 1 ".

			"UNION ".

			"SELECT 'HEADSHOT' AS fieldID, '6' AS formID, 'You must upload 2 Head Shots' AS theLabel, 1 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'HEADSHOT' AND userGUID = '".$userGUID."') < 2 ".

			"UNION ".

			"SELECT 'BODYSHOT' AS fieldID, '6' AS formID,  'You must upload 2 Body Shots' AS theLabel, 2 AS sortOrder ".
			"FROM candidateFiles ".
			"WHERE (SELECT COUNT(userGUID) FROM candidateFiles WHERE fileType = 'BODYSHOT' AND userGUID = '".$userGUID."') < 2 ".
			
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
			"    WHERE idNum = '".$userGUID."' ".
			"    AND fieldID = ff.fieldID ".
			") ".
			"AND (ff.formID = 1 OR ff.formID = 2 OR ff.formID = 3 OR ff.formID = 4) ".
			"AND ff.fieldParent IS NOT NULL ".
			"AND (ff.mandatory = 'CONDITIONAL') ".
			"AND (SELECT INSTR(ff.child_mandatory_value,(SELECT fieldValue FROM formCandidateDetailsValues WHERE idNum = '".$userGUID."' AND fieldID = ff.fieldParent))) > 0 ".
			"ORDER BY formID, sortOrder";
			
			if($result = $link->query($sql)){
				if($result->num_rows > 0){
					$retval["msg"] .=
					"You are so close...but you are still missing the following information.<br /><br />";
					/*
					$retval .=
					$sql."<br /><br />";
					*/
					while($row = $result->fetch_assoc()){
						$retval["msg"] .=
						"<strong>".$row["theLabel"]."</strong><br />";
					}
					$retval["msg"] .=
					"<br />".
					"In order to have access to the full site including up to date listings of available jobs, you will need to provide this info.<br /><br />".
					"If you are not ready to provide, you can always save your current session and come back to your profile when ready to fill out and submit<br /><br />";
				}
			}
		}
	}
}

//echo $sql."<br />";

$retval["SQL"] = $sql;

echo json_encode($retval);

?>