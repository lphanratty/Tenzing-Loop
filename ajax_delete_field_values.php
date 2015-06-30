<?php
include_once("include/dblink.php");
$userGUID = isset($_REQUEST["UID"])?$_REQUEST["UID"]:"";
$fieldList = isset($_REQUEST["fList"])?$_REQUEST["fList"]:"";

$retval = "";
$retval["GUID"] = $userGUID;
$retval["fieldList"] = $fieldList;

$retval["msg"] = "";

if(strlen($fieldList) > 0 && strlen($userGUID) > 0){
	$arrFields = explode("~",$fieldList);
	if(is_array($arrFields)){
		$sql = 
		"SELECT ff.filterName, ff.fieldParent, fv.fieldID, fv.fieldValue FROM formCandidateDetailsValues fv ".
		"INNER JOIN formFields ff ON fv.fieldID = ff.fieldID ".
		"WHERE fv.idNum = '".$userGUID."' ".
		"AND ((";
		$index = 0;
		foreach($arrFields as $field){
			$sql .=
			($index > 0?"OR ":"")."fv.fieldID = ".$field." ";
			$index++;
		}
		$sql .=
		"	) OR(";
		$index = 0;
		foreach($arrFields as $field){
			$sql .=
			($index > 0?"OR ":"")."ff.fieldParent = ".$field." ";
			$index++;
		}
		$sql .=
		"    )".
		")";
	}else{
		$sql =
		"SELECT ff.filterName, ff.fieldParent, fv.fieldID, fv.fieldValue FROM formCandidateDetailsValues fv ".
		"INNER JOIN formFields ff ON fv.fieldID = ff.fieldID ".
		"WHERE fv.idNum = '".$userGUID."' ".
		"AND (fv.fieldID = ".$arrFields." OR ff.fieldParent = ".$arrFields.") ".
		"ORDER BY ff.fieldParent, fv.fieldID";
	}
	$retval["msg"] .= $sql."<br />";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$delSQL = 
				"DELETE FROM formCandidateDetailsValues ".
				"WHERE idNum = '".$userGUID."' ".
				"AND fieldID = ".$row["fieldID"];
				if($link->query($delSQL)){
					
				}
			}
		}
		$retval["msg"] .= "Deleting!<br />";
	}else{
		$retval["msg"] .= "No Luck with the query<br />";
	}
}

echo json_encode($retval); 
?>