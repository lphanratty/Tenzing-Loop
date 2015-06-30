<?php
include_once("include/dblink.php");
$userGUID = isset($_REQUEST["UID"])?$_REQUEST["UID"]:"";
$submitTable = isset($_REQUEST["sTable"])?$_REQUEST["sTable"]:"";
$submitField = isset($_REQUEST["sField"])?$_REQUEST["sField"]:"";
$submitValue = isset($_REQUEST["sValue"])?$_REQUEST["sValue"]:"";

$retval = "";
$retval["GUID"] = $userGUID;
$retval["sTable"] = $submitTable;
$retval["sField"] = $submitField;
$retval["sValue"] = $submitValue;

//Now we build the UPDATE SQL
if(strlen($userGUID) > 0 && strlen($submitTable) > 0 && strlen($submitField) > 0 && strlen($submitValue) > 0){
	$sql = 
	"UPDATE ".$submitTable." ".
	"SET ".$submitField." = '".$submitValue."' ".
	"WHERE userGUID = '".$userGUID."'";
	
	$retval["SQL"] = $sql;
	if($link->query($sql)){
		if($link->affected_rows > 0){
			$retval["status"] = "Updated";
		}else{
			$retval["status"] = "Not Updated";
		}
	}else{
		$retval["status"] = "Error: ".$sql;
	}
}

echo json_encode($retval); 
?>