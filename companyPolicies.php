<?php
include_once("init.php");
include_once("classes/et_person.php");
$GUID = isset($_REQUEST["GUID"])?$_REQUEST["GUID"]:$_SESSION["et_userGUID"];
$ack_emp_status = 0;
$ack_comp_policies = 0;
if(strlen($GUID) > 0){
	$sql = 
	"SELECT acknowledge_employment_status, acknowledge_company_policies ".
	"FROM users ".
	"WHERE userGUID = '".$GUID."'";
	if($result = $link->query($sql)){
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$ack_emp_status = $row["acknowledge_employment_status"];
			$ack_comp_policies = $row["acknowledge_company_policies"];
		}
	}
}

$jscript = "js/policies.js|js/ajax_submit_field.js";
$pagescript = 
"	showDiv('employmentstatus');\r\n".
"	$(\"#policieslink\").addClass(\"currentpagelink\");\r\n".
"	$(\"#employmentstatuslink\").click(function(){\r\n".
"		showDiv('employmentstatus');\r\n".
"	});\r\n".
"	$(\"#companypolicieslink\").click(function(){\r\n".
"		showDiv('companypolicies');\r\n".
"	});\r\n".
"	$('#employment_status_acknowledgement').click (function (){\r\n".
"		if($(this).is (':checked')){\r\n".
"			submitValue('users','acknowledge_employment_status',1,'".$GUID."');\r\n".
"			$(this).hide();\r\n".
"			if(!$('#company_policies_acknowledgement').is(':checked')){\r\n".
"				showDiv('companypolicies');\r\n".
"			}\r\n".
"		}else{\r\n".
"			submitValue('users','acknowledge_employment_status',0,'".$GUID."');\r\n".
"		}\r\n".
"	});\r\n".
"	$('#company_policies_acknowledgement').click (function (){\r\n".
"		if($(this).is (':checked')){\r\n".
"			submitValue('users','acknowledge_company_policies',1,'".$GUID."');\r\n".
"			$(this).hide();\r\n".
"			if(!$('#employment_status_acknowledgement').is(':checked')){\r\n".
"				showDiv('employmentstatus');\r\n".
"			}\r\n".
"		}else{\r\n".
"			submitValue('users','acknowledge_company_policies',0,'".$GUID."');\r\n".
"		}\r\n".
"	});\r\n";

include("header.php");
?>
<div id="page_menu">
	<a id="employmentstatuslink" class="page_menu_link">Employment Status</a>
	<a id="companypolicieslink" class="page_menu_link">Company Policies</a>
</div>
<div class="clear"></div>
<form name="policies" id="policies" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<div id="employmentstatus" class="policydiv">
	<fieldset>
	<legend>Section One: Employment Status</legend>
	<p>
		We understand the difference between being an Employee and being Self-Employed is sometimes confusing.
		Here is some information that should help you in understanding your role as a Self-Employed Worker.
	</p>
	<p>
		It is important to us that you are aware of your position and your rights before you begin working with our company.
		Here is some further information that you must be aware of prior to your first contract with our company:
	</p>
	<ul>
		<li>As a Self-Employed worker you provide a service to The Loop Agency under the terms specified in a contract or within a verbal agreement.</li>
		<li>You do not work regularly for The Loop Agency; therefore you have control over your schedule and jobs accepted.</li>
		<li>You are entitled to work for other agencies and companies, and are not restricted to only working for our company while remaining on our Roster.</li>
		<li>You are not eligible for Employment Insurance (EI), therefore we do not provide Record of Employments.</li>
		<li>You do not receive employee benefits such as, but not limited to, sick leave, time and a half, vacation pay or coverage.
		<li>Jobs are accepted based on the hourly rate and hours outlined in the contract.</li>
		<li>You will not have tax withheld at the source. Your pay check will reflect the actual hours worked, at the hourly rate agreed upon, in the contract. Taxes will not be deducted.</li>
		<li>You keep all the funds that you collect until you have to pay your income tax to the Canada Revenue Agency.</li>
		<li>We recommend that you keep track of the following over the course of working for our company, to avoid issues with income taxes:<br />
			Program, Program Date and Time, Account Coordinator and/or Manager, Total Hours (including paid trainings), Hourly Rate, Compensated Expenses and Personal Expenses.
		</li>
		<li>In order to provide you with the tools necessary to get a better understanding of what it means to be Self-Employed, please review the following link: 
			<a href="http://www.cra-arc.gc.ca/E/pub/tg/rc4110/rc4110-13e.pdf" target="_blank">www.cra-arc.gc.ca/E/pub/tg/rc4110/rc4110-13e.pdf</a>
		</li>
	</ul>
	<p>
		<?php
			if($ack_emp_status == 0){
				echo
				"<input type=\"checkbox\" value=\"1\" name=\"employment_status_acknowledgement\" id=\"employment_status_acknowledgement\"".($ack_emp_status == 1?" checked ":"")." /> I have read and acknowledge the information outlined above\r\n";
			}else{
				echo
				"I have read and acknowledged the information outlined above\r\n";
			}
		?>
	</p>
	</fieldset>
</div>
<div id="companypolicies" class="policydiv">
	<fieldset>
	<legend>Section Two: Company Polices</legend>
	<p>
		As a member of our Roster working on various programs, everything you do infield is a direct reflection on the brand 
		within which you are representing. The utmost professionalism is expected at all times.
	</p>
	<p>
		Here are our general policies and procedures:
	</p>
	<ul>
		<li><strong>Lateness</strong> is not acceptable when attending shift or training.</li>
		<li><strong>Abandoning shift</strong> is not acceptable. If you are unable to work a booked shift, you must notify your Account 
			Coordinator/Manager prior to 24 hours of shift.
		</li>
		<li><strong>Illness or Emergencies</strong> beyond your control will be reviewed accordingly. Appropriate notice is required before 
			a booked shift, and a doctor's note is required within 24 hours of a cancelled shift.
		</li>
		<li><strong>Drinking and/or Drug Use</strong> during shift or on-site following a shift is not tolerated. You are also never 
			to attend shift hung-over or intoxicated. This will result in immediate removal from the shift.
		</li>
		<li><strong>Smoking</strong> is only allowed during approved breaks, when out of uniform and away from consumers.</li>
		<li><strong>Harassment or discrimination of any kind is not tolerated.</strong> If at any point you feel that you have 
			been discriminated against or harassed, a confidential written complaint should be provided to your Account Coordinator/Manager 
			for immediate follow up.
		</li>
	</ul>
		<p>
			<?php
				if($ack_comp_policies == 0){
					echo
					"<input type=\"checkbox\" value=\"1\" name=\"company_policies_acknowledgement\" id=\"company_policies_acknowledgement\"".($ack_comp_policies == 1?" checked ":"")." /> I have read and acknowledge the information outlined above\r\n";
				}else{
					echo
					"I have read and acknowledged the information outlined above\r\n";
				}
			?>
		</p>
	</fieldset>
</div>
	<input type="hidden" name="isSubmitted" value="1" />
</form>
<div class="clear"></div>
<?php
include("footer.php");
?>

