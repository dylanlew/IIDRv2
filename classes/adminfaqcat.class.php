<?php
class AdminFAQCat extends FAQCat
{		
	public function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT catid FROM faqcats WHERE catslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT catid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['catid'];
			}
		}
		return false;
	} // end of fn SlugExists
	
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
	
		// create slug
		if ($catslug = $this->ValidSlug(($this->id && $data['catslug']) ? $data['catslug'] : $catname))
		{	$fields[] = 'catslug="' . $catslug . '"';
			if ($this->id && ($catslug != $this->details['catslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['catslug'], 'actionto'=>$data['catslug']);
			}
		} else
		{	if ($pagetitle)
			{	$fail[] = 'slug missing';
			}
		}

		$listorder = (int)$data['listorder'];
		$fields[] = 'listorder=' . $listorder;
		if ($this->id && ($listorder != $this->details['listorder']))
		{	$admin_actions[] = array('action'=>'List order', 'actionfrom'=>$this->details['listorder'], 'actionto'=>$listorder);
		}
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE faqcats SET ' . $set . ' WHERE catid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO faqcats SET ' . $set;
			} 
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New FAQ Category created';
						$this->RecordAdminAction(array('tablename'=>'faqcats', 'tableid'=>$this->id, 'area'=>'faq categories', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'faqcats', 'tableid'=>$this->id, 'area'=>'faq categories');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));

	} // end of fn Save
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextInput('Category name', 'catname', $this->InputSafeString($data['catname']), 'long', 255);
		if ($this->id)
		{	$form->AddTextInput('Catgeory slug (in page url)', 'catslug', $this->InputSafeString($data['catslug']), 'long', 255, 1);
		}
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
	{	return $this->id && !$this->GetFAQ();
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM faqcats WHERE catid=' . $this->id))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array('tablename'=>'faqcats', 'tableid'=>$this->id, 'area'=>'faq categories', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	public function GetFAQ()
	{	return parent::GetFAQ(false);
	} // end of fn GetFAQ
	
} // end of class AdminFAQCat
?>