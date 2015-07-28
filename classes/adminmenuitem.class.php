<?php
class AdminMenuItem extends Base
{	var $user = 0;
	var $id = 0;
	var $details = array();
	var $submenu = false;

	function __construct(AdminUser $user, $id = 0, $showall = false) // constructor
	{	parent::__construct();
		$this->user = $user;
		$this->Get($id, $showall);
	} // end of fn __construct
	
	function Reset()
	{	$this->details = array();
		$this->submenu = false;
		$this->id = 0;
	} // end of fn Reset
	
	function Get($id = "", $showall = false)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			if ($this->id = $id["menuid"])
			{	$this->submenu = new AdminMenu($this->user, $this->id, $showall);
				return true;
			}
		} else
		{	if ($id = (int)$id)
			{	
				if ($result = $this->db->Query("SELECT * FROM adminmenus WHERE menuid=$id"))
				{	if ($row = $this->db->FetchArray($result))
					{	return $this->Get($row, $showall);
					}
				}
			}
		}
		return false;
	} // end of fn Get
	
	function InputForm($parentid = 0)
	{	
		if ($this->id)
		{	$data = $this->details;
		} else
		{	if (!$data = $_POST)
			{	$data = array("parentid"=>(int)$parentid);
			}
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=$this->id", "");
		$form->AddTextInput("Text for Menu", "menutext", $this->InputSafeString($data["menutext"]), "", 255);
		$form->AddTextInput("Link (if any)", "menulink", $this->InputSafeString($data["menulink"]), "", 255);
		$form->AddTextInput("Order in menu", "disporder", (int)$data["disporder"], "", 4);
		$form->AddCheckBox("Open in new tab", "newtab", "1", $data["newtab"]);
		$form->AddSelect("Access area", "menuaccess", $data["menuaccess"], "", $this->GetPossibleAccess(), 1, 0, "", "-- all users --");
		$form->AddSelect("Parent menu", "parentid", $data["parentid"], "", $this->GetPossibleParents(), 1, 0, "", "-- top level --");
		$form->AddTextInput("Menu area", "menuarea", $this->InputSafeString($data["menuarea"]), "", 255);
		$form->AddSubmitButton("&nbsp;", $this->id ? "Save Changes" : "Create New Item", "submit");
		$form->Output();
	} // end of fn InputForm
	
	function Save($data = array())
	{	$fail = array();
		$success = "";
		$fields = "";
		
		if ($menutext = $this->SQLSafe($data["menutext"]))
		{	$fields[] = "menutext='$menutext'";
		} else
		{	$fail[] = "Menu text missing";
		}
		
		$menulink = $this->SQLSafe($data["menulink"]);
		$fields[] = "menulink='$menulink'";
		
		$menuarea = $this->SQLSafe($data["menuarea"]);
		$fields[] = "menuarea='$menuarea'";
		
		$fields[] = "disporder=" . (int)$data["disporder"];
		$fields[] = "parentid=" . (int)$data["parentid"];
		$fields[] = "newtab=" . ($data["newtab"] ? "1" : "0");
		
		if ($menuaccess = $data["menuaccess"])
		{	if (isset($this->user->accessAreas[$menuaccess]))
			{	$fields[] = "menuaccess='$menuaccess'";
			} else
			{	$fail[] = "Invalid access area: \"$menuaccess\"";
			}
		} else
		{	$fields[] = "menuaccess=''";
		}

		if (!$fail || $this->id)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE adminmenus SET $set WHERE menuid=$this->id";
			} else
			{	$sql = "INSERT INTO adminmenus SET $set";
			}
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$success = "Changes saved";
						$this->Get($this->id, true);
					} else
					{	$success = "New menum item created";
						$this->Get($this->db->InsertID(), true);
					}
				}
			} else $fail[] = $this->db->Error();
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>$success);
		
	} // end of fn Save
	
	function GetPossibleParents($parentid = 0)
	{	$parents = array();
		$sql = "SELECT * FROM adminmenus WHERE NOT menuid=" . $this->id . " AND parentid=" . (int)$parentid . " ORDER BY disporder";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!is_a($this->submenu, "AdminMenu") || !isset($this->submenu->items[$row["menuid"]]))
				{	$parents[$row["menuid"]] = $row["menutext"];
					if ($submenu = $this->GetPossibleParents($row["menuid"]))
					{	foreach ($submenu as $subid=>$subitem)
						{	$parents[$subid] = '&nbsp;-&nbsp;' . $subitem;
						}
					}
				}
			}
		}
		return $parents;
	} // end of fn GetPossibleParents
	
	function GetPossibleAccess()
	{	$access = array();
		foreach ($this->user->accessAreas as $area=>$flag)
		{	$access[$area] = $area;
		}
		return $access;
	} // end of fn GetPossibleAccess
	
	public function CanDelete()
	{	return $this->id && count($this->submenu->menuitems) == 0;
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	$sql = 'DELETE FROM adminmenus WHERE menuid=' .$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	return true;
				}
			}
		}
	} // end of fn Delete
	
} // end of defn AdminMenuItem
?>