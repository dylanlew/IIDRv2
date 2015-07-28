<?php
class AdminLibCat extends LibCat
{	var $admintitle = "";
	var $langused = array();
	
	function __construct($id = "")
	{	parent::__construct($id);
		$this->GetAdminTitle();
	} // fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	$this->admintitle = $this->details["lcname"];
		}
	} // end of fn GetAdminTitle
	
	function CanDelete()
	{	
		if ($this->id)
		{	
			//check for any sub cats
			$sql = 'SELECT lcid FROM libcats WHERE parentid=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($row = $this->db->FetchArray($result))
				{	return false;
				}
			}
			
			// check for any books set
			$sql = 'SELECT lcid FROM multimediacats WHERE lcid=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($row = $this->db->FetchArray($result))
				{	return false;
				}
			}
			
			return true;
		}
		return false;
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM libcats WHERE lcid=' . (int)$this->id))
			{	$this->RecordAdminAction(array('tablename'=>'libcats', 'tableid'=>$this->id, 'area'=>'library cats', 'action'=>'deleted'));
				return $this->db->AffectedRows();
			}
		}
		return false;
	} // end of fn Delete
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = str_replace(' ', '_', preg_replace("/[^a-z0-9 ]/", "", strtolower($slug)));
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT lcid FROM libcats WHERE lcslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT lcid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['lcid'];
			}
		}
		return false;
	} // end of fn SlugExists
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($lcname = $this->SQLSafe($data['lcname']))
		{	$fields[] = 'lcname="' . $lcname . '"';
			if ($this->id && ($data['lcname'] != $this->details['lcname']))
			{	$admin_actions[] = array('action'=>'Name', 'actionfrom'=>$this->details['lcname'], 'actionto'=>$data['lcname']);
			}
		} else
		{	$fail[] = 'category missing';
		}
	
		if ($lcslug = $this->ValidSlug($this->id ? $data['lcslug'] : $lcname))
		{	$fields[] = 'lcslug="' . $lcslug . '"';
			if ($this->id && ($data['lcslug'] != $this->details['lcslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['lcslug'], 'actionto'=>$data['lcslug']);
			}
		} else
		{	$fail[] = 'slug missing';
		}
		
		$lcorder = (int)$data['lcorder'];
		$fields[] = 'lcorder=' . $lcorder;
		if ($this->id && ($data['lcorder'] != $this->details['lcorder']))
		{	$admin_actions[] = array('action'=>'Order', 'actionfrom'=>$this->details['lcorder'], 'actionto'=>$data['lcorder']);
		}
		
		
		if ($parentid = (int)$data['parentid'])
		{	if (!(($parents = $this->GetPossibleParents()) && $parents[$parentid]))
			{	$fail[] = 'Parent category not found';
				$parentid = (int)$this->details['parentid'];
			}
		}
		
		$fields[] = 'parentid=' . $parentid;
		if ($this->id && ($data["parentid"] != $this->details["parentid"]))
		{	$admin_actions[] = array("action"=>"Parent category", "actionfrom"=>$this->details["parentid"], "actionto"=>$parentid);
		}

		if ($set = implode(", ", $fields))
		{	
			if ($this->id)
			{	$sql = "UPDATE libcats SET $set WHERE lcid={$this->id}";
			} else
			{	$sql = "INSERT INTO libcats SET $set";
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->db->AffectedRows())
					{	if ($this->id)
						{	$record_changes = true;
							$success[] = "Changes saved";
						} else
						{	$this->id = $this->db->InsertID();
							$success[] = "New library category created";
							$this->RecordAdminAction(array("tablename"=>"libcats", "tableid"=>$this->id, "area"=>"library cats", "action"=>"created"));
						}
						$this->Get($this->id);
					}
				}
				
				if ($this->id)
				{	$this->Get($this->id);
					$this->GetAdminTitle();
				}
			
				if ($record_changes)
				{	$base_parameters = array("tablename"=>"libcats", "tableid"=>$this->id, "area"=>"library cats");
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
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id . '&lang=' . $this->language);
		$form->AddTextInput('Category name', 'lcname', $this->InputSafeString($data['lcname']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Category slug', 'lcslug', $this->InputSafeString($data['lcslug']), 'long', 255, 1);
		}
		if ($parents = $this->GetPossibleParents())
		{	$form->AddSelectWithGroups('Parent category', 'parentid', $data['parentid'], '', $parents, 1, 0, '');
		}
		$form->AddCheckBox('Live', 'live', '1', $data['live']);
		$form->AddTextInput('Display order', 'lcorder', (int)$data['lcorder'], 'short number', 4, 1);
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Category', 'submit');
		if ($histlink = $this->DisplayHistoryLink('libcats', $this->id))
		{	echo '<p>', $histlink, '</p>';
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this category</a></p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	function GetPossibleParents($parentid = 0, $level = 0)
	{
		$parents = array();
		$sql = 'SELECT libcats.* FROM libcats WHERE libcats.parentid=' . $parentid;
		if ($this->id)
		{	$sql .= ' AND NOT libcats.lcid=' . $this->id;
		}
		$sql .= ' ORDER BY libcats.lcorder';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$this->subcats[$row['lcid']])
				{	$parents[$row['lcid']] = str_repeat('- ', $level) . $this->InputSafeString($row['lcname']);
					if ($subcats = $this->GetPossibleParents($row['lcid'], $level + 1))
					{	foreach ($subcats as $subid=>$subcat)
						{	$parents[$subid] = $subcat;
						}
					}
				}
			}
		}
		
		return $parents;
		
	} // end of fn GetPossibleParents
	
} // end of defn AdminLibCat
?>