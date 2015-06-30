<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/Toronto');
include_once("include/dblink.php");
/*
//Set up our connection info
$DBServer = 'localhost'; // e.g 'localhost' or '192.168.1.100'
$DBUser   = 'eventtracks';
$DBPass   = 'jenncook';
$DBName   = 'thecompany';
$link = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

if ($link->connect_error) {
  trigger_error('Database connection failed: '  . $link->connect_error, E_USER_ERROR);
}
*/
//echo "Your GUID is ".$_SESSION["et_userGUID"]."<br />";
if(stripos($_SERVER['REQUEST_URI'],"index.php") === false && stripos($_SERVER['REQUEST_URI'],"register.php") === false  && stripos($_SERVER['REQUEST_URI'],"login.php") === false && stripos($_SERVER['REQUEST_URI'],"forgotpw.php") === false ){
	if((!isset($_SESSION["et_userGUID"]) || strlen($_SESSION["et_userGUID"]) == 0)){
		header("location:index.php?GOTO=http://".$_SERVER["SERVER_NAME"].str_replace("&","~",$_SERVER['REQUEST_URI']));
	}else{
		include_once("classes/et_person.php");
		$etUser = new etPerson($_SESSION["et_userGUID"]);
		//echo "SESSION User GUID is ".$_SESSION["et_userGUID"]."<br />";
		$profileIncomplete = $etUser->profileIncomplete();   //Returns 0 if the profile IS complete and the FIRST profile form section that is incomplete if the profile is NOT complete
		if(!$etUser->isActivated){
			header("location:login.php?AR=1");
		}elseif($etUser->typeID <> 1 && $profileIncomplete && stripos($_SERVER['REQUEST_URI'],"completeMyProfile.php") === false && stripos($_SERVER['REQUEST_URI'],"profileUpdate.php") === false){
			setcookie("numProfileWarnings", "", time()-3600);
			if($profileIncomplete == 1){
				header("location:profileUpdate.php?SEC=2");
			}else{
				header("location:completeMyProfile.php?SEC=".$profileIncomplete."&RC=1");
			}
			
			//echo "Profile section shopuld be ".$profileIncomplete."<br />";
		}
	}
}

function showIncompleteProfile(){
	
}

?>