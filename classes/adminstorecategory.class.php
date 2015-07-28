<?php
class AdminStoreCategory extends StoreCategory
{	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function CanDelete()
	{	return $this->id && !$this->GetProducts();
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	$sql = 'DELETE FROM storecategories WHERE cid=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		$this->AddBackLinkHiddenField($form);
		$form->AddTextInput('Category name', 'ctitle', $this->InputSafeString($data['ctitle']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Slug (for url)', 'cslug', $this->InputSafeString($data['cslug']), 'long', 255, 1);
		}
		
		$form->AddCheckBox('Current (shows on site)', 'live', '1', $data['live']);
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Category', 'submit');
		if ($this->id)
		{	
			if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this category</a></p>';
			}
			
			if ($histlink = $this->DisplayHistoryLink('storecategories', $this->id))
			{	echo '<p>', $histlink, '</p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm

	public function SlugExists($slug = '')
	{	$sql = 'SELECT cid FROM storecategories WHERE cslug="' . $this->SQLSafe($slug) . '"';
		if ($this->id)
		{	$sql .= ' AND NOT cid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return true;
			}
		}
		return false;
	} // end of fn SlugExists
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($ctitle = $this->SQLSafe($data['ctitle']))
		{	$fields[] = 'ctitle="' . $ctitle . '"';
			if ($this->id && ($data['ctitle'] != $this->details['ctitle']))
			{	$admin_actions[] = array('action'=>'Name', 'actionfrom'=>$this->details['ctitle'], 'actionto'=>$data['ctitle']);
			}
		} else
		{	$fail[] = 'name missing';
		}
		
		if ($this->id)
		{	$cslug = $this->TextToSlug($data['cslug']);
		} else
		{	if ($ctitle)
			{	$cslug = $this->TextToSlug($ctitle);
			}
		}
		
		if ($cslug)
		{	$suffix = '';
			while ($this->SlugExists($cslug . $suffix))
			{	$suffix++;
			}
			$cslug .= $suffix;
			
			$fields[] = 'cslug="' . $cslug . '"';
			if ($this->id && ($cslug != $this->details['cslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['cslug'], 'actionto'=>$cslug);
			}
		} else
		{	if ($this->id || $ctitle)
			{	$fail[] = 'slug missing';
			}
		}
		
		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE storecategories SET ' . $set . ' WHERE cid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO storecategories SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New category created';
						$this->RecordAdminAction(array('tablename'=>'storecategories', 'tableid'=>$this->id, 'area'=>'store categories', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'storecategories', 'tableid'=>$this->id, 'area'=>'store categories');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
				
		//		$this->UpdateStatusFromBookings();
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
} // end of class AdminStoreCategory
?>