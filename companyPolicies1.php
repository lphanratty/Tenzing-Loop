<?php
include_once("init.php");
include_once("classes/et_person.php");
$GUID = isset($_REQUEST["GUID"])?$_REQUEST["GUID"]:$_SESSION["et_userGUID"];

include("header.php");
?>
<div class="content_container">
	<?php echo "<legend>Section One: Employment Status</legend>".
			   "<p>We understand the difference between being an Employee and being Self-Employed is sometimes confusing.".
			   "  Here is some information that should help you in understanding your role as a Self-Employed Worker.".
			   "<p>It is important to us that you are aware of your position and your rights before you begin working with our company.".
			   "  Here is some further information that you must be aware of prior to your first contract with our company:</p>".
			   "<ul><li>As a Self-Employed worker you provide a service to The Loop Agency under the terms specified in a contract or within a verbal agreement.</li>".
			   "<li>You do not work regularly for The Loop Agency; therefore you have control over your schedule and jobs accepted.</li>".
			   "<li>You are entitled to work for other agencies and companies, and are not restricted to only working for our company while remaining on our Roster.</li>".
			   "<li>You are not eligible for Employment Insurance (EI), therefore we do not provide Record of Employments.</li>".
			   "<li>You do not receive employee benefits such as, but not limited to, sick leave, time and a half, vacation pay or coverage.".
			   "  Jobs are accepted based on the hourly rate and hours outlined in the contract.</li>".
			   "<li>You will not have tax withheld at the source. Your pay check will reflect the actual hours worked, at the hourly rate agreed upon, in the contract. Taxes will not be deducted.</li>".
			   "<li>You keep all the funds that you collect until you have to pay your income tax to the Canada Revenue Agency.</li>".
			   "<li>We recommend that you keep track a document of the following, over the course of working for our company, to avoid issues with income taxes:".
			   " Program, Program Date and Time, Account Coordinator and/or Manager, Total Hours (including paid trainings), Hourly Rate, Compensated Expenses and Personal Expenses.</li>".
			   "<li>In order to provide you with the tools necessary to get a better understanding of what it means to be Self-Employed, please review the following link: <a href=\"http://www.cra-arc.gc.ca/E/pub/tg/rc4110/rc4110-13e.pdf\" target=\"_blank\">www.cra-arc.gc.ca/E/pub/tg/rc4110/rc4110-13e.pdf</a></li></ul>".
			   "<p>I have read and acknowledge the information outlined above (Check box)</p>";
?>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

