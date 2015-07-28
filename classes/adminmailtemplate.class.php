<?php
class AdminMailTemplate extends MailTemplate
{	

	function __construct($id = 0)
	{	parent::__construct($id);
	} //  end of fn __construct
	
	
	function CanDelete()
	{	return false;
	} // end of fn CanDelete
	
	function Delete()
	{	return false;
	} // end of fn Delete

	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();

		$admin_actions = array();

		if ($subject = $this->SQLSafe($data["subject"]))
		{	$fields[] = "subject='$subject'";
			if ($this->id && ($data["subject"] != $this->details["subject"]))
			{	$admin_actions[] = array("action"=>"Subject", "actionfrom"=>$this->details["subject"], "actionto"=>$data["subject"]);
			}
		} else
		{	$fail[] = "subject missing";
		}

		if ($htmltext = $this->SQLSafe($data["htmltext"]))
		{	$fields[] = "htmltext='$htmltext'";
			if ($this->id && ($data["htmltext"] != $this->details["htmltext"]))
			{	$admin_actions[] = array("action"=>"HTML Text", "actionfrom"=>$this->details["htmltext"], "actionto"=>$data["htmltext"]);
			}
		} else
		{	$fail[] = "HTML text missing";
		}

		if ($plaintext = $this->SQLSafe($data["plaintext"]))
		{	$fields[] = "plaintext='$plaintext'";
			if ($this->id && ($data["plaintext"] != $this->details["plaintext"]))
			{	$admin_actions[] = array("action"=>"Plain Text", "actionfrom"=>$this->details["plaintext"], "actionto"=>$data["plaintext"]);
			}
		} else
		{	$fail[] = "plain text missing";
		}
		
		if (!$fail)
		{	
			if ($set = implode(", ", $fields))
			{	if ($this->id)
				{	$sql = "UPDATE mailtemplates SET $set WHERE mailid=$this->id";
				} else
				{	$sql = "INSERT INTO mailtemplates SET $set, mailid=$this->id";
				}
				if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	$success[] = "text changes saved";
						$base_parameters = array("tablename"=>"mailtemplates", "tableid"=>$this->id, "area"=>"mail templates");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
					}
					$this->Get($this->id);
				
				}else echo "<p>", $this->db->Error(), "</p>\n";
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm($courseid = 0)
	{	ob_start();

		if ($data = $this->details)
		{	
			if ($_POST)
			{	// initialise details from this
				foreach ($_POST as $field=>$value)
				{	$data[$field ] = $value;
				}
			}
			
		} else
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id, "emtInputForm");
		$form->AddTextInput("Mail subject", "subject", $this->InputSafeString($data["subject"]), "long", 255, 1);
		$form->AddTextArea("HTML Text", $name = "htmltext", stripslashes($data["htmltext"]), "tinymce", 0, 0, 20, 40);
		$form->AddTextArea("Plain Text", $name = "plaintext", stripslashes($data["plaintext"]), "", 0, 0, 20, 40);
		$form->AddSubmitButton("", "Save Changes", "submit");
		echo "<h3>Editing ... ", $this->InputSafeString($this->details["mailname"]), "</h3>";
		$form->Output();
		echo "<div id='emtFieldsList'>\n";
		if ($this->fields)
		{	echo "<h4>Available fields:</h4>\n<ul>\n";
			foreach ($this->fields as $field)
			{	echo "<li><input value='{", $field["fieldname"], "}' /></li>\n";
			}
			echo "</ul>\n";
		}
		echo "</div>\n<div class='clear'></div>\n<script>\n$('#emtFieldsList>ul>li>input').click(function() {\n$(this).select();\n});\n</script>\n<div class='emtSample'><h4>Sample HTML text (as saved)</h4>\n<div>", 
				$this->BuildHTMLEmailText(), 
				"</div></div>\n<div class='emtSample'><h4>Sample Plain text (as saved)</h4>\n<div>", 
				nl2br(htmlentities($this->BuildHTMLPlainText())), 
				"</div></div>";
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn AdminMailTemplate
?>