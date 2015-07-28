<?php
class SiteEmail extends Base
{	var $id = 0;
	var $details = array();
	var $sentlist = array();
	var $sentCount = 0;
	var $alreadySent = array();
	var $sendfrom_default = "info@alkauthar.org";
	var $header = "<p>As-salamu 'alaikum ~name~</p>\n";
	var $footer = "\n\n<p>AlKauthar Support Team<br />\nEmail: <a href='mailto:~sendfrom~'>~sendfrom~</a><br />\nWeb: <a href='http://www.alkauthar.org'>www.alkauthar.org</a></p>\n";
	
	function __construct($emid = 0)
	{	parent::__construct();
		$this->Get($emid);
	} // fn __construct

	function Reset()
	{	$this->details = array();
		$this->sentlist = array();
		$this->id = 0;
	} // end of fn Reset

	function Get($emid = 0)
	{	$this->Reset();
		if (is_array($emid))
		{	$this->details = $emid;
			$this->id = $emid["emid"];
			
		//	if ($result = $this->db->Query("SELECT * FROM siteemailssent WHERE emid=" . (int)$this->id))
		//	{	while ($row = $this->db->FetchArray($result))
		//		{	$this->sentlist[] = $row;
		//		}
		//	}
			
			$this->sentCount = 0;
			if ($result = $this->db->Query("SELECT COUNT(email) AS sesSent FROM siteemailssent WHERE emid=" . (int)$this->id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->sentCount = (int)$row["sesSent"];
				}
			}
			
		} else
		{	if ($result = $this->db->Query("SELECT * FROM siteemails WHERE emid=" . (int)$emid))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
		return false;

	} // end of fn GetDetails
	
	function SentToEmail($email = "")
	{	$sql = "SELECT * FROM siteemailssent WHERE email='" . $this->SQLSafe($email) . "' AND emid=" . (int)$this->id;
		if ($result = $this->db->Query($sql))
		{	if ($this->db->NumRows($result))
			{	$this->alreadySent[] = $row;
				return true;
			}
		}
		
	} // end of fn SentToEmail
	
	function CanDelete()
	{	return $this->id && !count($this->sentlist) && !$this->sentCount;
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query("DELETE FROM siteemails WHERE emid=" . $this->id))
			{	return $this->db->AffectedRows();
			}
		
		}
	} // end of fn Delete

	function DeleteLink()
	{	echo "<p><a href='siteemail.php?id=", $this->id, "&del=1", $_GET["del"] ? "&confirm=1" : "", "'>", $_GET["del"] ? "please confirm you want to " : "", "delete this email</a></p>\n";
	} // end of fn DeleteLink
	
	function Save($data = array())
	{	
		$fields = array();
		$fail = array();
		$success = array();
		
		$mailbody = $this->SQLSafe($data["mailbody"]);
		$fields[] = "mailbody='$mailbody'";
		
		if ($emaildesc = $this->SQLSafe($data["emaildesc"]))
		{	$fields[] = "emaildesc='$emaildesc'";
		} else
		{	$fail[] = "you must have a description";
		}
		
		if ($data["sendfrom"])
		{	if ($this->ValidEmail($data["sendfrom"]))
			{	$fields[] = "sendfrom='{$data["sendfrom"]}'";
			} else
			{	$fail[] = "the email address to send from is invalid";
			}
		} else
		{	$fields[] = "sendfrom=''";
		}
		
		$subject = $this->SQLSafe($data["subject"]);
		$fields[] = "subject='$subject'";
		
		if (!$this->id)
		{	$fields[] = "createtime='" . $this->datefn->SQLDateTime() . "'";
		}
		
		if ($this->id || !$fail)
		{	if ($set = implode(", ", $fields))
			{	if ($this->id)
				{	$sql = "UPDATE siteemails SET $set WHERE emid='{$this->id}'";
				} else
				{	$sql = "INSERT INTO siteemails SET $set";
				}

				if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	if ($this->id)
						{	$success[] = "changes saved";
						} else
						{	if ($this->id = $this->db->InsertID())
							{	$success[] = "changes saved";
							}
						}
						$this->Get($this->id);
						// check for any formatting in saved text
						if ($this->WordTextFound($this->details["mailbody"]))
						{	$fail[] = "WARNING: MS Word formatting found in text - this will affect the overall style of the email. Please copy and paste from notepad to avoid this in the future.";
						}
					}
				}else echo "<p>", $this->db->Error(), "</p>\n";
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function WordTextFound($str = "")
	{	if (strstr($str, 'MsoNormal'))
		{	return true;
		}
		if (strstr($str, 'MsoListParagraph'))
		{	return true;
		}
		return false;
	} // end of fn WordTextFound
	
	function InputForm()
	{	
		if ($this->id)
		{	$data = $this->details;
		} else
		{	$data = $_POST;
		}
	
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id, "seform");
		if ($this->CanDelete())
		{	$this->DeleteLink();
		}
		$form->AddTextInput("Description (for admin)", "emaildesc", $this->InputSafeString($data["emaildesc"]), "", 255, 0);
		$form->AddTextInput("Subject", "subject", $this->InputSafeString($data["subject"]), "", 255, 0);
		//$form->AddTextInput("Send from (email address)", "sendfrom", $this->InputSafeString($data["sendfrom"]), "", 255, 0);
		$form->AddMultiInput("Send from (email address)", array(
						array("type"=>"TEXT", "name"=>"sendfrom", "value"=>$this->InputSafeString($data["sendfrom"]), "maxlength"=>255, "css"=>"long"),
						array("type"=>"RAW", "text"=>"<div class='inlinespan'><a id='emsTrigger'>select email</a> - if blank will be <span style='font-weight: bold;'>{$this->sendfrom_default}</span></div>")));
	//	$form->AddRawText("<p><label></label><iframe width='400px' height='100px' src='siteemail_display.php?id={$this->id}&disp=header'></iframe></p>\n");
		$form->AddTextArea("Email body", "mailbody", stripslashes($data["mailbody"]), "wysiwyg", 0, 0, 30, 60);
		$form->AddRawText("<p><label></label><iframe width='400px' height='100px' src='siteemail_display.php?id={$this->id}&disp=footer'></iframe></p>\n");
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create Email", "submit");
		echo $this->SelectEmailPopUp();
		$form->Output();
	} // end of fn InputForm
	
	function SelectEmailPopUp()
	{	ob_start();
		echo '<script type="text/javascript">$().ready(function(){$("body").append($("#ems_modal_popup"));$("#ems_modal_popup").jqm({trigger:"#emsTrigger"});});</script>', 
				"<!-- START email select modal popup -->\n<div id='ems_modal_popup' class='jqmWindow'>\n<a href='#' class='jqmClose submit'>Close</a>\n<div id='ems_popup_inner'><ul>";
		foreach ($this->GetEmailsToUse() as $email)
		{	echo "<li onclick='emsSelectAddress(\"", $email["emailfrom"], "\");'>", $email["emailfrom"], " (", $this->InputSafeString($email["emaillabel"]), ")</li>";
		}
		echo "</ul></div></div>\n<!-- EOF email select modal popup -->\n";
		return ob_get_clean();
	} // end of fn SelectEmailPopUp
	
	function GetEmailsToUse()
	{	$emails = array();
		// get from countries
		$sql = "SELECT emailfrom, ccode, shortname AS emaillabel FROM countries WHERE NOT emailfrom=''";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($row["emailfrom"] && !$emails[$row["emailfrom"]])
				{	$emails[$row["emailfrom"]] = $row;
				}
			}
		}
		// get from cities
		$sql = "SELECT emailfrom, cityname AS emaillabel FROM cities WHERE NOT emailfrom=''";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($row["emailfrom"] && !$emails[$row["emailfrom"]])
				{	$emails[$row["emailfrom"]] = $row;
				}
			}
		}
		// get from courses
		$sql = "SELECT emailfrom, 'course specific' AS emaillabel FROM courses WHERE NOT emailfrom=''";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($row["emailfrom"] && !$emails[$row["emailfrom"]])
				{	$emails[$row["emailfrom"]] = $row;
				}
			}
		}
		// get from site emails
		$sql = "SELECT sendfrom AS emailfrom, 'previously used' AS emaillabel FROM siteemails WHERE NOT sendfrom=''";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($row["emailfrom"] && !$emails[$row["emailfrom"]])
				{	$emails[$row["emailfrom"]] = $row;
				}
			}
		}
		
		return $emails;
	} // end of fn GetEmailsToUse
	
	function IFrameDisplay()
	{	echo "<p>Subject: ", $this->InputSafeString($this->details["subject"]), "</p>\n<iframe width='500px' height='500px' src='siteemail_display.php?disp=full&id=", $this->id, "'></iframe>\n";
	} // end of fn IFrameDisplay
	
	function Footer()
	{	return str_replace("~sendfrom~", $this->SendFromEmail(), $this->footer);
	} // end of fn Footer
	
	function SendEmail($emails = array(), $allow_duplicates = false)
	{	$sentcount = 0;
		if ($this->id && $emails)
		{	
			$mailbody = stripslashes($this->details["mailbody"]) . $this->Footer();
			$mail = new HTMLMail();
			$mail->SetSubject(stripslashes($this->details["subject"]));
			$mail->SetFrom($this->SendFromEmail());

			foreach ($emails as $email)
			{	if ($this->ValidEMail($email["email"]))
				{	
					if ($allow_duplicates || !$this->SentToEmail($email["email"]))
					{	$htmlbody = $mailbody;
						/*if ($name = stripslashes($email["name"]))
						{	$header = str_replace("~name~", $name, $this->header);
						} else
						{	$header = str_replace("~name~", "", $this->header);
						}
						$htmlbody = $header . $mailbody;*/
						$plainbody = strip_tags($htmlbody);
						//$mail->AddMailFooter($htmlbody, $plainbody, $email["userid"] > 0);
						$mail->Send($email["email"], $htmlbody, $plainbody, array("sitemail.php"));
						$this->RecordSent($email);
						$sentcount++;
					}
				}
			}
		}
		return $sentcount;
	} // end of fn SendEmail
	
	function RecordSent($email = array())
	{	$sql = "INSERT INTO siteemailssent SET emid=$this->id, userid=" . (int)$email["userid"] . ", whensent='" . $this->datefn->SQLDateTime() . "', email='" . $this->SQLSafe($email["email"]) . "', username='" . $this->SQLSafe($email["name"]) . "'";
		$this->db->Query($sql);
	} // end of fn RecordSent
	
	function GetSentHistory()
	{	$sent = array();
		$sql = "SELECT LEFT(whensent, 16) AS date_minute, COUNT(userid) AS user_count FROM siteemailssent WHERE userid>0 AND emid=$this->id GROUP BY date_minute ORDER BY date_minute";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$sent[$row["date_minute"] . ":00"] = $row["user_count"];
			}
		}else echo "<p>", $this->db->Error(), "</p>\n";
		
		return $sent;
	} // end of fn GetSentHistory
	
	function HistoryTable()
	{	if ($this->id)
		{	if ($sent = $this->GetSentHistory())
			{	echo "<table id='semHistory'>\n<tr><th>sent on</th><th>to</th></tr>\n";
				foreach ($sent as $date=>$count)
				{	echo "<tr>\n<td>", date("d-M-Y @H:i", strtotime($date)), "</td>\n<td>", (int)$count, "</td>\n</tr>\n";
				}
				echo "<tr><th>Total</th><th>", array_sum($sent), "</th></tr>\n</table>\n";
			} else
			{	echo "<p>not sent yet</p>\n";
			}
		}
	} // end of fn HistoryTable
	
	function SendFromEmail()
	{	if ($this->details["sendfrom"] && $this->ValidEmail($this->details["sendfrom"]))
		{	return $this->details["sendfrom"];
		} else
		{	return $this->sendfrom_default;
		}
	} // end of fn SendFromEmail
	
} // end if class defn SiteEmail
?>