<?php
class AdminCountryPaymentOption extends PaymentOption
{	var $ctry_details = array();
	var $ctry = "";

	function __construct($id = "", $ctry = "")
	{	parent::__construct($id);
		if ($this->ctry = $ctry)
		{	$this->ctry_details = $this->GetCountryDetails($this->ctry);
		}
	} // fn __construct
	
	function DeleteAllCtryFields()
	{	$sql = "DELETE FROM pmtoptions_ctry WHERE ccode='{$this->ctry}' AND optvalue='{$this->id}'";
		if ($result = $this->db->Query($sql))
		{	if ($this->db->AffectedRows())
			{	$this->ctry_details = array();
				return true;
			}
		}
	} // end of fn DeleteAllCtryFields
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		
		if ($this->DeleteAllCtryFields())
		{	$saved = 1;
		}
		$basefields = ", ccode='{$this->ctry}', optvalue='{$this->id}'";
		foreach ($data as $field=>$raw_value)
		{	if ($value = $this->SQLSafe($raw_value))
			{	// then save this one
				$sql = "INSERT INTO pmtoptions_ctry SET optfield='$field', fieldvalue='$value'" . $basefields;
				if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	$saved++;
						$this->RecordAdminAction(array("tablename"=>"pmtoptions_ctry", "tableid"=>$this->ctry, "area"=>"country payment options", "action"=>$field, "actionto"=>$raw_value));
					}
				}
			}
		}
		
		if ($saved)
		{	$success[] = "changes saved";
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		ob_start();
		if (!$data = $this->ctry_details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?ctry=" . $this->InputSafeString($this->ctry) . "&pmt={$this->id}");
		$form->AddTextInput("Option text", "optname", $this->InputSafeString($data["optname"]), "long", 50);
		$form->AddTextInput("Order to list", "optorder", (int)$data["optorder"], "short", 5);
		$form->AddTextInput("Percent fee %", "percentfee", round($data["percentfee"], 2), "short", 5);
		$form->AddTextInput("Fee description", "feetext", $this->InputSafeString($data["feetext"]), "", 20);
		$form->AddCheckBox("Suppress for this country", "suppress", "1", $data["suppress"]);
		$form->AddCheckBox("Make default for this country", "ctrydefault", "1", $data["ctrydefault"]);
	//	$form->AddCheckBox("make this the default option", "defoption", "1", $data["defoption"]);
		$form->AddTextArea("User instructions", "opttext", $this->InputSafeString($data["opttext"]), "tinymce", 0, 0, $rows = 6, $cols = 20);
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Payment Option", "submit");
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn AdminCountryPaymentOption
?>