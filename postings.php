<?php
include_once("init.php");
include_once("include/inc_form_functions.php");
include_once("classes/et_person.php");
include_once("classes/et_job_posting.php");
include_once("classes/email_message.php");
include_once("classes/et_emails.php");
include_once("include/posting_application.php");

$pageMsg = "";
//$posting = new etJobPosting($postingID);
$postingID = isset($_GET["PID"])?$_GET["PID"]:0;
$action = isset($_GET["ACT"])?$_GET["ACT"]:"";
if($postingID > 0 && strcasecmp($action,"APPLY") == 0){
	$pageMsg = submitApplication($postingID);
}
$content = getPostingsList($etUser->typeID,$pageMsg);
$jscript = "js/postings.js";
$pagescript = 
"	showDiv('postings_active');\r\n".
"	$(\"#postingslink\").addClass(\"currentpagelink\");\r\n".
"	$(\"#activelink\").click(function(){\r\n".
"		showDiv('postings_active');\r\n".
"	});\r\n".
"	$(\"#inactivelink\").click(function(){\r\n".
"		showDiv('postings_inactive');\r\n".
"	});\r\n";
include("header.php");
echo $content; 
include("footer.php");

function getPostingsList($typeID, $pageMsg){
	global $link,$etUser;
	$retval = "";
	if($typeID < 4){
		$retval .=
		"<div id=\"page_menu\">\r\n".
		"	<a id=\"activelink\" class=\"page_menu_link\">Active Postings</a>\r\n".
		"	<a id=\"inactivelink\" class=\"page_menu_link\">Inactive Postings</a>\r\n".
		"</div>\r\n".
		"<div class=\"clear\"></div>\r\n";
		$sql = 
		"SELECT * FROM jobPostings ".
		"WHERE UPPER(postingStatus) = 'ACTIVE' ".
		"ORDER BY dateCreated DESC";
	}else{
		$sql = 
		"SELECT jp.*, ".
		"CASE WHEN ap.applicationID IS NULL THEN 'NO' ELSE 'YES' END AS hasApplied ".
		"FROM jobPostings jp ".
		"LEFT JOIN applications ap ON (jp.postingID = ap.postingID AND ap.userGUID = '".$etUser->userGUID."') ".
		"WHERE UPPER(postingStatus) = 'ACTIVE' ".
		"ORDER BY dateCreated DESC";
	}
	
	//echo $sql."<br />";
	
	if($result = $link->query($sql)){
		$retval .=
		"<div id=\"postings_active\" class=\"profilediv\">\r\n";
		if(strlen($pageMsg) > 0){
			$retval .=
			"	<div style=\"color:red;font-weight:bold;\">\r\n".
			"		".$pageMsg."\r\n".
			"</div>\r\n";
		}
		if($result->num_rows > 0){
			$retval .=
			"	<div style=\"width:400px;float:left;\"><h3>Current Job Postings</h3></div>\r\n".
			($typeID < 4?"<div style=\"float:left;width:200px;margin-top:10px;\"><a href=\"createJobPosting.php\" class=\"btn_blue\" style=\"float:right;margin-right:0px;\">Add Job Posting</a></div>\r\n":"").
			"	<div class=\"clear\"></div>\r\n".
			"	<table class=\"tbl_bordered\" style=\"width:90%\">\r\n".
			"		<tr>\r\n".
			"			<th>Program Name</th>\r\n".
			"			<th>Province</th>\r\n".
			"			<th>Cities</th>\r\n".
			"			<th>Program Timing</th>\r\n".
			"			<th>Roles Needed</th>\r\n".
			"			<th>".($typeID == 4?"Apply":"Edit")."</th>\r\n".
			"		</tr>\r\n";
			while($row = $result->fetch_assoc()){
				if($typeID == 1){
					$linkto = 
					"<a href=\"jobPosting.php?PID=".$row["postingID"]."\">DELETE/EDIT</a>";
				}else{
					if(strcasecmp($row["hasApplied"],"YES") == 0){
						$linkto = "&nbsp;";
					}else{
						$linkto = 
						//"<a href=\"mailto:".$row["adminEmail"]."?subject=Application for Job Posting - ".$row["programName"]."\">APPLY</a>";
						//"<a href=\"posting_application.php?PID=".$row["postingID"]."\">APPLY</a>";
						"<a href=\"".$_SERVER["PHP_SELF"]."?PID=".$row["postingID"]."&ACT=APPLY\">APPLY</a>";
					}
					
				}
				$retval .=
				"		<tr>\r\n".
				"			<td><a href=\"jobPosting.php?PID=".$row["postingID"]."\">".$row["programName"]."</a></td>\r\n".
				"			<td>".$row["province"]."</td>\r\n".
				"			<td>".$row["cities"]."</td>\r\n".
				"			<td>".$row["programTiming"]."</td>\r\n".
				"			<td>".$row["rolesRequired"]."</td>\r\n".
				"			<td>".$linkto."</td>\r\n".
				"		</tr>\r\n";
			}
			$retval .=
			"	</table>\r\n";
		}else{
			$retval .=
			"	<div style=\"width:400px;float:left;\"><h3>No Current Job Postings</h3></div>\r\n".
				($typeID < 4?"<div style=\"float:left;width:200px;margin-top:10px;\"><a href=\"createJobPosting.php\" class=\"btn_blue\" style=\"float:right;margin-right:0px;\">Add Job Posting</a></div>\r\n":"").
			"	<div class=\"clear\"></div>\r\n";
		}
		$retval .=
		"</div>\r\n";
	}
	
	//Now we add the INACTIVE postings for administrators IF the user is an administrator
	if($typeID < 4){
		$sql = 
		"SELECT * FROM jobPostings ".
		"WHERE UPPER(postingStatus) = 'INACTIVE' ".
		"ORDER BY dateCreated DESC";
		
		if($result = $link->query($sql)){
			$retval .=
			"<div id=\"postings_inactive\" class=\"profilediv\">\r\n";
			if($result->num_rows > 0){
				$retval .=
				"	<div style=\"width:400px;float:left;\"><h3>Inactive Job Postings</h3></div>\r\n".
				($typeID < 4?"<div style=\"float:left;width:200px;margin-top:10px;\"><a href=\"createJobPosting.php\" class=\"btn_blue\" style=\"float:right;margin-right:0px;\">Add Job Posting</a></div>\r\n":"").
				"	<div class=\"clear\"></div>\r\n".
				"	<table class=\"tbl_bordered\" style=\"width:90%\">\r\n".
				"		<tr>\r\n".
				"			<th>Program Name</th>\r\n".
				"			<th>Province</th>\r\n".
				"			<th>Cities</th>\r\n".
				"			<th>Program Timing</th>\r\n".
				"			<th>Roles Needed</th>\r\n".
				"			<th>".($typeID == 4?"Apply":"Edit")."</th>\r\n".
				"		</tr>\r\n";
				while($row = $result->fetch_assoc()){
					if($typeID == 1){
						$linkto = 
						"<a href=\"jobPosting.php?PID=".$row["postingID"]."\">DELETE/EDIT</a>";
					}else{
						$linkto = 
						"<a href=\"mailto:".$row["adminEmail"]."?subject=Application for Job Posting - ".$row["programName"]."\">APPLY</a>";
					}
					$retval .=
					"		<tr>\r\n".
					"			<td><a href=\"jobPosting.php?PID=".$row["postingID"]."\">".$row["programName"]."</a></td>\r\n".
					"			<td>".$row["province"]."</td>\r\n".
					"			<td>".$row["cities"]."</td>\r\n".
					"			<td>".$row["programTiming"]."</td>\r\n".
					"			<td>".$row["rolesRequired"]."</td>\r\n".
					"			<td>".$linkto."</td>\r\n".
					"		</tr>\r\n";
				}
				$retval .=
				"	</table>\r\n";
			}else{
				$retval .=
				"	<div style=\"width:400px;float:left;\"><h3>No Inactive Job Postings</h3></div>\r\n".
					($typeID < 4?"<div style=\"float:left;width:200px;margin-top:10px;\"><a href=\"createJobPosting.php\" class=\"btn_blue\" style=\"float:right;margin-right:0px;\">Add Job Posting</a></div>\r\n":"").
				"	<div class=\"clear\"></div>\r\n";
			}
			$retval .=
			"</div>\r\n";
		}
	}
	
	return $retval;
}
?>