<?php
include_once("init.php");
include_once("classes/et_person.php");
include_once("classes/email_message.php");
include_once("classes/et_emails.php");

$loginEmail = isset($_POST["et_email"])?$_POST["et_email"]:"";
$formMsg = "";
$pword = "";
if(isset($_POST["isSubmitted"])){
	if(strlen($loginEmail) > 0){
		$sql = 
		"SELECT firstName, lastName, email, password ".
		"FROM users ".
		"WHERE email = '".$link->real_escape_string($loginEmail)."'";
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				$toArray = array($row["email"] => $row["firstName"]." ".$row["lastName"]);
				$replaceFields = array(	"[[firstName]]" => $row["firstName"],
										"[[emailLink]]" => "<a href=\"http://www.theloopagency.ca/recruit/login.php\">http://www.theloopagency.ca/recruit/login.php</a>",
										"[[pword]]" => $row["password"]
										);
				$email = new etEmail("FORGOT_PASSWORD",$toArray,$replaceFields);
				if($email->sendEmails()){
					//Successful, forward the user to the login page with a flag to indicate a message is required.
					header("location:login.php?ACT=PWR");
					/*
					$formMsg .= 
					"An email with your password has been sent to you.<br />\r\n";
					*/
				}else{
					$formMsg = $email->emailDisplay();
				}
			}else{
				$formMsg .=
				"Sorry - we could not find a matching email address in our database<br />\r\n";
			}
		}else{
			$formMsg .=
			"Sorry - there was an error processing your request<br />\r\n";
		}
	}
}

$content = buildForm($formMsg);


include("header.php");
echo $content;
include("footer.php");

function buildForm($formMsg){
	$retval = "";
	$retval =
	"<div style=\"width:700px;margin:30px auto;position:relative;\">\r\n".
	"	<div class=\"rounded-box\" style=\"width:300px;margin:200px auto;\">\r\n".
	(strlen($formMsg) > 0?"<p style=\"color:red;\">".$formMsg."</p>\r\n":"").
	"		<form name=\"forgotpw\" id=\"forgotpw\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">\r\n".
	"			<span class=\"header-style\">Forgot your Password?</span>\r\n".
	"			<p>\r\n".
	"				Please enter the email address that you used to sign up, and we will email your password to you.\r\n".
	"			</p>\r\n".
	"			<p>\r\n".
	"				<input type=\"text\" name=\"et_email\" id=\"et_email\" class=\"style_text_field\" value=\"".(strlen($loginEmail) == 0?"email address":$loginEmail)."\" onfocus=\"clearField('et_email', 'email address');\" /><br />\r\n".
	"			</p>\r\n".
	"			<p style=\"width:250px;text-align:right;\">\r\n".
	"				<input type=\"hidden\" name=\"isSubmitted\" value=\"1\" />\r\n".
	"				<input type=\"submit\" class=\"btn_blue\" value=\"Request Password\" />\r\n".
	"			</p>\r\n".
	"			<div class=\"clear\"></div>\r\n".
	"		</form>\r\n".
	"	</div>\r\n".
	"</div>\r\n".
	"<div class=\"clear\"></div>\r\n";
	return $retval;
}
?>