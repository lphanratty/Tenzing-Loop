<?php
include_once("init.php");
include_once("classes/et_person.php");
$GUID = isset($_REQUEST["GUID"])?$_REQUEST["GUID"]:$_SESSION["et_userGUID"];
$pagescript = 
"	$(\"#myjobslink\").addClass(\"currentpagelink\");\r\n";

$content = "These are my jobs<br />";
include("header.php");
?>
<div style="width:700px;margin:30px auto;position:relative;">
	<?php echo $content; ?>
</div>
<div class="clear"></div>
<?php
include("footer.php");
?>

