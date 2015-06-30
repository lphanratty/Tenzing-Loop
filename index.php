<?php
include_once("init.php");
include_once("classes/et_person.php");
$activationCode = (isset($_REQUEST["AC"])?$_REQUEST["AC"]:"");
$goToPage = (isset($_REQUEST["GOTO"])&& strlen($_REQUEST["GOTO"]) > 0?str_replace("~","&",$_REQUEST["GOTO"]):"");
include("header.php");
?>
<div style="width:700px;margin:30px auto;position:relative;">
	<div class="rounded-box" style="float:left;width:300px">
		<form name="login_form" id="login_form" action="login.php" method="post">
			<span class="header-style">Been here before?</span>
			<p>
				If you've already created a personal login, then please enter your information below to continue.
			</p>
			<p>
				<input type="text" name="et_email" id="et_email" class="style_text_field" value="email/username" onfocus="clearField('et_email', 'email/username');" /><br />
				<input type="password" name="et_pword" id="et_pword" class="style_text_field" value="" /><br />
			</p>
			<p style="float:left;width:49%;">
				<a href="forgotpw.php">Forgot Password?</a>
			</p>
			<p style="float:right;width:49%;">
				<input type="hidden" name="loginSubmitted" value="1" />
				<input type="hidden" name="GOTO" value="<?php echo $goToPage; ?>" />
				<input type="submit" class="btn_blue" value="Login" />
			</p>
		</form>
	</div>
	<div class="rounded-box" style="float:right;width:300px">
		<form name="login_form" id="login_form" action="register.php" method="post">
			<span class="header-style">New to our site?</span>
			<p>
				Please enter your first and last name, then click the button below to continue.
			</p>
			<p>
				<input type="text" name="first_name" id="first_name" class="style_text_field" value="first name" onfocus="clearField('first_name', 'first name');" /><br />
				<input type="text" name="last_name" id="last_name" class="style_text_field" value="last name" onfocus="clearField('last_name', 'last name');"  /><br />
			</p>
			<p style="float:right;width:49%;text-align:right;">
				<input type="submit" class="btn_grey" value="Next >" />
			</p>
		</form>
	</div>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

