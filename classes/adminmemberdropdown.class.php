<?php
class AdminMemberDropdown extends MemberDropdown
{	var $admintitle = "";
	var $langused = array();
	
	function __construct($id = "")
	{	parent::__construct($id);
		$this->GetLangUsed();
		$this->GetAdminTitle();
	} // fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	if (!$this->admintitle = $this->details["textdesc"])
			{	if ($details = $this->GetDefaultDetails())
				{	$this->admintitle = $details["textdesc"] . " [{$this->language}]";
				}
			}
		}
	} // end of fn GetAdminTitle
	
	function GetDefaultDetails()
	{	$sql = "SELECT * FROM memberdropdowns_lang WHERE mdname='" . $this->SQLSafe($this->id) . "' AND lang='{$this->def_lang}'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row;
			}
		}
		return array();
	} // end of fn GetDefaultDetails
	
	function AssignDDLanguage()
	{	if (!$this->language = $_GET["lang"])
		{	$this->language = $this->def_lang;
		}
	} // end of fn AssignDDLanguage
	
	function AddDetailsForDefaultLang(){}
	
	function GetLangUsed()
	{	$this->langused = array();
		if ($this->id)
		{	if ($result = $this->db->Query("SELECT lang FROM memberdropdowns_lang WHERE mdname='" . $this->SQLSafe($this->id) . "'"))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->langused[$row["lang"]] = true;
				}
			}
		}
	} // end of fn GetLangUsed
	
	function InputForm()
	{	
		ob_start();

		$data = $this->details;
		if ($this->id)
		{	
			if (!$this->langused[$this->language])
			{	if ($_POST)
				{	// initialise details from this
					foreach ($_POST as $field=>$value)
					{	$data[$field ] = $value;
					}
				}
			}
		} else
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&lang=" . $this->language);
		$form->AddTextInput("Label text", "textdesc", $this->InputSafeString($data["textdesc"]), "long", 255, 1);
		$form->AddTextInput("Order to list", "listorder", (int)$data["listorder"], "short", 5);
		$form->AddRawText($this->OptionsTable());
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Payment Option", "submit");
		echo "<h4>Dropdown box properties for field {", $this->id, "}</h4>";
		$this->AdminEditLangList();
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm

	function OptionsTable()
	{	ob_start();
		echo "<table id='mdoptions_table'>\n<tr><th>Option values</th><th>Order in dropdown</th></tr>\n";
		foreach ($this->options as $option)
		{	echo $this->OptionsTableLine($option);
		}
		for ($i = 1; $i <= 3; $i++)
		{	echo $this->OptionsTableLine();
		}
		echo "</table>\n";
		return ob_get_clean();
	} // end of fn OptionsTable

	function OptionsTableLine($option = array())
	{	ob_start();
		static $linecount = 0;
		$linecount++;
		echo "<tr><td><input type='hidden' name='mdoid[", $linecount, "]' value='", (int)$option["mdoid"], "' /><input type='text' class='mdot_text' name='optvalue[", $linecount, "]' value='", $this->InputSafeString($option["optvalue"]), "' /></td><td><input type='text' class='mdot_order' name='optorder[", $linecount, "]' value='", isset($option["optorder"]) ? (int)$option["optorder"] : "", "' /></td></tr>\n";
		return ob_get_clean();
	} // end of fn OptionsTableLine
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$l_fields = array();
		
		if ($textdesc = $this->SQLSafe($data["textdesc"]))
		{	$l_fields[] = "textdesc='$textdesc'";
		} else
		{	$fail[] = "label text is missing";
		}

		$fields[] = "listorder=" . (int)$data["listorder"];
		
		if ($set = implode(", ", $fields))
		{	$sql = "UPDATE memberdropdowns SET $set WHERE mdname='" . $this->SQLSafe($this->id) . "'";
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = "Changes saved";
				}
				if ($set = implode(", ", $l_fields))
				{	if ($this->langused[$this->language])
					{	$sql = "UPDATE memberdropdowns_lang SET $set WHERE mdname='" . $this->SQLSafe($this->id) . "' AND lang='$this->language'";
					} else
					{	$sql = "INSERT INTO memberdropdowns_lang SET $set, mdname='" . $this->SQLSafe($this->id) . "', lang='$this->language'";
					}
					if ($result = $this->db->Query($sql))
					{	if ($this->db->AffectedRows())
						{	$success[] = "text changes saved";
						}
					}
				}
				$this->Get($this->id);
				$this->GetLangUsed();
			}
		}
		
		// now save options
		foreach ($data["mdoid"] as $key=>$mdoid)
		{	$optorder = (int)$data["optorder"][$key];
			if ($mdoid) // existing option
			{	if ($optvalue = $this->SQLSafe($data["optvalue"][$key]))
				{	$sql = "UPDATE mdropdownoptions SET optorder=$optorder, optvalue='$optvalue' WHERE mdname='{$this->id}' AND lang='{$this->language}' AND mdoid=" . (int)$mdoid;
					if ($result = $this->db->Query($sql))
					{	if ($this->db->AffectedRows())
						{	$success[] = "option \"" . $this->InputSafeString($optvalue) . "\" amended";
						}
					}
				} else
				{	$sql = "DELETE FROM mdropdownoptions WHERE mdname='{$this->id}' AND lang='{$this->language}' AND mdoid=" . (int)$mdoid;
					if ($result = $this->db->Query($sql))
					{	if ($this->db->AffectedRows())
						{	$success[] = "option has been deleted";
						}
					}
				}
			} else // i.e. new option
			{	if ($optvalue = $this->SQLSafe($data["optvalue"][$key]))
				{	$sql = "INSERT INTO mdropdownoptions SET optorder=$optorder, optvalue='$optvalue', mdname='{$this->id}', lang='{$this->language}'";
					if ($result = $this->db->Query($sql))
					{	if ($this->db->AffectedRows())
						{	$success[] = "option \"" . $this->InputSafeString($optvalue) . "\" added";
						}
					}
				}
			}
		}
		
		if ($success)
		{	$this->Get($this->id);
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function SelectOptionsAllLang($add = "")
	{	$options = array();
		$sql = "SELECT * FROM mdropdownoptions WHERE mdname='{$this->id}' ORDER BY lang, optorder";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$options[$row["optvalue"]] = "[{$row["lang"]}] {$row["optvalue"]}";
			}
		}
		if ($add && !isset($options[$add]))
		{	$options[$add] = $add;
		}
		
		return $options;
	} // end of fn SelectOptionsAllLang
	
} // end of defn AdminMemberDropdown
?>