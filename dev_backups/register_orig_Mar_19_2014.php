<?php
include_once("init.php");
include_once("classes/et_person.php");
include_once("classes/et_form_generator.php");
include_once("inc_form_functions.php");

$userGUID = "";  //NOTE: THis will actually be the users GUID
$firstName = (isset($_POST["first_name"])?$_POST["first_name"]:"");
$lastName = (isset($_POST["last_name"])?$_POST["last_name"]:"");
$email = (isset($_POST["email"])?$_POST["email"]:"");
$password = (isset($_POST["password"])?$_POST["password"]:"");
$passwordConfirm = (isset($_POST["password_confirm"])?$_POST["password_confirm"]:"");
$candidateFieldHTML = "";
$msg = "";

$contact = new etForminator(1,$userGUID);
$contactFields = $contact->getFormFields();
$personal = new etForminator(2,$userGUID);
$personalFields = $personal->getFormFields();
$work = new etForminator(3,$userGUID);
$workFields = $work->getFormFields();
$promotional = new etForminator(4,$userGUID);
$promotionalFields = $promotional->getFormFields();

 

if(isset($_POST["isSubmitted"])){
	$etUser = new etPerson();
	$etUser->firstName = $firstName;
	$etUser->lastName = $lastName;
	$etUser->email = $email;
	$etUser->password = $password;
	$etUser->typeID = 4;
	if($etUser->addDataToDB()){
		$msg .=
		"Your registration was successfully processed<br />";
		//Now we have a userGUID so we can add the candidate details

		$contact->idNum = $etUser->userGUID;
		$personal->idNum = $etUser->userGUID;
		$work->idNum = $etUser->userGUID;
		$promotional->idNum = $etUser->userGUID;
		//Now we add the candidate details to the DB
		foreach($contactFields as $fieldID=>$fieldData){
			$contact->submitValue($fieldID,$fieldData, $_POST["fld_".$fieldID]);
			//$frm->submitValue($fieldID,$fieldData);
		}
		foreach($personalFields as $fieldID=>$fieldData){
			$personal->submitValue($fieldID,$fieldData, $_POST["fld_".$fieldID]);
			//$frm->submitValue($fieldID,$fieldData);
		}
		foreach($workFields as $fieldID=>$fieldData){
			$work->submitValue($fieldID,$fieldData, $_POST["fld_".$fieldID]);
			//$frm->submitValue($fieldID,$fieldData);
		}
		foreach($promotionalFields as $fieldID=>$fieldData){
			$promotional->submitValue($fieldID,$fieldData, $_POST["fld_".$fieldID]);
			//$frm->submitValue($fieldID,$fieldData);
		}
		
	}else{
		$msg .=
		"There was an error processing your registration<br />";
	}
	
}



include("header.php");
?>
<p>
	<span class="header-style">Welcome to The Company</span>
</p>
<p>
	We are a national staffing and experiential marketing agency that connects brands with their consumers through interactions with people just like you 
</p>
<p>
	We have job opportunities across Canada that offers promotional representatives the chance to gain experience working with a wide range of brands and 
	products. For us to get to know you better and for you to have access to up to date job listings and opportunities that are tailored to you, 
	create your profile now with us 
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
	<select name="legallywork" class="style_select" style="width:100px;">
		<option value=""> -- Select -- </option>
		<option value=1>Yes</option>
		<option value=2>No</option>
	</select>
	<div class="clear" style="height:0px;"></div>
	<fieldset>
		<legend>Login Information</legend>
		<label for="email">Email </label>
		<input type="text" name="email" id="email" class="style_text_field" value="<?php echo (strlen($email) > 0?$email:"Email Address"); ?>" onfocus="clearField('email', 'Email Address');" /><br />
		<label for="password">Password </label>
		<input type="password" name="password" id="password" class="style_text_field" value="<?php echo (strlen($password) > 0?$password:""); ?>" /><br />
		<label for="password">Confirm Password </label>
		<input type="password" name="password_confirm" id="password_confirm" class="style_text_field" value="<?php echo (strlen($passwordConfirm) > 0?$passwordConfirm:""); ?>" /><br />
	</fieldset>
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
		<legend>Lets Get Personal</legend>
		<?php
			$candidateFieldHTML = "";
			$personalFields = $personal->getFormFields();
			foreach($personalFields as $fieldID=>$fieldData){
				$candidateFieldHTML .= $personal->createField($fieldID,$fieldData);
			} 
			echo $candidateFieldHTML;
		?>
	</fieldset>
	<fieldset>
		<legend>General Work History</legend>
		<?php
			$candidateFieldHTML = "";
			$workFields = $work->getFormFields();
			foreach($workFields as $fieldID=>$fieldData){
				$candidateFieldHTML .= $work->createField($fieldID,$fieldData);
			} 
			echo $candidateFieldHTML;
		?>
	</fieldset>
	<fieldset>
		<legend>Promotional History</legend>
		<?php
			$candidateFieldHTML = "";
			$promotionalFields = $promotional->getFormFields();
			foreach($promotionalFields as $fieldID=>$fieldData){
				$candidateFieldHTML .= $promotional->createField($fieldID,$fieldData);
			} 
			echo $candidateFieldHTML;
		?>
		<input type="hidden" name="isSubmitted" value="1" />
	</fieldset>
	<div style="width:422px;text-align:right;"><input type="submit" class="btn_blue" style="margin-right:0px;" value="Register" /></div>
</form>

<?php
include("footer.php");
?>