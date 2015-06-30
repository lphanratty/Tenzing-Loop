<?php
include_once("include/dblink.php");
$userGUID = isset($_REQUEST["UID"])?$_REQUEST["UID"]:"";
$fileType = isset($_REQUEST["fType"])?$_REQUEST["fType"]:"";
$filePath = isset($_REQUEST["fPath"])?$_REQUEST["fPath"]:"";
$linkID = isset($_REQUEST["LID"])?$_REQUEST["LID"]:"";

$retval = "";
//ob_clean();
$retval["GUID"] = $userGUID;
$retval["fileType"] = $fileType;
$retval["filePath"] = $filePath;
$retval["linkID"] = $linkID;
$retval["msg"] = "";
//Now we build the delete SQL
$sql = 
"DELETE FROM candidateFiles ".
"WHERE linkID = '".$linkID."' ";

$retval["SQL"] = $sql;

if($linkID > 0){
	if($link->query($sql)){
		if($link->affected_rows > 0){
			//Now we delete the file from the file system
			if(unlink($filePath)){
				//Now we get the updated list of files
				$sql = 
				"SELECT * FROM candidateFiles ".
				"WHERE fileType = '".$fileType."' ".
				"AND userGUID = '".$userGUID."' ".
				"ORDER BY dateCreated DESC ".
				"LIMIT ".(strcasecmp($fileType,"RESUME") == 0?"1":"2");
				if($result = $link->query($sql)){
					$retval["numCurrentFiles"] = $result->num_rows;
					if(strcasecmp($fileType,"RESUME") == 0){
						if($result->num_rows > 0){
							while($row = $result->fetch_assoc()){
								$retval["msg"] =
								"	<a href=\"".$row["filePath"]."\">Current Resume</a> Last Updated on ".date("M jS, Y",strtotime($row["dateCreated"])).
								"&nbsp;&nbsp; <INPUT TYPE=\"BUTTON\" value=\"Delete Resume\" onclick=\"deleteFile('RESUME','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n";
							}
						}else{
							$retval["msg"] = "No Resume currently saved";
						}	
					}else{ //Should be a HEADSHOT or BODYSHOT
						if($result->num_rows > 0){
							$retval["msg"] =
							"<table width=\"800\" border=\"0\">\r\n".
							"	<tr>\r\n".
							"		<td".($result->num_rows > 1?" colspan=\"".$result->num_rows."\"":"")." style=\"font-weight:bold;\">".(strcasecmp($fileType,"HEADSHOT") == 0?"Head Shots":"Body Shots")."</td>\r\n".
							"	</tr>\r\n".
							"	<tr>\r\n";
							while($row=$result->fetch_assoc()){
								$retval["msg"] .= 
								"		<td".($result->num_rows > 1?" width=\"".floor(100/$result->num_rows)."%\"":"")." align=\"center\">\r\n".
								"			<img src=\"".$row["filePath"]."?" . time() . "\" style=\"width:200px;\" /><br />\r\n".
								(strlen($row["fileCreated"]) > 0?"		Photo Taken ".date("M jS, Y",strtotime($row["fileCreated"]))."<br />\r\n":"		Photo Uploaded ".date("M jS, Y",strtotime($row["dateCreated"]))."<br />\r\n").
								"<input type=\"button\" class=\"btn_blue\" value=\"Delete Picture\" onclick=\"deleteFile('".$fileType."','".$row["linkID"]."','".$row["filePath"]."','".$userGUID."');return false;\" /><br />\r\n".
								"		</td>\r\n";
							}
							$retval["msg"] .=
							"	</tr>\r\n".
							"</table>\r\n";
						}else{
							$retval["msg"] = 
							"No ".(strcasecmp($fileType,"HEADSHOT") == 0?"Head Shots":"Body Shots")." uploaded<br />";
						}
					}
				}
			}
		}
	}
	
}


echo json_encode($retval); 
?>