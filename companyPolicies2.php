<?php
include_once("init.php");
include_once("classes/et_person.php");
$GUID = isset($_REQUEST["GUID"])?$_REQUEST["GUID"]:$_SESSION["et_userGUID"];

include("header.php");
?>
<div class="content_container">
	<?php echo "<legend>Section Two: Company Polices</legend>".
			   "<p>As a member of our Roster working on various programs, everything you do infield is a direct reflection on the brand within which you are representing. The utmost professionalism is expected at all times.</p>".
			   "<p>Here are our general policies and procedures:</p>".
			   "<ul><li><strong>Lateness</strong> is not acceptable when attending shift or training.</li>".
			   "<li><strong>Abandoning shift</strong> is not acceptable. If you are unable to work a booked shift, you must notify your Account Coordinator/Manager prior to 24 hours of shift.</li>".
			   "<li><strong>Illness or Emergencies</strong> beyond your control will be reviewed accordingly. Appropriate notice is required before a booked shift, and a doctor’s note is required within 24 hours of a cancelled shift.</li>".
			   "<li><strong>Drinking and/or Drug Use</strong> during shift or on-site following a shift is not tolerated. You are also never to attend shift hung-over or intoxicated. This will result in immediate removal from the shift.</li>".
			   "<li><strong>Smoking</strong> is only allowed during approved breaks, when out of uniform and away from consumers.</li>".
			   "<li><strong>Harassment or discrimination of any kind is not tolerated.</strong> If at any point you feel that you have been discriminated against or harassed, a confidential written complaint should be provided to your Account Coordinator/Manager for immediate follow up.</li>".
			   "<p>I have read and acknowledge the information outlined above (Check box)</p>";
?>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

