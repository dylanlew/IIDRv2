<?php
class AdminFPTextLang extends AdminFPText
{	var $editlang = "";
	var $language = array();

	function __construct($name = "", $lang = "")
	{	$this->editlang = $lang;
		parent::__construct($name);
		$this->GetLanguage($this->editlang);
	} //  end of fn __construct

	function GetLanguage($langcode = "")
	{	$this->language = array();
		if ($result = $this->db->Query("SELECT * FROM languages WHERE langcode='" . $this->SQLSafe($langcode) . "'"))
		{	if ($row = $this->db->FetchArray($result))
			{	$this->language = $row;
			}
		}
		
	} // end of fn GetLanguage
	
	function Save($content = "")
	{	
		$fail = array();
		$success = array();
	
		$sql = "";
		$text = $this->SQLSafe($content);
		if ($this->langused[$this->editlang])
		{	if ($text)
			{	$sql = "UPDATE fptext SET content='$text' WHERE fptlabel='$this->name' AND lang='{$this->editlang}'";
			} else
			{	$sql = "DELETE FROM fptext WHERE fptlabel='$this->name' AND lang='{$this->editlang}'";
			}
		} else
		{	if ($text)
			{	$sql = "INSERT INTO fptext SET fptlabel='$this->name', lang='{$this->editlang}', content='$text'";
			}
		}
		if ($sql)
		{	if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = "changes saved";
				}
			}
		}
	
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Save
	
	function UpdateForm()
	{
		$text = $this->InputSafeString($this->langused[$this->editlang]);
		$form = new Form("fptbylabellang.php?name=" . $this->name . "&lang=" . $this->editlang, "pageedit");
		$form->AddTextArea($this->language["langname"], "content", $text, "fp_textarea");
	//	$form->AddTextInput($this->language["langname"], "content", $text, "long", 255);
		$form->AddSubmitButton("", "Save", "submit");
		echo '<h3>"', $this->name, '"';
		if ($this->editlang != $this->def_lang)
		{	echo ' - ', $this->def_lang, ' = "', 
						$this->InputSafeString($this->langused[$this->def_lang]), '"';
		}
		echo '</h3>';
		$form->Output();
	} // end of fn UpdateForm
	
	function LangList()
	{	$langlist = array();
		if ($result = $this->db->Query("SELECT * FROM languages WHERE langcode='{$this->editlang}' ORDER BY disporder"))
		{	while ($row = $this->db->FetchArray($result))
			{	$langlist[$row["langcode"]] = $row["langname"];
			}
		}
		return $langlist;
	} // end of fn LangList
	
} // end of defn AdminFPText
?>