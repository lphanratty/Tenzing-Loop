<?php
include_once("init.php");
include_once("classes/et_person.php");
$GUID = isset($_REQUEST["GUID"])?$_REQUEST["GUID"]:$_SESSION["et_userGUID"];

include("header.php");
?>
<div class="content_container">
	<?php echo "<legend>Frequently Asked Questions:</legend>".
			   "<p><strong>I have made a profile, but what happens next?</strong><br />".
			   "Once you have completed a profile, you will have access on the site to all job postings within Canada. Please check your profile regularly to learn of upcoming programs in your neighbourhood and click on \"Apply\" if you are available and interested. If  your profile matches the perfect candidate for the role, the recruiter will contact you and provide further info with regards to next steps.</p>".
			   "<p><strong>What if I forget my login information?</strong><br />".
			   "If at any point you do not remember your password... go to the site and select \"Forgot Password?\" Your password will be emailed to you so that you can gain access to your account.</p>".
			   "<p><strong>Do I need to complete My Profile to join the Roster?</strong><br />".
			   "You can always revisit My Profile to add more information and make updates, however there is key info that we need. So please make sure that all fields have info to make sure you are able to access job postings.</p>".
			   "<p><strong>What should I do if I am having issues uploading my Resume or Picture on My Profile?</strong><br />".
			   "If you are having any technical difficulties with completing your profile, please reach out to recruiting@theloopagency.ca for further assistance.</p>".
			   "<p><strong>How many jobs am I allowed to apply for at a time?</strong><br />".
			   "If you are interested in applying to every job... we are pleased to talk with you about it. Review the job description, dates and locations to make sure you are 100% interested.</p>".
			   "<p><strong>What if I applied for a job but want to revoke my applications?</strong><br />".
			   "No problem. When a recruiter follows up with you, just let them know.</p>";
?>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

