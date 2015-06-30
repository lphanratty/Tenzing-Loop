<?php
include_once("init.php");
include_once("include/inc_form_functions.php");
include_once("classes/et_person.php");
include_once("classes/et_job_posting.php");

$postingID = isset($_REQUEST["PID"])?$_REQUEST["PID"]:0;
$posting = new etJobPosting($postingID);
$formMsg = "";
//echo "Hello There<br />";
if(isset($_POST["isSubmitted"])){
	$posting->postingStatus = isset($_POST["posting_status"])?$_POST["posting_status"]:"ACTIVE";
	$posting->programName = isset($_POST["program_name"])?$_POST["program_name"]:"";
	$posting->province = isset($_POST["province"])?$_POST["province"]:"";
	$posting->cities = isset($_POST["cities"])?$_POST["cities"]:"";
	$posting->timing = isset($_POST["program_timing"])?$_POST["program_timing"]:"";
	$posting->rolesRequired = isset($_POST["required_roles"])?$_POST["required_roles"]:"";
	$posting->postingDetails = isset($_POST["details"])?$_POST["details"]:"";
	$posting->adminOwner = isset($_POST["admin_owner"])?$_POST["admin_owner"]:$etUser->userGUID;
	$posting->getAdminEmail();
	
	$posting->addDataToDB();
}

//echo "Displaying Form<br />";
$content = $posting->displayForm();

include("header.php");
?>
<form name="job_posting" id="job_posting" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<?php 
	
	echo $content; 
?>
</form>
<?php
include("footer.php");
?>