<?php
class AdminCity extends City
{		
	function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	function CanDelete()
	{	
		
		if ($this->id && !$this->GetLocations() && $this->CanAdminUserDelete())
		{
			// courses
			$sql = "SELECT cid FROM courses WHERE city=" . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->NumRows($result))
				{	return false;
				}
			}
			
			return true;
		}
		return false;
		
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	$sql = "DELETE FROM cities WHERE cityid=" . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array("tablename"=>"cities", "tableid"=>$this->id, "area"=>"cities", "action"=>"deleted", "actiontype"=>"deleted", "deleteparentid"=>$this->details["country"], "deleteparenttable"=>"countries"));
					$this->Reset();
					return true;
				}
			}
			
		}
	} // end of fn Delete

	function Save()
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($cityname = $this->SQLSafe($_POST["cityname"]))
		{	$fields[] = "cityname='$cityname'";
			if ($this->id && ($data["cityname"] != $this->details["cityname"]))
			{	$admin_actions[] = array("action"=>"Category name", "actionfrom"=>$this->details["cityname"], "actionto"=>$data["cityname"]);
			}
		} else
		{	$fail[] = "city name missing";
		}
		
		if ($country = $this->SQLSafe($_POST["country"]))
		{	$ctrylist = $this->CountriesList();
			if ($ctrylist[$country])
			{	$fields[] = "country='$country'";
				if ($this->id && ($country != $this->details["country"]))
				{	$admin_actions[] = array("action"=>"Country", "actionfrom"=>$this->details["country"], "actionto"=>$country, "actiontype"=>"link", "linkmask"=>"ctryedit.php?ctry={linkid}");
				}
			} else
			{	$fail[] = "country not valid";
			}
		} else
		{	$fail[] = "country name missing";
		}
		
		if ($_POST["emailfrom"])
		{	if ($this->ValidEMail($_POST["emailfrom"]))
			{	$fields[] = "emailfrom='{$_POST["emailfrom"]}'";
				if ($this->id && ($_POST["emailfrom"] != $this->details["emailfrom"]))
				{	$admin_actions[] = array("action"=>"Email sent from", "actionfrom"=>$this->details["emailfrom"], "actionto"=>$_POST["emailfrom"]);
				}
			} else
			{	$fail[] = "\"\From\" email address is invalid";
			}
		} else
		{	$fields[] = "emailfrom=''";
			if ($this->id && $this->details["emailfrom"])
			{	$admin_actions[] = array("action"=>"Email sent from", "actionfrom"=>$this->details["emailfrom"]);
			}
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE cities SET $set WHERE cityid={$this->id}";
			} else
			{	$sql = "INSERT INTO cities SET $set";
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array("tablename"=>"cities", "tableid"=>$this->id, "area"=>"cities");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New city created";
						$this->RecordAdminAction(array("tablename"=>"cities", "tableid"=>$this->id, "area"=>"cities", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if ($this->id)
					{	$fail[] = "No changes made";
					} else
					{	$fail[] = "Insert failed";
					}
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id, "crewcvForm");
		$form->AddTextInput("City name", "cityname", $this->InputSafeString($this->details["cityname"]), "", 100);
		$form->AddSelect("Country", "country", $this->id ? $this->details["country"] : $_GET["ctry"], "", $this->CountriesList(), true, true, "onchange='GetFromEmail(\"country\", \"country\")';");
		$form->AddTextInput("\"From\" email address", "emailfrom", $this->InputSafeString($this->details["emailfrom"]), "long", 255);
		if ($this->details["country"])
		{	$country = new Country($this->details["country"]);
			$defemail = $country->EmailFrom();
		} else
		{	$defemail = $this->GetParameter("emailfrom");
		}
		$form->AddRawText("<label>Default \"From\" if not defined here</label><label id='ad_ak_emailfrom' class='content_label'>$defemail</label><br />\n");
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New City", "submit");
		if ($histlink = $this->DisplayHistoryLink("cities", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->CanDelete())
		{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
					$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
					"delete this city</a></p>\n";
		}
		$form->Output();
	} // end of fn InputForm
	
	function CountriesList()
	{	$countries = array();
		$adminuser = new AdminUser((int)$_SESSION[SITE_NAME]["auserid"]);
		
		$sql = "SELECT countries.ccode, countries.shortname, IF(toplist > 0, 0, 1) AS istoplist FROM countries ORDER BY istoplist, toplist, shortname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[$row["ccode"]] = $row["shortname"];
			}
		}
		return $countries;
	} // end of fn CountriesList
	
	function LocationList()
	{	if ($this->id)
		{	echo "<table><tr class='newlink'><th colspan='2'><a href='locationedit.php?city=", $this->id, "'>add new location</a></th></tr>\n<tr><th>Location name</th><th>Actions</th></tr>\n";
			if ($locations = $this->GetLocations())
			{	foreach ($locations as $location)
				{	
					echo "<tr>\n<td>", $this->InputSafeString($location->details["loctitle"]), "</td>\n<td><a href='locationedit.php?id=", $location->id, "'>edit</a>";
					if ($histlink = $this->DisplayHistoryLink("locations", $location->id))
					{	echo "&nbsp;|&nbsp;", $histlink;
					}
					if ($location->CanDelete())
					{	echo "&nbsp;|&nbsp;<a href='locationedit.php?id=", $location->id, "&delete=1'>delete</a>";
					}
					echo "</td>\n</tr>\n";
				}
			}
			echo "</table>\n";
		}
	} // end of fn LocationList
	
	function AssignLocation($location = array())
	{	return new AdminLocation($location);
	} // end of fn AssignLocation

	function LocationDropdownList()
	{	$loc_list = array();
		
		if ($locations = $this->GetLocations())
		{	foreach ($locations as $location)
			{	$loc_list[$location->id] = $location->details["loctitle"];
			}
		}
		//print_r($this);
		return $loc_list;
	} // end of fn LocationDropdownList
	
} // end of defn AdminCity
?>