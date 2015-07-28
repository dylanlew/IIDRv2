<?php
class AdminFAQ extends FAQ
{		
	public function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($question = $this->SQLSafe($data['question']))
		{	$fields[] = 'question="' . $question . '"';
			if ($this->id && ($data['question'] != $this->details['question']))
			{	$admin_actions[] = array('action'=>'Question', 'actionfrom'=>$this->details['question'], 'actionto'=>$data['question']);
			}
		} else
		{	$fail[] = 'Question missing';
		}
		
		if ($answer = $this->SQLSafe($data['answer']))
		{	$fields[] = 'answer="' . $answer . '"';
			if ($this->id && ($data['answer'] != $this->details['answer']))
			{	$admin_actions[] = array('action'=>'Answer', 'actionfrom'=>$this->details['answer'], 'actionto'=>$data['answer']);
			}
		} else
		{	$fail[] = 'Answer missing';
		}

		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}

		$listorder = (int)$data['listorder'];
		$fields[] = 'listorder=' . $listorder;
		if ($this->id && ($listorder != $this->details['listorder']))
		{	$admin_actions[] = array('action'=>'List order', 'actionfrom'=>$this->details['listorder'], 'actionto'=>$listorder);
		}
		
		if (!$this->id)
		{	$fields[] = 'created="' . $this->datefn->SQLDateTime() . '"';
			if (isset($data['askid']))
			{	if (($askimam = new AskTheImam($data['askid'])) && $askimam->id)
				{	if ($askimam->details['faqid'])
					{	$fail[] = 'This "Ask the Imam" record already has an FAQ';
					} else
					{	$fields[] = 'askid=' . $askimam->id;
					}
				} else
				{	$fail[] = '"Ask the Imam" record not found';
				}
			}
		}
		
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE faq SET ' . $set . ' WHERE faqid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO faq SET ' . $set;
			} 
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New FAQ created';
						$this->RecordAdminAction(array('tablename'=>'faq', 'tableid'=>$this->id, 'area'=>'faq', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'faq', 'tableid'=>$this->id, 'area'=>'faq');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		
			if ($this->id)
			{	
				// save categories
				$delcat = 0;
				foreach ($this->cats as $catid=>$catrow)
				{	if (!$data['catid'][$catid])
					{	// then delete
						$sql = 'DELETE FROM faqtocats WHERE faqid=' . $this->id . ' AND catid=' . (int)$catid;
						if ($result = $this->db->Query($sql))
						{	if ($this->db->AffectedRows())
							{	$delcat++;
							}
						}
					}
				}
				
				if ($delcat)
				{	$fail[] = 'removed from ' . $delcat . ' categories';
				}
				
				if (is_array($data['catid']))
				{	$addcat = 0;
					foreach ($data['catid'] as $catid=>$checked)
					{	if (!$this->cats[$catid])
						{	// then add
							$sql = 'INSERT INTO faqtocats SET faqid=' . $this->id . ', catid=' . (int)$catid;
							if ($result = $this->db->Query($sql))
							{	if ($this->db->AffectedRows())
								{	$addcat++;
								}
							}
						}
					}
					
					if ($addcat)
					{	$success[] = 'added to ' . $addcat . ' categories';
					}
				}
				
				$this->Get($this->id);
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Save
	
	public function InputForm()
	{	ob_start();
		
		if ($data = $this->details)
		{	$data['catid'] = $this->cats;
		} else
		{	if (!$data = $_POST)
			{	$data = array();
				if ($_GET['catid'])
				{	$data['catid'][$_GET['catid']] = true;
				}
			}
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextArea('Question', 'question', $this->InputSafeString($data['question']), '', 0, 0, 3, 60);
		$form->AddTextArea('Answer', 'answer', $this->InputSafeString($data['answer']), '', 0, 0, 10, 60);
		$form->AddTextInput('Display order', 'listorder', (int)$data['listorder'], 'short num', 6);
		$form->AddCheckBox('Live in front-end?', 'live', '', $data['live']);
		
		if ($cats = $this->GetPossibleCats())
		{	$form->AddRawText('<label style="font-style: italic;">Categories</label><br />');
			foreach ($cats as $catid=>$catname)
			{	$form->AddCheckBox($this->InputSafeString($catname), 'catid['. $catid . ']', '1', $data['catid'][$catid]);
			}
		}
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create FAQ', 'submit');
		
		if ($this->CanDelete())
		{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
					$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
					"delete this FAQ</a></p>\n";
		}
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	function GetPossibleCats()
	{
		$cats = array();
		$sql = 'SELECT * FROM faqcats ORDER BY listorder';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[$row['catid']] = $row['catname'];
			}
		}
		
		return $cats;
		
	} // end of fn GetPossibleCats
	
	public function CatsList()
	{	$cats = array();
		foreach ($this->cats as $cat_row)
		{	$cats[] = $this->InputSafeString($cat_row['catname']);
		}
		return implode(', ', $cats);
	} // end of fn CatsList
	
	public function CanDelete()
	{	return $this->id;
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM faq WHERE faqid=' . $this->id))
			{	if ($this->db->AffectedRows())
				{	$this->db->Query('DELETE FROM faqtocats WHERE faqid=' . $this->id);
					$this->RecordAdminAction(array('tablename'=>'faq', 'tableid'=>$this->id, 'area'=>'faq', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
} // end of class AdminFAQ
?>