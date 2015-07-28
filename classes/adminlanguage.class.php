<?php
class AdminLanguage extends Language
{	
	function __construct($code = 0)
	{	parent::__construct($code);
	} //  end of fn __construct
	
	function CodeUsed($langcode = "")
	{	$sql = "SELECT * FROM languages WHERE langcode='$langcode'";
		if ($result = $this->db->Query($sql))
		{	return $this->db->AffectedRows();
		}
	
	} // end of fn CodeUsed
	
	function CodeValid($langcode = "")
	{	$gtrans = new GoogleTranslate();
		return $gtrans->LanguageUseable($langcode);
	} // end of fn CodeValid
	
	function Save($data = array())
	{	
		$fail = array();
		$success = array();
		$fields = array();
		
		if (!$this->code)
		{	if ($langcode = $this->SQLSafe($data["langcode"]))
			{	if ($this->CodeUsed($langcode))
				{	$fail[] = "code already used";
				} else
				{	if ($this->CodeValid($langcode))
					{	$fields[] = "langcode='$langcode'";
					} else
					{	$fail[] = "invalid code";
					}
				}
			} else
			{	$fail[] = "code missing";
			}
		}
		
		if ($langname = $this->SQLSafe($data["langname"]))
		{	$fields[] = "langname='$langname'";
		} else
		{	$fail[] = "language name missing";
		}
		
		if ($listname = $this->SQLSafe($data["listname"]))
		{	$fields[] = "listname='$listname'";
		} else
		{	if ($langname)
			{	$fields[] = "listname='$langname'";
			}
		}
		
		$country = $this->SQLSafe($data["country"]);
		$fields[] = "country='$country'";

		$fields[] = "disporder=" . (int)$data["disporder"];
		$fields[] = "live=" . ($data["live"] || ($this->code == $this->def_lang) ? "1" : "0");

		if ((!$fail || $this->code) && $set = implode(", ", $fields))
		{	
			if ($this->code)
			{	$sql = "UPDATE languages SET $set WHERE langcode='" . $this->code . "'";
			} else
			{	$sql = "INSERT INTO languages SET $set";
				//return;
			}
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->code)
					{	$success[] = "Changes saved";
						$this->Get($this->code);
					} else
					{	$success[] = "New category created";
						$this->Get($langcode);
					}
				}
			}
			
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function CanDelete()
	{	return false;
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	
		}
	} // end of fn Delete
	
	function InputForm()
	{	
		if ($this->code)
		{	$data = $this->details;
		} else
		{	$data = $_POST;
		}
		
		$form = new Form("langedit.php?lang=" . $this->code, "pageedit");
		if ($this->code)
		{	$form->AddHiddenInput("langcode", $this->InputSafeString($data["langcode"]));
		} else
		{	$gtrans = new GoogleTranslate();
			$links = array();
			foreach ($gtrans->SplitList() as $letter=>$list)
			{	$links[] = "<a onclick='alert(\"" . implode("\\n", $list) . "\"); return false;'>$letter</a>";
			}
			$form->AddTextInput("Language code, available:<br />" . implode(" | ", $links), "langcode", $this->InputSafeString($data["langcode"]), "", 6);
		}
		$form->AddTextInput("Language name", "langname", $this->InputSafeString($data["langname"]), "", 50);
		$form->AddTextInput("Select list name", "listname", $this->InputSafeString($data["listname"]), "", 50);
		$form->AddTextInput("Order in list", "disporder", (int)$data["disporder"], "num", 4);
		$form->AddSelect("Default country", "country", $data["country"], "", $this->GetCountries(), true, true);
		if ($this->code && ($this->code != $this->def_lang))
		{	$form->AddCheckBox("Make live", "live", 1, $data["live"]);
		}
		$form->AddSubmitButton("", $this->code ? "Save" : "Create", "submit");
		$form->Output();
		
	} // end of fn InputForm
	
} // end of defn AdminLanguage
?>