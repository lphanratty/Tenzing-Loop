<?php
include_once("init.php");
include_once("include/inc_form_functions.php");
include_once("classes/et_person.php");
include_once("classes/et_job_posting.php");
include_once("classes/email_message.php");
include_once("classes/et_emails.php");
include_once("include/posting_application.php");

$formMsg = "";
$postingID = isset($_REQUEST["PID"])?$_REQUEST["PID"]:0;
$action = isset($_REQUEST["ACT"])?$_REQUEST["ACT"]:"";

$posting = new etJobPosting($postingID);
if(strlen($action) > 0){
	if(strcasecmp($action,"DEL") == 0){
		if($posting->deletePost()){
			header("location:postings.php");
		}
	}elseif(strcasecmp($action,"APPLY") == 0){
		$formMsg = submitApplication($postingID);
		$content = $posting->displayData();
	}
}else{
	$content = $posting->displayData();
}

include("header.php");
echo $content; 
include("footer.php");
?>