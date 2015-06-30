<?php
include_once("init.php");
include_once("classes/et_member.php");

$formMsg = "";
$content = "";
if(isset($_REQUEST["GUID"])){
	$GUID = $_REQUEST["GUID"];
}else{
	$GUID = $_SESSION["et_userGUID"];
}
$member = new etMember($GUID);

if(isset($_POST["isSubmitted"])){
	$sql = 
	"UPDATE users ".
	"SET userStatus = 'INACTIVE' ".
	"WHERE userGUID = '".$GUID."'";
	if($link->query($sql)){
		if($link->affected_rows > 0){
			$content .=
			$member->firstName." ".$member->lastName." has been successfully deleted<br />";
		}else{
			"Unable to delete ".$member->firstName." ".$member->lastName."<br />";
		}
	}else{
		"Unable to delete ".$member->firstName." ".$member->lastName."<br />";
	}
}else{
	$content .= showForm($GUID,$member->firstName,$member->lastName,$formMsg);
	
}
if(strlen($action) > 0){
	if(strcasecmp($action,"DEL") == 0){
		if($posting->deletePost()){
			header("location:postings.php");
		}
	}
}else{
	
}

include("header.php");
echo $content; 
include("footer.php");

function showForm($GUID,$firstName, $lastName,$formMsg){
	$retval = "";
	$retval .=
	"<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">\r\n".
	"	Are you sure you want to delete ".$firstName." ".$lastName."?<br /><br />\r\n".
	"	<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
	"	<input type=\"hidden\" name=\"GUID\" value=\"".$GUID."\" />\r\n".
	"	<input type=\"submit\" name=\"btnSubmit\" value=\"DELETE\" class=\"btn_red\" />\r\n".
	"</form>\r\n";
	return $retval;
}
?>