<?php
include_once("init.php");
include_once("classes/et_person.php");

$email = (isset($_REQUEST["et_email"])?$_REQUEST["et_email"]:"");
$action = isset($_REQUEST["ACT"])?$_REQUEST["ACT"]:"";
$password = (isset($_POST["et_pword"])?$_POST["et_pword"]:"");
$activationCode = (isset($_REQUEST["AC"])?$_REQUEST["AC"]:"");
$activationRequired = (isset($_REQUEST["AR"])?$_REQUEST["AR"]:0);
$goToPage = (isset($_REQUEST["GOTO"])&& strlen($_REQUEST["GOTO"]) > 0?str_replace("~","&",$_REQUEST["GOTO"]):"");
$logout = (isset($_GET["LO"])&& $_GET["LO"] == 1?true:false);

if($logout){
	session_destroy();	
	session_start();
}
$msg = "";

if(strlen($activationCode) > 0){
	$sql = 
	"SELECT email FROM users ".
	"WHERE activationCode = '".$link->real_escape_string($activationCode)."'";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$loginEmail = $row["email"];
		}
	}
}else{
	$loginEmail = (strlen($email) > 0?$email:""); 
}


if(isset($_POST["loginSubmitted"])){
	//echo "Login is Submitted<br />";
	$etUser = new etPerson();
	if(strlen($activationCode) > 0){
		$loggedIn = $etUser->doActivate($email,$password,$activationCode);
	}else{
		$loggedIn = $etUser->doLogin($email,$password);
	}
	if($loggedIn){
		$profileIncomplete = $etUser->profileIncomplete();   //Returns 0 id the profile IS complete and the FIRST profile form section that is incomplete if the profile is NOT complete
		//Check to see if the user has activated their account
		if(!$etUser->isActivated){
			//Do Nothing - we want the form to display WITH the activation field
			$activationRequired = 1;
			//echo "You need activation<br />";
		}elseif($etUser->typeID != 1 && $profileIncomplete){
			if($profileIncomplete == 1){
				header("location:profileUpdate.php?SEC=2");
			}else{
				header("location:completeMyProfile.php?SEC=".$profileIncomplete."&RC=1");
			}
			//header("location:completeMyProfile.php?SEC=".$profileIncomplete);
			//echo "Profile Section should be ".$profileIncomplete."<br />";
		}else{
			$etUser->forwardToPage($goToPage);
		}
		//echo "You are now logged in<br />";
	}else{
		
	}
}
include("header.php");
?>
<div style="width:700px;margin:30px auto;position:relative;">
	<div class="rounded-box" style="width:300px;margin:200px auto;">
		<form name="login_form" id="login_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
			<span class="header-style">Been here before?</span>
			<p>
				If you've already created a personal login, then please enter your information below to continue.
			</p>
			<?php
				if(strlen($msg) > 0){
					echo 
					"<p style=\"color:red;\">\r\n".
					$msg."\r\n".
					"</p>\r\n";
				}
			?>
			<p>
				<input type="text" name="et_email" id="et_email" class="style_text_field" value="<?php echo (strlen($loginEmail) == 0?"email/username":$loginEmail); ?>" onfocus="clearField('et_email', 'email/username');" /><br />
				<input type="password" name="et_pword" id="et_pword" class="style_text_field" value="" /><br />
				<?php
					if($activationRequired == 1){
						echo 
						"<span style=\"color:red;\">\r\n".
						"	In order to proceed, please click the link from the Welcome email you were sent, or enter the Activation code ".
						"	from that email in the field below\r\n".
						"</span><br />\r\n".
						"<input type=\"text\" name=\"AC\" id=\"AC\" class=\"style_text_field\" value=\"".(strlen($activationCode) > 0?$activationCode:"Activation Code")."\" onfocus=\"clearField('AC', 'Activation Code');\"/><br />";
					}
					if(strlen($action) > 0){
						if(strcasecmp($action,"PWR") == 0){
							echo 
						"<span style=\"color:red;\">\r\n".
						"	An email with your password has been sent to you.<br />\r\n".
						"</span><br />\r\n";
						}
					}
				?>
			</p>
			<p style="float:left;width:49%;">
				<a href="forgotpw.php">Forgot Password?</a>
			</p>
			<p style="float:right;width:49%;">
				<input type="hidden" name="loginSubmitted" value="1" />
				<input type="submit" class="btn_blue" value="Login" />
			</p>
			<div class="clear"></div>
		</form>
	</div>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

