<?php
class AdminATICat extends ATICat
{		
	public function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($catname = $this->SQLSafe($data['catname']))
		{	$fields[] = 'catname="' . $catname . '"';
			if ($this->id && ($data['catname'] != $this->details['catname']))
			{	$admin_actions[] = array('action'=>'Category name', 'actionfrom'=>$this->details['catname'], 'actionto'=>$data['catname']);
			}
		} else
		{	$fail[] = 'Name missing';
		}

		$listorder = (int)$data['listorder'];
		$fields[] = 'listorder=' . $listorder;
		if ($this->id && ($listorder != $this->details['listorder']))
		{	$admin_actions[] = array('action'=>'List order', 'actionfrom'=>$this->details['listorder'], 'actionto'=>$listorder);
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE atiqcats SET $set WHERE catid={$this->id}";
			} else
			{	$sql = "INSERT INTO atiqcats SET $set";
			} 
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New FAQ Category created";
						$this->RecordAdminAction(array("tablename"=>"atiqcats", "tableid"=>$this->id, "area"=>"ask the imam categories", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array("tablename"=>"atiqcats", "tableid"=>$this->id, "area"=>"ask the imam categories");
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Save
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextInput('Category name', 'catname', $this->InputSafeString($data['catname']), 'long', 255);
		$form->AddTextInput('Display order', 'listorder', (int)$data['listorder'], 'short num', 6);
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create Category', 'submit');
		
		if ($this->CanDelete())
		{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
					$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
					"delete this category</a></p>\n";
		}
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	public function CanDelete()
	{	return $this->id && !$this->GetQuestions();
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM atiqcats WHERE catid=' . $this->id))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array('tablename'=>'atiqcats', 'tableid'=>$this->id, 'area'=>'ask the imam categories', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	public function GetQuestions()
	{	return parent::GetQuestions(false);
	} // end of fn GetQuestions
	
} // end of class AdminATICat
?>