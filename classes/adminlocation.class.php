<?php
class AdminLocation extends Location
{	
	function __construct($id = 0)
	{	parent::__construct($id);
	} // fn __construct
	
	function CanDelete()
	{	if ($this->id && $this->CanAdminUserDelete())
		{
			// courses
			$sql = "SELECT cid FROM courses WHERE location=" . (int)$this->id;
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
		{	if ($result = $this->db->Query("DELETE FROM locations WHERE locid=$this->id"))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array("tablename"=>"locations", "tableid"=>$this->id, "area"=>"locations", "action"=>"deleted", "actiontype"=>"deleted", "deleteparentid"=>$this->details["city"], "deleteparenttable"=>"cities"));
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete

	function Save($data= array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($loctitle = $this->SQLSafe($data["loctitle"]))
		{	$fields[] = "loctitle='$loctitle'";
			if ($this->id && ($data["loctitle"] != $this->details["loctitle"]))
			{	$admin_actions[] = array("action"=>"Title", "actionfrom"=>$this->details["loctitle"], "actionto"=>$data["loctitle"]);
			}
		} else
		{	$fail[] = "title missing";
		}
		
		$address = $this->SQLSafe($data["address"]);
		$fields[] = "address='$address'";
		if ($this->id && ($data["address"] != $this->details["address"]))
		{	$admin_actions[] = array("action"=>"Address", "actionfrom"=>$this->details["address"], "actionto"=>$data["address"]);
		}
		
		$directions = $this->SQLSafe($data["directions"]);
		$fields[] = "directions='$directions'";
		if ($this->id && ($data["directions"] != $this->details["directions"]))
		{	$admin_actions[] = array("action"=>"Directions", "actionfrom"=>$this->details["directions"], "actionto"=>$data["directions"]);
		}
		
		if (is_numeric($data["googlelat"]))
		{	$googlelat = round($data["googlelat"], 6);
		} else
		{	$googlelat = "";
		}
		$fields[] = "googlelat='" . $googlelat . "'";
		if ($this->id && ($googlelat != $this->details["googlelat"]))
		{	$admin_actions[] = array("action"=>"Latitude", "actionfrom"=>$this->details["googlelat"], "actionto"=>$googlelat);
		}
		
		if (is_numeric($data["googlelong"]))
		{	$googlelong = round($data["googlelong"], 6);
		} else
		{	$googlelong = "";
		}
		$fields[] = "googlelong='" . $googlelong . "'";
		if ($this->id && ($googlelong != $this->details["googlelong"]))
		{	$admin_actions[] = array("action"=>"Longitude", "actionfrom"=>$this->details["googlelong"], "actionto"=>$googlelong);
		}
		
		// city
		if ($city = (int)$data["city"])
		{	if ($cities = $this->CityList())
			{	if ($cities[$city])
				{	$fields[] = "city=$city";
					if ($this->id && ($city != $this->details["city"]))
					{	$admin_actions[] = array("action"=>"City", "actionfrom"=>$this->details["city"], "actionto"=>$city, "actiontype"=>"link", "linkmask"=>"cityedit.php?id={linkid}");
					}
				} else
				{	$fail[] = "city invalid";
				}
			}
		} else
		{	$fail[] = "city missing";
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE locations SET $set WHERE locid={$this->id}";
			} else
			{	$sql = "INSERT INTO locations SET $set";
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array("tablename"=>"locations", "tableid"=>$this->id, "area"=>"locations");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New location created";
						$this->RecordAdminAction(array("tablename"=>"locations", "tableid"=>$this->id, "area"=>"locations", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&city=" . $_GET["city"], "course_edit");
		$form->AddTextInput("Location title", "loctitle", $this->InputSafeString($data["loctitle"]), "long", 255, 1);
		$form->AddSelect("City", "city", $data["city"] ? $data["city"] : $_GET["city"], "", $this->CityList(), true, true);
		$form->AddTextArea("Address", "address", $this->InputSafeString($data["address"]), "desc_text");
		$form->AddTextArea("Access &amp; facilities", "directions", $this->InputSafeString($data["directions"]), "desc_text");
		$form->AddTextInput("Google latitude", "googlelat", $this->InputSafeString($data["googlelat"]), "", 20, 0, "onchange='akGoogleMapRefresh();'");
		$form->AddTextInput("Google longitude<br /><a onclick='akGoogleMapLookUp();'>get these from address</a>", "googlelong", $this->InputSafeString($data["googlelong"]), "", 20, 0, "onchange='akGoogleMapRefresh();'");
		$form->AddRawText("<p><label>Map (<a onclick='akGoogleMapRefresh();'>refresh</a>)</label><div id='gmap' style='width: 500px; height: 420px; position:relative; display: none;'>&nbsp;</div><div class='clear'></div></p>");
		if ($data["googlelat"] || $data["googlelong"])
		{	$form->AddRawText($this->GoogleMap($data["googlelat"], $data["googlelong"], $data["loctitle"]));
		}
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Location", "submit");
		
		if ($histlink = $this->DisplayHistoryLink("locations", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->CanDelete())
		{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
					$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
					"delete this location</a></p>\n";
		}
		$form->Output();
	} // end of fn InputForm
	
	function GoogleMap($lat = 0, $long = 0, $location_title = "course location")
	{	ob_start();
		echo "<script type='text/javascript'>
			window.onload=function(){
			gminitialize(", $lat, ", ", $long, ", \"", $this->InputSafeString($location_title), "\");
			};
			document.getElementById('gmap').style.display='block';
			</script>\n";
		return ob_get_clean();
	} // end of fn GoogleMap
	
	function CityList()
	{	$cities = array();
		$adminuser = new AdminUser((int)$_SESSION[SITE_NAME]["auserid"]);
		$sql = "SELECT cities.*,countries.shortname FROM cities, countries WHERE cities.country=countries.ccode ORDER BY countries.shortname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($adminuser->CanAccessCity($row["cityid"]))
				{	$cities[$row["cityid"]] = $row["cityname"] . " - " . $row["shortname"];
				}
			}
		}
		return $cities;
	} // end of fn CityList
	
} // end of defn AdminLocation
?>