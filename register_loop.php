<?php
include_once("init.php");
include_once("classes/et_person.php");
include_once("classes/et_form_generator.php");
include_once("classes/email_message.php");
include_once("classes/et_emails.php");

$userGUID = "";  //NOTE: THis will actually be the users GUID
$firstName = (isset($_POST["first_name"])?$_POST["first_name"]:"");
$lastName = (isset($_POST["last_name"])?$_POST["last_name"]:"");
$email = (isset($_POST["email"])?$_POST["email"]:"");
$password = (isset($_POST["password"])?$_POST["password"]:"");
$passwordConfirm = (isset($_POST["password_confirm"])?$_POST["password_confirm"]:"");
$emailOptin = (isset($_POST["email_optin"])?$_POST["email_optin"]:0);
$emailOptinPost = 1;
$candidateFieldHTML = "";
$msg = "";

$contact = new etForminator(1,$userGUID);
$contactFields = $contact->getFormFields();

if(isset($_POST["isSubmitted"])){
	if(isset($_POST["email_optin"])){
		$emailOptinPost = 1;
	}else{
		$emailOptinPost = 0;
	}
	$etUser = new etPerson();
	$etUser->firstName = $firstName;
	$etUser->lastName = $lastName;
	$etUser->email = $email;
	$etUser->emailOptin = $emailOptin;
	$etUser->password = $password;
	$etUser->typeID = 4;
	if($etUser->addDataToDB()){
		$contact->idNum = $etUser->userGUID;
		$contactFields = $contact->getFormFields();
		//Now we add the candidate details to the DB
		foreach($contactFields as $fieldID=>$fieldData){
			if(!$contact->submitValue($fieldID,$fieldData, $_POST["fld_".$fieldID])){
				$msg .= 
				"We could not enter data for ".$fieldData["fieldLabel"]."<br />\r\n";
			}
		}
		if(strlen($msg) == 0){
			$toArray = array($etUser->email => $etUser->firstName." ".$etUser->lastName);
			$replaceFields = array("[[firstName]]" => $etUser->firstName,
									"[[companyName]]" => "The Loop Agency",
									"[[emailLink]]" => "<a href=\"http://www.theloopagency.ca/recruit/myprofile.php\">http://www.theloopagency.ca/recruit/myprofile.php</a>"
									);
			$welcomeMsg = new etEmail("REGISTER_CONFIRM",$toArray,$replaceFields);
			$welcomeMsg->sendEmails();
			//echo $welcomeMsg->emailDisplay();
		}else{
			echo "Msg is ".$msg."<br />";
		}
		$msg .=
		"Thanks for signing up! An email has been sent to you now for verification.<br />";
		//Now we have a userGUID so we can add the candidate details
		
	}else{
		$msg .=
		"There was an error processing your registration<br />";
	}
	
}



include("header.php");
?>
<p>
	<span class="header-style">Welcome to The Loop Agency</span>
</p>
<p>
	We are a national staffing and experiential marketing agency that connects brands with their consumers through interactions with people just like you 
</p>
<p>
	Here’s the scoop. We have jobs all over Canada and in order to fit the right people with the right promotions we need to spend a little time getting to know you better….But before we do, you need to fill out the below to gain access to the site. 
</p>
<?php
	if(strlen($msg) > 0){
		echo
		"<p style=\"font-weight:bold;color:red;\">\r\n".
		$msg.
		"</p>\r\n";
	}
?>
<form name="register_person" id="register_person" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
	<label for="legallywork" style="width:250px;text-align:left;">Are you legally entitled to work in Canada?</label>
	<select name="legallywork" id="legallywork" class="style_select" style="width:100px;">
		<option value=""> -- Select -- </option>
		<option value=1>Yes</option>
		<option value=0>No</option>
	</select>
	<div class="clear" style="height:0px;"></div>
	<fieldset>
		<legend>Contact Information</legend>
		<label for="first_name">First Name</label>
		<input type="text" name="first_name" id="first_name" class="style_text_field" value="<?php echo (strlen($firstName) > 0?$firstName:"first name"); ?>" onfocus="clearField('first_name', 'first name');" /><br />
		<label for="last_name">Last Name</label>
		<input type="text" name="last_name" id="last_name" class="style_text_field" value="<?php echo (strlen($lastName) > 0?$lastName:"last name"); ?>" onfocus="clearField('last_name', 'last name');" /><br />
		<?php
			$candidateFieldHTML = "";
			$contactFields = $contact->getFormFields();
			//print_r($fields);
			foreach($contactFields as $fieldID=>$fieldData){
				$candidateFieldHTML .= $contact->createField($fieldID,$fieldData);
			} 
			echo $candidateFieldHTML;
			$candidateFieldHTML = "";
		?>
	</fieldset>
	<fieldset>
		<legend>Login Information</legend>
		<label for="email">Email </label>
		<input type="text" name="email" id="email" class="style_text_field" value="<?php echo (strlen($email) > 0?$email:"Email Address"); ?>" onfocus="clearField('email', 'Email Address');" /><br />
		<label for="password">Password </label>
		<input type="password" name="password" id="password" class="style_text_field" value="<?php echo (strlen($password) > 0?$password:""); ?>" /><br />
		<label for="password">Confirm Password </label>
		<input type="password" name="password_confirm" id="password_confirm" class="style_text_field" value="<?php echo (strlen($passwordConfirm) > 0?$passwordConfirm:""); ?>" /><br />
		<input type="checkbox" name="email_optin" id="email_optin" value=1 <?php echo ($emailOptinPost == 1?"checked ":""); ?> style="float:left;margin-right:5px;"/>
		<div>Check this box to have important account information sent to your inbox<br />
		(If you don’t opt-in we won’t be able to communicate with you via email)</div>
		<div class="clear"></div>
	</fieldset>
		<input type="hidden" name="isSubmitted" value="1" />
	<div style="width:422px;text-align:right;"><input type="submit" class="btn_blue" style="margin-right:0px;" value="Register" /></div>
</form>
<script type="text/javascript">checkLegallyWork();</script>
<?php
include("footer.php");
?>