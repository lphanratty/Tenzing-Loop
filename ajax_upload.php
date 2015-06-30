<?php
include_once("include/dblink.php");
ob_clean();
$GUID = isset($_POST["GUID"])?$_POST["GUID"]:"";
if(isset($_POST["HEADSHOTMONTH"]) && is_numeric($_POST["HEADSHOTMONTH"]) && 
	isset($_POST["HEADSHOTYEAR"]) && is_numeric($_POST["HEADSHOTYEAR"])
){
  //$fileDate = $_POST["HEADSHOTYEAR"]."-".$_POST["HEADSHOTMONTH"]."-".$_POST["HEADSHOTDAY"];
  $fileDate = $_POST["HEADSHOTYEAR"]."-".$_POST["HEADSHOTMONTH"]."-01";
}elseif(isset($_POST["BODYSHOTMONTH"]) && is_numeric($_POST["BODYSHOTMONTH"]) && 
	isset($_POST["BODYSHOTYEAR"]) && is_numeric($_POST["BODYSHOTYEAR"])
){
   $fileDate = $_POST["BODYSHOTYEAR"]."-".$_POST["BODYSHOTMONTH"]."-01";
}else{
	$fileDate = "";
}

$currentFiles = 0;


if(isset($_FILES["myresume"]))
{
	$output_dir = "candidate_files/";
	$fileType = "RESUME";
}elseif(isset($_FILES["myheadshot"])){
	$output_dir = "candidate_files/";
	$fileType = "HEADSHOT";
}elseif(isset($_FILES["mybodyshot"])){
	$output_dir = "candidate_files/";
	$fileType = "BODYSHOT";
}else{
	$output_dir = "candidate_files/";
	$fileType = "FILE";
}

/*
$sql = 
"SELECT COUNT(linkID) as numFiles ".
"FROM candidateFiles ".
"WHERE userGUID = '".$GUID."' ".
"AND fileType = '".$fileType."'";
*/
$sql = 
"SELECT fileType, filePath ".
"FROM candidateFiles ".
"WHERE userGUID = '".$GUID."' ".
"AND fileType = '".$fileType."' ".
"ORDER BY filePath";

if($result = $link->query($sql)){
	if($result->num_rows > 0){
		$currentFiles = $result->num_rows;
		$fileNum = 0;
		$currentNum = 0;
		while($row = $result->fetch_assoc()){
			$lastUnder = strripos($row["filePath"],"_") + 1;
			$lastPeriod = strripos($row["filePath"],".");
			$currentNum = substr($row["filePath"],$lastUnder,($lastPeriod - $lastUnder));
			if($fileNum + 1 < $currentNum){
				$fileNum = $fileNum + 1;
				break;
			}
		}
		if($fileNum == 0){
			$fileNum = $currentNum + 1;
		}
	}else{
		$fileNum = 1;
	}
}
	
$ret = uploadFiles($fileType,$output_dir,$GUID,$fileDate,$fileNum);
$sql = 
"SELECT * ".
"FROM candidateFiles ".
"WHERE userGUID = '".$GUID."' ".
"AND fileType = '".$fileType."'";
if($result = $link->query($sql)){
	$ret["fileList"] = "";
	if($result->num_rows > 0){
		if(strcasecmp($fileType,"HEADSHOT") == 0){
			$ret["fileList"] .=
			"<table width=\"800\" border=\"0\">\r\n".
			"	<tr>\r\n".
			"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Head Shots</td>\r\n".
			"	</tr>\r\n".
			"	<tr>\r\n";
		}
		if(strcasecmp($fileType,"BODYSHOT") == 0){
			$ret["fileList"] .=
			"<div id=\"bodyshots\">\r\n".
			"<table width=\"800\" border=\"0\">\r\n".
			"	<tr>\r\n".
			"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">Body Shots</td>\r\n".
			"	</tr>\r\n".
			"	<tr>\r\n";
		}
		while($row = $result->fetch_assoc()){
			if(strcasecmp($fileType,"RESUME") == 0){
				$ret["fileList"] .= 
				"<a href='".$row["filePath"]."'>Current Resume</a> Last Updated on ".date("M jS, Y",strtotime($row["dateCreated"])).
				"&nbsp;&nbsp; <INPUT TYPE='BUTTON' class='btn_blue' value='Delete Resume' onclick=\"deleteFile('RESUME','".$row["linkID"]."','".$row["filePath"]."','".$GUID."');return false;\" /><br />\r\n";
			}elseif(strcasecmp($fileType,"HEADSHOT") == 0){
				$ret["fileList"] .= 
				"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
				"			<img src=\"".$row["filePath"]."?" . time() . "\" style=\"width:200px;\" /><br />\r\n".
				(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M, Y",strtotime($row["dateCreated"]))."<br />\r\n").
				"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('HEADSHOT','".$row["linkID"]."','".$row["filePath"]."','".$GUID."');return false;\" /><br />\r\n".
				"		</td>\r\n";
			}
			elseif(strcasecmp($fileType,"BODYSHOT") == 0){
				$ret["fileList"] .= 
				"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
				"			<img src=\"".$row["filePath"]."?" . time() . "\" style=\"width:200px;\" /><br />\r\n".
				(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M, Y",strtotime($row["dateCreated"]))."<br />\r\n").
				"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('BODYSHOT','".$row["linkID"]."','".$row["filePath"]."','".$GUID."');return false;\" /><br />\r\n".
				"		</td>\r\n";
			}
		}
		if(strcasecmp($fileType,"HEADSHOT") == 0){
			$ret["fileList"] .=
			"	</tr>\r\n".
			"</table>\r\n".
			"</div>\r\n";
		}
		if(strcasecmp($fileType,"BODYSHOT") == 0){
			$ret["fileList"] .=
			"	</tr>\r\n".
			"</table>\r\n".
			"</div>\r\n";
		}
	}
}

echo json_encode($ret);
 
 
function uploadFiles($fileType,$output_dir,$GUID,$fileDate = "",$fileNum){
	global $link, $_FILES;
	$retval = array();
	$elementID = "";

	//	This is for custom errors;	
	/*	$custom_error= array();
	$custom_error['jquery-upload-file-error']="File already exists";
	echo json_encode($custom_error);
	die();
	*/	if(strcasecmp($fileType,"HEADSHOT") == 0){
		$elementID = "myheadshot";
		
	}elseif(strcasecmp($fileType,"RESUME") == 0){
		$elementID = "myresume";
	}elseif(strcasecmp($fileType,"BODYSHOT") == 0){
		$elementID = "mybodyshot";
	}else{
		$elementID = "myfile";
	}
	$error =$_FILES[$elementID]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData() 
	//$retval[] = var_dump($_FILES);
	$retval["msg"] = "File Date is ".$fileDate;
	if(strlen($fileDate) > 0){
		$tmpdate = date_parse($fileDate);
		//if ($tmpdate["errors"] == 0 && checkdate($tmpdate["month"], $tmpdate["day"], $tmpdate["year"])){
		if(!strtotime($fileDate)){
			//All is good - too lazy to figure out the NOT syntax
			$fileDate = "";
		}else{
			
			//$retval[] = "Month - ".$tmpdate["month"]." Day - ".$tmpdate["day"]."Year - ". $tmpdate["year"];
			//$fileDate = "";
		}

	}
	
	
	if(!is_array($_FILES[$elementID]["name"])) //single file
	{
		//$retval[] = "Element Name is ".$elementID;
		$fileName = $_FILES[$elementID]["name"];
		$ext = ".".strtolower(end(explode(".", $fileName)));
		//$fileName = $GUID."_".strtolower($fileType)."_".($currentFiles + 1).$ext;
		$fileName = $GUID."_".strtolower($fileType)."_".$fileNum.$ext;
		move_uploaded_file($_FILES[$elementID]["tmp_name"],$output_dir.$fileName);
		$retval["msg"] .= $fileName;
		$sql = 
		"INSERT INTO candidateFiles ".
		"SET userGUID = '".$GUID."', ".
		"fileType = '".$fileType."', ".
		"filePath = '".$link->real_escape_string($output_dir.$fileName)."', ".
		(strlen($fileDate) > 0?"fileCreated = '".$fileDate."', ":"").
		"dateCreated = NOW()";  //Remove hardcoding for extension and add extension
		
		if($link->query($sql)) {
			if($link->affected_rows > 0){
				//Check for Number of files currently uploaded
				$sql = 
				"SELECT * FROM candidateFiles ".
				"WHERE fileType = '".$fileType."' ".
				"AND userGUID = '".$GUID."' ".
				"ORDER BY dateCreated DESC ";
				//"LIMIT ".(strcasecmp($fileType,"RESUME") == 0?"1":"3");
				$retval["SQL"] = $sql;
				if($result = $link->query($sql)){
					$retval["currentFiles"] = $result->num_rows;
				}else{
					$retval["currentFiles"] = 0;
				}
			}
		}else{
			$retval["msg"] = "Error ".$sql;
		}
	}
	else  //Multiple files, file[]
	{
	  $fileCount = count($_FILES[$elementID]["name"]);
	  for($i=0; $i < $fileCount; $i++)
	  {
		$fileName = $_FILES[$elementID]["name"];
		$ext = ".".strtolower(end(explode(".", $fileName)));
		//$fileName = $GUID."_".strtolower($fileType)."_".($currentFiles + $i).$ext;
		$fileName = $GUID."_".strtolower($fileType)."_".$fileNum.$ext;
		move_uploaded_file($_FILES[$elementID]["tmp_name"][$i],$output_dir.$fileName);
		$retval["msg"]= $fileName;
	  }

	}

	return $retval;
 }
 ?>