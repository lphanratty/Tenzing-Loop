<?php
$pagetitle = isset($pageTitle)?$pageTitle:"The Loop Agency";
include_once("include/navigation.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title><?php echo $pagetitle; ?></title>
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link href="css/uploadfile.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="./js/common.js"></script>
	<script type="text/javascript" src="js/jquery.maskedinput.js"></script>
	<script type="text/javascript" src="js/jquery.uploadfile.js"></script>
	
	<?php
	if(isset($jscript) && $jscript != ""){
		$scripts = explode("|",$jscript);
		for($i=0;$i<count($scripts);$i++){
			echo "<script type=\"text/javascript\" src=\"".$scripts[$i]."\"></script>\r\n";	
		}	
	}
	if(isset($pagescript) && strlen($pagescript) > 0){
		echo 	"<script type=\"text/javascript\">\r\n".
				"	$(document).ready(function() {\r\n".
						$pagescript."\r\n".
				"	});\r\n".
				"</script>\r\n";
	}
	?>
</head>
<body>
<div id="msgBox">
		<div class="msgBoxOverlay">
			&nbsp;
		</div>
		<div id="msgContentHolder">
			<div id="msgContent">
				<a id='closeModal'><img src='images/modal_close.png' /></a>
				<div id="msgText"></div>
			</div>
		</div>
</div>
<div id="container">	
	<div id="header_container">
		<img src="images/thecompany_logo.png" id="header_logo"/>
		<div id="header_login_data">
			<?php
				echo getLoginData();
			?>
		</div>
		<div class="clear"></div>
		<?php echo $menu_content; ?>
	</div>
	<div class="content_container">		
	
<?php

function getLoginData(){
	global $etUser;
	$retval = "";
	/*
	if(isset($_SESSION["et_userGUID"]) && strlen($_SESSION["et_userGUID"]) > 0){
		$retval .=
		"Welcome ".$_SESSION["et_firstname"]." ".$_SESSION["et_lastname"]."<br />".
		(strlen($_SESSION["et_membersince"]) > 0?"Member since: ".date("M jS, Y",strtotime($_SESSION["et_membersince"]))."<br />".
		(strlen($_SESSION["et_last_login"]) > 0?"Last Login: ".date("M jS, Y g:i A",strtotime($_SESSION["et_last_login"]))."<br />":"").
		"<a href=\"login.php?&LO=1\">Log Out</a>\r\n";
	}
	*/
	if(isset($etUser->userGUID) && strlen($etUser->userGUID) > 0){
		$retval .=
		"Welcome ".$etUser->firstName." ".$etUser->lastName."<br />".
		(strlen($etUser->profileCreated) > 0?"Member since: ".date("M jS, Y",strtotime($etUser->profileCreated))."<br />":"").
		(strlen($_SESSION["et_last_login"]) > 0?"Last Login: ".date("M jS, Y g:i A",strtotime($_SESSION["et_last_login"]))."<br />":"").
		"<a href=\"login.php?LO=1\">Log Out</a>\r\n";
	}
	/*
	if(stripos($_SERVER['REQUEST_URI'],"completeMyProfile.php") !== false && $etUser->typeID == 4){
		$retval .=
		"<p>\r\n".
		"		<input type=\"submit\" name=\"SAVE\" class=\"btn_clear\" style=\"margin-right:0px;\" value=\"SAVE CURRENT SESSION\" />\r\n".
		"</p>\r\n";
	}
	*/
	return $retval;
}
?>