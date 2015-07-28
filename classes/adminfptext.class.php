<?php
class AdminFPText extends Base
{	
	var $name = 0;
	var $details = array();
	var $langused = array();
	
	function __construct($name = "")
	{	parent::__construct();
		$this->Get($name);
	} //  end of fn __construct
	
	function GetAllLanguages()
	{	$this->langused = array();
		$sql = "SELECT * FROM fptext WHERE fptlabel='{$this->name}'";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->langused[$row["lang"]] = $row["content"];
			}
		}
		
	} // end of fn GetAllLanguages
	
	function Reset()
	{	$this->name = "";
		$this->details = array();
	} // end of fn Reset
	
	function Get($name = "")
	{	$this->Reset();
		if (is_array($name))
		{	$this->details = $name;
			$this->name = $name["fptlabel"];

			$this->GetAllLanguages();
		} else
		{	if ($name)
			{	$sql = "SELECT * FROM fptext WHERE fptlabel='" . $name . "' AND lang='{$this->def_lang}'";
				if ($result = $this->db->Query($sql))
				{	if ($row = $this->db->FetchArray($result))
					{	return $this->Get($row);
					}
				}
			}
		}
		
	} // end of fn Get
	
	function Create($data = array())
	{	
		$fail = array();
		$success = array();
		$fields = array();
		
		if ($fptlabel = $this->SQLSafe($_POST["fptlabel"]))
		{	// check if already used
			if ($result = $this->db->Query("SELECT * FROM fptext WHERE fptlabel='$fptlabel'"))
			{	if ($this->db->NumRows($result))
				{	$fail[] = "label already used";
				}
			}
			$fields[] = "fptlabel='$fptlabel'";
		} else
		{	$fail[] = "label missing";
		}
		
		$fields[] = "lang='{$this->def_lang}'";
		$fields[] = "content='" . $this->SQLSafe($_POST["content"]) . "'";
		
		if (!$fail && ($set = implode(", ", $fields)))
		{	
			$sql = "INSERT INTO fptext SET $set";
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = "New label created";
				} else $fail[] = "save failed";
			} else $fail[] = "save failed: " . $this->db->Error();
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Create
	
	function Save($contents = array())
	{	
		$fail = array();
		$success = array();
		
		foreach ($contents as $lcode=>$text)
		{	$sql = "";
			$text = $this->SQLSafe($text);
			if ($this->langused[$lcode])
			{	if ($text)
				{	$sql = "UPDATE fptext SET content='$text' WHERE fptlabel='$this->name' AND lang='$lcode'";
				} else
				{	$sql = "DELETE FROM fptext WHERE fptlabel='$this->name' AND lang='$lcode'";
				}
			} else
			{	if ($text)
				{	$sql = "INSERT INTO fptext SET fptlabel='$this->name', lang='$lcode', content='$text'";
				}
			}
			if ($sql)
			{	if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	$saved++;
					}
				}
			}
		}
		
		if ($saved)
		{	$success[] = "changes saved";
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Save
	
	function CreateForm()
	{	$form = new Form("fptcreate.php", "pageedit");
		$form->AddTextInput("Label", "fptlabel", $this->InputSafeString($_POST["fptlabel"]), "", 30);
		$form->AddTextInput("Text ({$this->def_lang})", "content", $this->InputSafeString($_POST["content"]), "long", 255);
		$form->AddSubmitButton("", "Create", "submit");
		$form->Output();
	} // end of fn CreateForm
	
	function UpdateForm()
	{
		$form = new Form("fptbylabel.php?name=" . $this->name, "pageedit");
		foreach ($this->LangList() as $lcode=>$lname)
		{	$text = $this->InputSafeString($this->langused[$lcode]);
			$form->AddTextArea($lname, "content[$lcode]", $text, "fp_textarea");
		}
		$form->AddSubmitButton("", "Save", "submit");
		echo "<h3>", $this->name, "</h3>\n";
		$form->Output();
	} // end of fn UpdateForm
	
	function LangList()
	{	$langlist = array();
		if ($result = $this->db->Query("SELECT * FROM languages ORDER BY disporder"))
		{	while ($row = $this->db->FetchArray($result))
			{	$langlist[$row["langcode"]] = $row["langname"];
			}
		}
		return $langlist;
	} // end of fn LangList
	
	public function Delete()
	{	$sql = 'DELETE FROM fptext WHERE fptlabel="' . $this->SQLSafe($this->name) . '"';
		if ($result = $this->db->Query($sql))
		{	if ($this->db->AffectedRows())
			{	$this->Reset();
				return true;
			}
		}
	} // end of fn Delete
	
} // end of defn AdminFPText
?>