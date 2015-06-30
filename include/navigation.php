<?php
$menu_content = "";
if(isset($etUser) && strlen($etUser->userGUID) > 0){
	if($etUser->typeID == 1){
		$menu_content .=
		"	<div id=\"top_menu\">\r\n".
		"		<a href=\"candidateSearch.php\" class=\"top_menu_link\" id=\"searchlink\">CANDIDATE SEARCH</a> / ".
		//"		<a href=\"promotions.php\" class=\"top_menu_link\">PROMOTIONS</a> / ".
		"		<a href=\"postings.php\" class=\"top_menu_link\" id=\"postingslink\">JOB POSTINGS</a> / ".
		"	</div>\r\n".
		"	<div class=\"clear\"></div>\r\n";
	}elseif($etUser->typeID == 4 && !$etUser->profileIncomplete()){
		$menu_content .=
		"	<div id=\"top_menu\">\r\n".
		"		<a href=\"myprofile.php\" class=\"top_menu_link\" id=\"myprofilelink\">MY INFO</a> / ".
		"		<a href=\"companyPolicies.php\" class=\"top_menu_link\" id=\"policieslink\">COMPANY POLICIES</a> / ".
		"		<a href=\"postings.php\" class=\"top_menu_link\" id=\"postingslink\">JOB POSTINGS</a> / ".
		"		<a href=\"faq.php\" class=\"top_menu_link\" id=\"faqlink\">FAQ&#39;S</a>".
		"	</div>\r\n".
		"	<div class=\"clear\"></div>\r\n";
	}
}

?>