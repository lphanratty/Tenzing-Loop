<?php
include_once("init.php");
$GUID = "HGFDKLJHGF";
$pagescript = 
"	var settings = {\r\n".
"    url: \"ajax_upload.php\", ".
"    method: \"POST\",\r\n".
"	 dynamicFormData: function()\r\n".
"	 {\r\n".
"		var data ={ GUID:\"".$GUID."\"}\r\n".
"		return data;\r\n".
"	 },\r\n".
"    allowedTypes:\"jpg,png,gif,doc,pdf,zip\",\r\n".
"    fileName: \"myresume\", \r\n".
"    multiple: true,\r\n".
"    onSuccess:function(files,data,xhr) \r\n".
"    {\r\n".
"        $(\"#status\").html(\"<font color='green'>Upload is successful</font>\");\r\n".
"\r\n".
"    },\r\n".
"    onError: function(files,status,errMsg)\r\n".
"    { \r\n".
"       	$(\"#status\").html(\"<font color='red'>Upload has Failed</font>\");\r\n".
"    }\r\n".
"}\r\n".
"$(\"#resumeuploader\").uploadFile(settings);\r\n";
include("header.php");
include("include/resumeUpload.php");
include("footer.php");
?>