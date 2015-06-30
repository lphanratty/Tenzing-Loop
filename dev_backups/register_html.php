<?php
include_once("init.php");
include_once("classes/et_person.php");
include_once("inc_form_functions.php");
$userGUID = "1hkZrILwg9";  //NOTE: THis will actually be the users GUID
$firstName = (isset($_POST["first_name"])?$_POST["first_name"]:"");
$lastName = (isset($_POST["last_name"])?$_POST["last_name"]:"");
$email = (isset($_POST["email"])?$_POST["email"]:"");
$password = (isset($_POST["password"])?$_POST["password"]:"");
$passwordConfirm = (isset($_POST["password_confirm"])?$_POST["password_confirm"]:"");
$telephone = (isset($_POST["telephone"])?$_POST["telephone"]:"");
$mobile = (isset($_POST["mobile"])?$_POST["mobile"]:"");
$address = (isset($_POST["address"])?$_POST["address"]:"");
$addressUnit = (isset($_POST["addressUnit"])?$_POST["addressUnit"]:"");
$city = (isset($_POST["city"])?$_POST["city"]:"");
$province = (isset($_POST["province"])?$_POST["province"]:"");
$arrCandidateFields = "";
$msg = "";

$sql = 
"SELECT frm.formName, frm.formLabel, ".
"ff.fieldID, ff.fieldTypeID, ff.formID, ff.sortOrder, ff.mandatory, ff.validation, ".
"fft.formFieldTypeID, fft.formFieldTypeName, fft.formFieldTypeLabel, fft.formFieldFormat, ".
"fv.fieldValue ".
"FROM forms frm ".
"INNER JOIN formFields ff ON frm.formID = ff.formID ".
"INNER JOIN formFieldTypes fft ON fft.formFieldTypeID = ff.fieldTypeID ".
"LEFT JOIN formCandidateDetailsValues fv ON (fv.fieldID = ff.fieldID AND fv.GUID = '".$userGUID."') ".
"WHERE frm.formID = 1 ".
"ORDER BY ff.sortOrder";

echo $sql."<br />";

if($result = $link->query($sql)){
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			foreach($row AS $fldName=>$fldVal){
				if(strcasecmp($fldName,"fieldID") !== 0){
					$arrCandidateFields[$row["fieldID"]][$fldName] = $fldVal;
				}
			}
		}
	}
} 

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
<form style="width:500px;" name="register_person" id="register_person" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
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
		<input type="password" name="password" id="password" class="style_text_field" value="<?php echo (strlen($password) > 0?$password:""); ?>"" /><br />
		<label for="password">Confirm Password </label>
		<input type="password" name="password_confirm" id="password_confirm" class="style_text_field" value="<?php echo (strlen($passwordConfirm) > 0?$passwordConfirm:""); ?>"" /><br />
	</fieldset>
	<fieldset>
		<legend>Contact Information</legend>
		<label for="first_name">First Name</label>
		<input type="text" name="first_name" id="first_name" class="style_text_field" value="<?php echo (strlen($firstName) > 0?$firstName:"first name"); ?>" onfocus="clearField('first_name', 'first name');" /><br />
		<label for="last_name">Last Name</label>
		<input type="text" name="last_name" id="last_name" class="style_text_field" value="<?php echo (strlen($lastName) > 0?$lastName:"last name"); ?>" onfocus="clearField('last_name', 'last name');" /><br />
		<label for="telephone">Home Telephone Number</label>
		<input type="text" name="telephone" id="telephone" class="style_text_field" value="<?php echo (strlen($telephone) > 0?$telephone:"Home Phone"); ?>" onfocus="clearField('telephone', 'Home Phone');" /><br />
		<label for="mobile">Mobile Phone Number</label>
		<input type="text" name="mobile" id="mobile" class="style_text_field" value="<?php echo (strlen($mobile) > 0?$mobile:"Mobile Phone"); ?>" onfocus="clearField('mobile', 'Mobile Phone');" /><br />
		<label for="address">Address</label>
		<input type="text" name="address" id="address" class="style_text_field" value="<?php echo (strlen($address) > 0?$address:"Address"); ?>" onfocus="clearField('address', 'Address');" /><br />
		<label for="addressUnit">Apt/Unit Number</label>
		<input type="text" name="addressUnit" id="addressUnit" class="style_text_field" value="<?php echo (strlen($addressUnit) > 0?$addressUnit:"Apt/Unit #"); ?>" onfocus="clearField('addressUnit', 'Apt/Unit #');" /><br />
		<label for="city">City</label>
		<input type="text" name="city" id="city" class="style_text_field" value="<?php echo (strlen($city) > 0?$city:"City"); ?>" onfocus="clearField('city', 'City');" /><br />
		<label for="province">Province</label>
		<select name="province" class="style_select" style="width:200px;">
			<?php echo generateProvinceSelect($province); ?>
		</select>
		<div class="clear" style="height:0px;"></div>
		<label for="postcode">Postal Code</label>
		<input type="text" name="postcode" id="postcode" class="style_text_field" style="width:80px;" value="<?php echo (strlen($postcode) > 0?$postcode:"Postal Code"); ?>" onfocus="clearField('postcode', 'Postal Code');" /><br />
		<input type="hidden" name="isSubmitted" value="1" />
	</fieldset>
	<div style="width:422px;text-align:right;"><input type="submit" class="btn_blue" style="margin-right:0px;" value="Register" /></div>
</form>

<?php
include("footer.php");
?>