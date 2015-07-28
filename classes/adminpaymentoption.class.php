<?php
class AdminPaymentOption extends PaymentOption
{	var $admintitle = "";
	var $langused = array();
	
	function __construct($id = "", $country = "")
	{	parent::__construct($id, $country);
		$this->GetLangUsed();
		$this->GetAdminTitle();
	} // fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	if (!$this->admintitle = $this->details["optname"])
			{	if ($details = $this->GetDefaultDetails())
				{	$this->admintitle = $details["optname"] . " [{$this->language}]";
				}
			}
		}
	} // end of fn GetAdminTitle
	
	function GetDefaultDetails()
	{	$sql = "SELECT * FROM pmtoptions_lang WHERE optvalue='$this->id' AND lang='{$this->def_lang}'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row;
			}
		}
		return array();
	} // end of fn GetDefaultDetails
	
	function AssignPmtLanguage()
	{	if (!$this->language = $_GET["lang"])
		{	$this->language = $this->def_lang;
		}
	} // end of fn AssignPmtLanguage
	
	function AddDetailsForDefaultLang(){}
	
	function GetLangUsed()
	{	$this->langused = array();
		if ($this->id)
		{	if ($result = $this->db->Query("SELECT lang FROM pmtoptions_lang WHERE optvalue='$this->id'"))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->langused[$row["lang"]] = true;
				}
			}
		}
	} // end of fn GetLangUsed
	
	function CanDelete()
	{	return false;
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query("DELETE FROM pmtoptions WHERE optvalue='" . $this->SQLSafe($this->id) . "'"))
			{	if ($this->db->AffectedRows())
				{	$this->db->Query("DELETE FROM pmtoptions_lang WHERE optvalue='$this->id'");
					$this->RecordAdminAction(array("tablename"=>"pmtoptions", "tableid"=>$this->id, "area"=>"pmtoptions", "action"=>"deleted"));
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete

	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$l_fields = array();
		$admin_actions = array();
		
		if ($optname = $this->SQLSafe($data["optname"]))
		{	$l_fields[] = "optname='$optname'";
			if ($this->id && ($data["optname"] != $this->details["optname"]))
			{	$admin_actions[] = array("action"=>"Option name ({$this->language})", "actionfrom"=>$this->details["optname"], "actionto"=>$data["optname"]);
			}
		} else
		{	$fail[] = "option text missing";
		}
		
		$opttext = $this->SQLSafe($data["opttext"]);
		$l_fields[] = "opttext='$opttext'";
		if ($this->id && ($data["opttext"] != $this->details["opttext"]))
		{	$admin_actions[] = array("action"=>"Help text ({$this->language})", "actionfrom"=>$this->details["opttext"], "actionto"=>$data["opttext"], "actiontype"=>"html");
		}
		
		$feetext = $this->SQLSafe($data["feetext"]);
		$l_fields[] = "feetext='$feetext'";
		if ($this->id && ($data["feetext"] != $this->details["feetext"]))
		{	$admin_actions[] = array("action"=>"Fee text ({$this->language})", "actionfrom"=>$this->details["feetext"], "actionto"=>$data["feetext"]);
		}
		
		if ($data["paypal"] && $data["payondoor"])
		{	$fail[] = "option cannot be \"pay on door\" and \"submit to paypal\"";
		} else
		{	$paypal = $data["paypal"] ? "1" : "0";
			$fields[] = "paypal=" . $paypal;
			if ($this->id && ($paypal != $this->details["paypal"]))
			{	$admin_actions[] = array("action"=>"Paypal", "actionfrom"=>$this->details["paypal"], "actionto"=>$paypal);
			}
			$payondoor = $data["payondoor"] ? "1" : "0";
			$fields[] = "payondoor=" . $payondoor;
			if ($this->id && ($payondoor != $this->details["payondoor"]))
			{	$admin_actions[] = array("action"=>"Pay on door", "actionfrom"=>$this->details["payondoor"], "actionto"=>$payondoor);
			}
		}
		
		$defoption = $data["defoption"] ? "1" : "0";
		$fields[] = "defoption=" . $defoption;
		if ($this->id && ($defoption != $this->details["defoption"]))
		{	$admin_actions[] = array("action"=>"Set as default", "actionfrom"=>$this->details["defoption"], "actionto"=>$defoption);
		}

		$optorder = (int)$data["optorder"];
		$fields[] = "optorder=" . $optorder;
		if ($this->id && ($optorder != $this->details["optorder"]))
		{	$admin_actions[] = array("action"=>"List order", "actionfrom"=>$this->details["optorder"], "actionto"=>$optorder);
		}
		
		$percentfee = round($data["percentfee"], 2);
		$fields[] = "percentfee=" . $percentfee;
		if ($this->id && ($percentfee != $this->details["percentfee"]))
		{	$admin_actions[] = array("action"=>"Fee %", "actionfrom"=>$this->details["percentfee"], "actionto"=>$percentfee);
		}
		
		if ($set = implode(", ", $fields))
		{	$sql = "UPDATE pmtoptions SET $set WHERE optvalue='" . $this->SQLSafe($this->id) . "'";
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$record_changes = true;
					$success[] = "Changes saved";
					$this->Get($this->id);
					if ($this->details["defoption"])
					{	$sql = "UPDATE pmtoptions SET defoption=0 WHERE NOT optvalue='" . $this->SQLSafe($this->id) . "'";
						$this->db->Query($sql);
					}
				}
				
				if ($this->id)
				{	
					if ($set = implode(", ", $l_fields))
					{	if ($this->langused[$this->language])
						{	$sql = "UPDATE pmtoptions_lang SET $set WHERE optvalue='" . $this->SQLSafe($this->id) . "' AND lang='$this->language'";
						} else
						{	$sql = "INSERT INTO pmtoptions_lang SET $set, optvalue='" . $this->SQLSafe($this->id) . "', lang='$this->language'";
						}
						if ($result = $this->db->Query($sql))
						{	if ($this->db->AffectedRows())
							{	$success[] = "text changes saved";
								$record_changes = true;
							}
						}
					}
					$this->Get($this->id);
					$this->GetLangUsed();
				}
				
				if ($record_changes)
				{	$base_parameters = array("tablename"=>"pmtoptions", "tableid"=>$this->id, "area"=>"pmtoptions");
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		ob_start();

		$data = $this->details;
		if (!$this->langused[$this->language])
		{	if ($_POST)
			{	// initialise details from this
				foreach ($_POST as $field=>$value)
				{	$data[$field ] = $value;
				}
			}
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&lang=" . $this->language);
		$form->AddTextInput("Option text", "optname", $this->InputSafeString($data["optname"]), "long", 50, 1);
		$form->AddTextInput("Order to list", "optorder", (int)$data["optorder"], "short", 5);
		$form->AddCheckBox("Paypal (option submits to Paypal)", "paypal", "1", $data["paypal"]);
		$form->AddTextInput("Percent fee %", "percentfee", round($data["percentfee"], 2), "short", 5);
		$form->AddTextInput("Fee description", "feetext", $this->InputSafeString($data["feetext"]), "", 20);
		$form->AddCheckBox("Pay on door (for stats)", "payondoor", "1", $data["payondoor"]);
		$form->AddCheckBox("make this the default option", "defoption", "1", $data["defoption"]);
		$form->AddTextArea("User instructions", "opttext", $this->InputSafeString($data["opttext"]), "tinymce", 0, 0, $rows = 6, $cols = 20);
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Payment Option", "submit");
		if ($histlink = $this->DisplayHistoryLink("pmtoptions", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->id)
		{	echo "<h3>Editing ... ", $this->InputSafeString($this->admintitle), "</h3>";
			if ($this->CanDelete())
			{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", $_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", "delete this payment option</a></p>\n";
			}
			$this->AdminEditLangList();
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	function SuppressedCountries()
	{	$countries = array();
		$sql = "SELECT countries.* FROM countries, pmtoptions_ctry WHERE countries.ccode=pmtoptions_ctry.ccode AND optvalue='{$this->id}' AND optfield='suppress' AND fieldvalue='1'";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[$row["ccode"]] = $row;
			}
		}else echo "<p>", $this->db->Error(), "</p>\n";
		
		return $countries;
	} // end of fn SuppressedCountries
	
	function SuppressedCountriesList($namefield = "longcode")
	{	if ($countries = $this->SuppressedCountries())
		{	$links = array();
			foreach ($countries as $ccode=>$ctry)
			{	$links[] = "<a href='ctryedit.php?ctry={$ccode}'>" . $this->InputSafeString($ctry[$namefield]) . "</a>";
			}
			return implode(", ", $links);
		}
	} // end of fn SuppressedCountriesList
	
} // end of defn AdminPaymentOption
?>