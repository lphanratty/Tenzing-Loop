<?php
include_once("init.php");
require("email_message.php");
include_once("classes/et_emails.php");

$email = new etEmail("REGISTER_CONFIRM","","","");

$content = $email->emailDisplay();

echo $content;
?>