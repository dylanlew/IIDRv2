<?php
class Currency extends Base
{	var $code = 0;
	var $details = array();
	var $history = array();	

	function __construct($code = "")
	{	parent::__construct();
		$this->Get($code);
	} //  end of fn __construct
	
	function Get($code = "")
	{	$this->ReSet();
		
		if (is_array($code))
		{	$this->code = $code["curcode"];
			$this->details = $code;
			$this->GetHistory();
		} else
		{	if ($result = $this->db->Query("SELECT * FROM currencies WHERE curcode='$code'"))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->code = $row["curcode"];
					$this->details = $row;
					$this->GetHistory();
				}
			}
		}
		
	} // end of fn Get
	
	function GetHistory()
	{	$this->history = array();
		if ($result = $this->db->Query("SELECT * FROM currency_rates WHERE curcode='$this->code' ORDER BY `when` DESC"))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->history[] = $row;
			}
		}
	} // end of fn GetHistory
	
	function ReSet()
	{	
		$this->code = 0;
		$this->details = array();
		$this->history = array();
	} // end of fn ReSet
	
	function Save($data = array())
	{	
		$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if (!$this->code)
		{	if ($curcode = strtoupper($this->SQLSafe($data["curcode"])))
			{	$fields[] = "curcode='$curcode'";
			} else
			{	$fail[] = "currency code cannot be empty";
			}
		}
		
		if ($curname = $this->SQLSafe($data["curname"]))
		{	$fields[] = "curname='$curname'";
			if ($this->code && ($data["curname"] != $this->details["curname"]))
			{	$admin_actions[] = array("action"=>"Name", "actionfrom"=>$this->details["curname"], "actionto"=>$data["curname"]);
			}
		} else
		{	$fail[] = "currency name cannot be empty";
		}
		
		if ($cursymbol = $this->SQLSafe($data["cursymbol"]))
		{	$fields[] = "cursymbol='$cursymbol'";
			if ($this->code && ($data["cursymbol"] != $this->details["cursymbol"]))
			{	$admin_actions[] = array("action"=>"Symbol", "actionfrom"=>$this->details["cursymbol"], "actionto"=>$data["cursymbol"]);
			}
		} else
		{	$fail[] = "symbol cannot be empty";
		}
		
		$curorder = (int)$data["curorder"];
		$fields[] = "curorder=" . $curorder;
		if ($this->code && ($curorder != $this->details["curorder"]))
		{	$admin_actions[] = array("action"=>"List order", "actionfrom"=>$this->details["curorder"], "actionto"=>$curorder);
		}
		
		if (isset($data["convertrate"]))
		{	if ($convertrate = (float)$data["convertrate"])
			{	$fields[] = "convertrate=" . (float)$data["convertrate"];
				if ($convertrate != $this->details["convertrate"])
				{	$rate_changed = true;
					if ($this->id )
					{	$admin_actions[] = array("action"=>"Conversion rate", "actionfrom"=>$this->details["convertrate"], "actionto"=>$convertrate);
					}
				}
			} else
			{	$fail[] = "zero conversion rate not allowed";
			}
		}
		
		if ($this->code || !$fail)
		{	
			$set = implode(", ", $fields);
			if ($this->code)
			{	$sql = "UPDATE currencies SET $set WHERE curcode='$this->code'";
			} else
			{	$sql = "INSERT INTO currencies SET $set";
			}

			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->code)
					{	$base_parameters = array("tablename"=>"currencies", "tableid"=>$this->code, "area"=>"currencies");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = "changes saved";
						$this->Get($this->code);
					} else
					{	$this->Get($curcode);
						$success[] = "new currency added";
						$this->RecordAdminAction(array("tablename"=>"currencies", "tableid"=>$this->code, "area"=>"currencies", "action"=>"created"));
					}
					if ($rate_changed)
					{	// now record manual change
						$this->RecordNewRate($this->details["convertrate"], true);
					}
				}
			}
			
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function GoogleUpdateRate()
	{	if ($this->CanUpdate())
		{	if ($rate = GoogleCurrencyConverter::convert(1,'GBP',$this->code));
			{	$sql = "UPDATE currencies SET convertrate=$rate WHERE curcode='{$this->code}'";
				if ($result = $this->db->Query($sql))
				{	$this->RecordNewRate($rate);
					$this->details["convertrate"] = $rate;
				}
			}
		}
	} // end of fn GoogleUpdateRate
	
	function RecordNewRate($rate, $manual = false)
	{	$now = $this->datefn->SQLDateTime();
		$recordsql = "INSERT INTO currency_rates SET curcode='{$this->code}', convertrate=$rate, `when`='$now'";
		if ($manual)
		{	$recordsql .= ", manual_change=1";
		}
		$this->db->Query($recordsql);
		$this->GetHistory();
	} // end of fn RecordNewRate
	
	function HistoryTable()
	{	if ($this->history || $this->CanUpdate())
		{
			echo "<table id='history'>\n<tr><th class='num'>Rate</th><th>Applied from";
			if ($this->CanUpdate())
			{	echo "<br /><a href='", $_SERVER["SCRIPT_NAME"], "?code=", $this->code, "&update=1'>update now</a>";
			}
			echo "</th></tr>\n";
			foreach ($this->history as $rate)
			{	echo "<tr>\n<td class='num'>", number_format($rate["convertrate"], 4),"</td>\n<td>", 
						date("d/m/y @H:i", strtotime($rate["when"])), 
						$rate["manual_change"] ? " (manual)" : "", "</td>\n</tr>\n";
			}
			echo "</table>";
		}
	} // end of fn HistoryTable
	
	function CountriesList()
	{	if ($countries = $this->GetCountries())
		{	echo "<p>Used in ...<ul>\n";
			foreach ($countries as $ctry)
			{	echo "<li><a href='ctryedit.php?ctry=", $ctry->code, "'>", $ctry->details["shortname"], "</a></li>\n";
			}
			echo "</ul>\n</p>\n";
		} else
		{	echo "<p>This currency is not used for any countries yet - <a href='countries.php'>add from list<a></p>\n";
		}
	} // end of fn CountriesList
	
	function GetCountries()
	{	$countries = array();
		if ($result = $this->db->Query("SELECT * FROM countries WHERE currency='{$this->details["curcode"]}'"))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[] = new AdminCountry($row);
			}
		}
		
		return $countries;
	} // end of fn GetCountries
	
	function CanUpdate()
	{	return $this->details["rateupdateable"];
	} // end of fn CanUpdate
	
	function InputForm()
	{	 
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?code=" . $this->code);
		if ($this->code)
		{	$form->AddLabelLine("<h4>{$this->code} ({$this->details["cursymbol"]})</h4>\n");
		} else
		{	$form->AddTextInput("3 letter code", "curcode", $this->InputSafeString(strtoupper($_POST["$this->code"])));
		}
		$form->AddTextInput("Symbol", "cursymbol", htmlentities($this->details["cursymbol"]));
		$form->AddTextInput("Name", "curname", $this->InputSafeString($this->details["curname"]));
		$form->AddTextInput("Order listed", "curorder", (int)$this->details["curorder"]);
	//	$form->AddTextInput("Conversion Rate", "convertrate", round($this->details["convertrate"], 4));
		
		$form->AddSubmitButton("", $this->code ? "Save Changes" : "Create Currency", "submit");
		
		if ($histlink = $this->DisplayHistoryLink("currencies", $this->code))
		{	echo "<p>", $histlink, "</p>";
		}
		$form->Output();
		echo "<br />\n";
	} // end of fn InputForm
	
} // end of defn Currency
?>