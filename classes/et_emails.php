<?php

class etEmail{
	public $emailID;
	public $emailType;
	public $sqlCriteria;
	public $emailSubject;
	public $htmlBody;
	public $textBody;
	public $replaceFieldsArray;
	public $toArray;
	public $ccArray;
	public $bccArray;
	public $fromName;
	public $fromEmail;
	public $file_attachment;
	
	function __construct($emailType="",$toArray = "", $replaceFieldsArray = "", $sqlCriteria = ""){
		$this->emailType = $emailType;
		$this->toArray = $toArray;
		$this->replaceFieldsArray = $replaceFieldsArray;
		$this->sqlCriteria = $sqlCriteria;
		$this->ccArray = "";
		$this->bccArray = "";
		$this->fromName = "The Loop Agency";
		$this->fromEmail = "recruiting@theloopagency.ca";
		$this->file_attachment = "";
		if(strlen($this->emailType) > 0){
			$this->getEmailContent();
		}
	}
	
	function getEmailContent(){
		global $link;
		$sql = 
		"SELECT * FROM emailMessages ".
		"WHERE emailType = '".$this->emailType."'".
		(strlen($this->sqlCriteria) > 0?", ".$this->sqlCriteria:"").
		"LIMIT 1";
		if($result = $link->query($sql)){
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				$this->emailSubject = $row["emailSubject"];
				$this->htmlBody = 
				"<div style=\"width:80%;margin:0px auto;\">\r\n".
				"	<table style=\"width:100%;margin:0px;\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\r\n".
				"		<tr>\r\n".
				"			<td width=\"110\" align=\"left\" valign=\"top\">\r\n".
				"				<img src=\"http://www.theloopagency.ca/recruit/images/email_logo.png\" />\r\n".
				"			</td>\r\n".
				"			<td align=\"right\" valign=\"top\">\r\n".
				"				The Loop Agency<br />\r\n".
				"				Suite 100, 488 Wellington St W<br />\r\n".
				"				Toronto, ON M5V1E3\r\n".
				"			</td>\r\n".
				"		</tr>\r\n".
				"		<tr>\r\n".
				"			<td colspan=\"2\">\r\n".
				$this->htmlBody .= $row["emailBody"]."\r\n".
				"			</td>\r\n".
				"		</tr>\r\n".
				"		<tr>\r\n".
				"			<td colspan=\"2\" style=\"font-size:10px;\">\r\n".
				"				<em>Confidentiality Notice: the information contained in this email and any attachments may be legally privileged and confidential. ".
				"				If you are not an intended recipient, you are hereby notified that any dissemination, distribution, or copying of this e-mail is ".
				"				strictly prohibited. If you have received this e-mail in error, please notify the sender and permanently delete the e-mail and any ".
				"				attachments immediately. You should not retain, copy or use this e-mail or any attachments for any purpose, nor disclose all or any ".
				"				part of the contents to any other person.\r\n".
				"			</td>\r\n".
				"		</tr>\r\n".
				"	</table>\r\n".
				"</div>\r\n";
				
			}
		}
		$counter = 0;
		if(is_array($this->replaceFieldsArray)){
			foreach($this->replaceFieldsArray as $find => $replace){
				$findit[$counter] = $find;
				$replaceit[$counter] = $replace;
				$counter++;
			}
			$this->htmlBody = str_replace($findit, $replaceit,$this->htmlBody);
			$this->emailSubject = str_replace($findit, $replaceit,$this->emailSubject);
		}
	}
	
	function emailDisplay(){
		$retval = "";
		$retval .=
		"<strong>From: ".$this->fromName."- ".$this->fromEmail."</strong><br />\r\n";
		foreach($this->toArray as $to_address=>$to_name){
			$retval .=
			"<strong>To: ".$to_name." - ".$to_address."<br />"."</strong><br />\r\n";
		}
		$retval .=
		"<strong>Subject: ".$this->emailSubject."</strong><br />\r\n".
		"<strong>Email Body:</strong><br />\r\n".
		$this->htmlBody."<br />\r\n";
		return $retval;
	}
	
	
	function sendEmails(){	
		
		if(is_array($this->toArray)){
			$email_message=new email_message_class;   //Instantiate our message class
			$email_message->SetEncodedEmailHeader("From",$this->fromEmail,$this->fromName);
			$email_message->SetEncodedEmailHeader("Reply-To",$this->fromEmail,$this->fromName);
			$email_message->SetHeader("Sender",$this->fromEmail);
			
			if(is_array($this->ccArray)){
				$email_message->SetMultipleEncodedEmailHeader('cc', $this->ccArray);	
			}else{
				/*
				$email_message->SetMultipleEncodedEmailHeader('cc', array(
				'liam@datascape.ca' => 'Liam Hanratty',
				'gkeaney@tenzing-im.com' => 'Greg Keaney',
				'tenzingtest1@hotmail.com' => 'Greg Keaney',
				'michelle@sugarmedia.ca' => 'Michelle Diomede',
				'donnelly@sugarmedia.ca' => 'Donnelly Baxter',
				'john@sugarmedia.ca' => 'John Lacey'
				));
				*/
			}
			if(is_array($this->bccArray)){
				$email_message->SetMultipleEncodedEmailHeader('Bcc', $this->bccArray);	
			}else{
				
				$email_message->SetMultipleEncodedEmailHeader('Bcc', array(
				//'liam@datascape.ca' => 'Liam Hanratty'
				'bcc@tenzing-im.com' => 'Tenzing',
				//'tenzingtest1@hotmail.com' => 'Greg Keaney'
				//'michelle@sugarmedia.ca' => 'Michelle Diomede',
				//'donnelly@sugarmedia.ca' => 'Donnelly Baxter',
				//'john@sugarmedia.ca' => 'John Lacey'
				));
				
				
			}
			
			$email_message->SetEncodedHeader("Subject",$this->emailSubject);
			$email_message->CreateQuotedPrintableHTMLPart($this->htmlBody,"",$html_part);
			$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($this->textBody),"",$text_part);
			
			$alternative_parts=array(
				$text_part,
				$html_part
			);
			
			$email_message->CreateAlternativeMultipart($alternative_parts,$alternative_part);
		
			$related_parts=array(
					$alternative_part
			);
			
			$email_message->AddRelatedMultipart($related_parts);
		
		
		
			if(is_array($this->file_attachment)){
				$email_message->AddFilePart($file_attachment);
			}
			
			foreach($this->toArray as $to_address=>$to_name){
				$email_message->SetEncodedEmailHeader("To",$to_address,$to_name);
				
				$error = $email_message->Send();
				if(strlen($error) > 0){
					echo "Error: $error\n";
					//var_dump($email_message->parts);
					$this->emailDisplay();
					return false;
				}else{
					//echo "Message sent to ".$to_name." at ".$to_address."<br />\n";
					return true;
				}
			}		
		}
	}
}

?>