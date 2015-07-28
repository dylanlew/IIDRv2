<?php
class AdminFPTLang extends Base
{	
	var $language = "";
	var $labels = array();
	
	function __construct($language = "")
	{	parent::__construct();
		$this->language = new AdminLanguage($language);
		$this->Get();
	} //  end of fn __construct
	
	function Reset()
	{	$this->name = "";
		$this->labels = array();
	} // end of fn Reset
	
	function Get()
	{	$this->Reset();
		
		// first get all labels
		$sql = "SELECT * FROM fptext WHERE lang='{$this->def_lang}' ORDER BY fptlabel";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->labels[$row["fptlabel"]]  = $row;
				if ($this->language->code != $this->def_lang)
				{	$this->labels[$row["fptlabel"]]["deflang"] = $this->labels[$row["fptlabel"]]["content"];
					$this->labels[$row["fptlabel"]]["content"] = "";
				}
			}
		}
		
		// now all labels for language
		if ($this->language->code != $this->def_lang)
		{	$sql = "SELECT * FROM fptext WHERE lang='{$this->language->code}'";
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->labels[$row["fptlabel"]]["content"] = $row["content"];
				}
			}
		}
		
	} // end of fn Get
	
	function Save($contents = array())
	{	
		$fail = array();
		$success = array();
		
		foreach ($contents as $name=>$text)
		{	$sql = "";
			$text = $this->SQLSafe($text);
			if ($this->labels[$name]["content"])
			{	if ($text)
				{	$sql = "UPDATE fptext SET content='$text' WHERE fptlabel='$name' AND lang='{$this->language->code}'";
				} else
				{	$sql = "DELETE FROM fptext WHERE fptlabel='$name' AND lang='{$this->language->code}'";
				}
			} else
			{	if ($text)
				{	$sql = "INSERT INTO fptext SET fptlabel='$name', lang='{$this->language->code}', content='$text'";
				}
			}
			if ($sql)
			{	
				if ($result = $this->db->Query($sql))
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
	
	function UpdateForm()
	{
		if ($_GET["restart"])
		{	$gtrans = new GoogleTranslate();
		//	$this->WarningMessage("Translated for you by Google: please check and correct");
		}
		$form = new Form("fptbylang.php?lang=" . $this->language->code, "pageedit");
		foreach ($this->labels as $lname=>$label)
		{	if ($label["deflang"])
			{	$lname .= " ({$this->def_lang}=\"" . $this->InputSafeString($label["deflang"]) . "\")";
			}
			if ($_GET["restart"])
			{	$label["content"] = $gtrans->OutputTranslation($this->def_lang, $this->language->code, $label["deflang"]);
			}
			$form->AddTextArea($lname, "content[{$label["fptlabel"]}]", $this->InputSafeString($label["content"]), "fp_textarea");
		//	$form->AddTextInput($lname, "content[{$label["fptlabel"]}]", $this->InputSafeString($label["content"]), "long", 255);
		}
		$form->AddSubmitButton("", "Save", "submit");
		echo "<h3>", $this->language->details["langname"];
		if ($this->def_lang != $this->language->code)
		{	echo " - <a href='fptbylang.php?lang=", $this->language->code, "&restart=1'>restart with translation of original</a>";
		}
		echo "</h3>\n";
		$form->Output();
	} // end of fn UpdateForm
	
} // end of defn AdminFPTLang
?>