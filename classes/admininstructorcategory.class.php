<?php
class AdminInstructorCategory extends InstructorCategory
{	
	function __construct($id = 0)
	{	parent::__construct($id);	
	} // fn __construct
	
	public function GetPeople($liveonly = false)
	{	return parent::GetPeople($liveonly);
	} // end of fn GetPeople
	
	function CanDelete()
	{	return $this->id && !$this->subcats && !$this->GetPeople();
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM instructorcats WHERE icid=' . $this->id))
			{	if ($this->db->AffectedRows())
				{	
					$this->RecordAdminAction(array('tablename'=>'instructorcats', 'tableid'=>$this->id, 'area'=>'people categories', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT cid FROM instructorcats WHERE catslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT icid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['icid'];
			}
		}
		return false;
	} // end of fn SlugExists

	function Save($data = array(), $bgfile = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($catname = $this->SQLSafe($data['catname']))
		{	$fields[] = 'catname="' . $catname . '"';
			if ($this->id && ($data['catname'] != $this->details['catname']))
			{	$admin_actions[] = array('action'=>'Name', 'actionfrom'=>$this->details['catname'], 'actionto'=>$data['catname']);
			}
		} else
		{	$fail[] = 'name missing';
		}
	
		// create slug
		if ($catslug = $this->ValidSlug(($this->id && $data['catslug']) ? $data['catslug'] : $catname))
		{	$fields[] = 'catslug="' . $catslug . '"';
			if ($this->id && ($catslug != $this->details['catslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['catslug'], 'actionto'=>$data['catslug']);
			}
		} else
		{	if ($catname)
			{	$fail[] = 'slug missing';
			}
		}
		
		if ($parentcat = (int)$data['parentcat'])
		{	if ($parents = $this->GetPossibleParents())
			{	if ($parents[$parentcat])
				{	$fields[] = 'parentcat=' . $parentcat;
					if ($this->id && ($parentcat != $this->details['parentcat']))
					{	$admin_actions[] = array('action'=>'Parent category', 'actionfrom'=>$this->details['parentcat'], 'actionto'=>$parentcat, 'actiontype'=>'link', 'linkmask'=>'instructorcatedit.php?id={linkid}');
					}
				} else
				{	$fail[] = 'parent not found';
				}
			}
		} else
		{	$fields[] = 'parentcat=0';
			if ($this->id && $this->details['parentcat'])
			{	$admin_actions[] = array('action'=>'Parent category', 'actionfrom'=>$this->details['parentcat'], 'actionto'=>0, 'actiontype'=>'link', 'linkmask'=>'instructorcatedit.php?id={linkid}');
			}
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE instructorcats SET ' . $set . ' WHERE icid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO instructorcats SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array('tablename'=>'instructorcats', 'tableid'=>$this->id, 'area'=>'people categories');
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = 'Changes saved';
					} else
					{	if ($this->id = $this->db->InsertID())
						{	$success[] = 'New category created';
							$this->RecordAdminAction(array('tablename'=>'instructorcats', 'tableid'=>$this->id, 'area'=>'people categories', 'action'=>'created'));
						}
					}
					$this->Get($this->id);
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
			}
			
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		$form->AddTextInput('Catgeory name', 'catname', $this->InputSafeString($data['catname']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Catgeory slug (in page url)', 'catslug', $this->InputSafeString($data['catslug']), 'long', 255, 1);
		}
		if ($parents = $this->GetPossibleParents())
		{	$form->AddSelectWithGroups('Parent category', 'parentcat', $data['parentcat'], '', $parents, 1, 0, '');
		}
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Category', 'submit');
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this category</a></p>';
			}
			
			if ($histlink = $this->DisplayHistoryLink('instructorcats', $this->id))
			{	echo '<p>', $histlink, '</p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	public function GetPossibleParents($parentid = 0, $prefix = '')
	{	$parents = array();
		$sql = 'SELECT * FROM instructorcats WHERE parentcat=' . (int)$parentid;
		if ($this->id)
		{	$sql .= ' AND NOT icid=' . $this->id;
		}
		$sql .= ' ORDER BY catname';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$this->subcats[$row['icid']])
				{	$parents[$row['icid']] = $prefix . $this->InputSafeString($row['catname']);
					if ($children = $this->GetPossibleParents($row['icid'], '-&nbsp;' . $prefix))
					{	foreach ($children as $pid=>$ptitle)
						{	$parents[$pid] = $ptitle;
						}
					}
				}
			}
		}
		return $parents;
	} // end of fn GetPossibleParents
	
} // end of class AdminInstructorCategory
?>