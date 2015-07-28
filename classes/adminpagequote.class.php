<?php
class AdminPageQuote extends PageQuote
{
	public function __construct($id = null)
	{	parent::__construct($id);
	} // fn __construct
	
	public function CanDelete()
	{	return true;
	} // end of fn CanDelete
	
	public function SampleText()
	{	return $this->InputSafeString(substr($this->details['quotetext'], 0, 30)) . ' ...';
	} // end of fn SampleText
	
	public function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
	
		if ($quotetext = $this->SQLSafe($data['quotetext']))
		{	$fields[] = 'quotetext="' . $this->SQLSafe($data['quotetext']) . '"';
			if ($this->id && ($data['quotetext'] != $this->details['quotetext']))
			{	$admin_actions[] = array('action'=>'Text', 'actionfrom'=>$this->details['quotetext'], 'actionto'=>$data['quotetext']);
			}
		} else
		{	$fail[] = 'Text is missing';
		}
	
		$live = ($data['live'] ? 1 : 0);
		$fields[] = 'live=' . $live;
		if ($this->id && ($this->details['live'] != $live))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}
		
		if (!$fail)
		{	if ($this->id)
			{	$sql = 'UPDATE pagequotes SET ' . implode(', ', $fields) . ' WHERE 	pqid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO pagequotes SET ' . implode(', ', $fields);
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$success[] = 'Changes saved';
						$base_parameters = array('tablename'=>'pagequotes', 'tableid'=>$this->id, 'area'=>'page quotes');
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New quote created';
					}
					$this->Get($this->id);
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AdminSave
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		
		$form->AddTextArea('Quote', 'quotetext', $this->InputSafeString($data['quotetext']), '', 0, 0, 10, 40);
		$form->AddCheckBox('Live', 'live', '1', $data['live']);
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Quote', 'submit');
		if ($this->id)
		{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this quote</a></p>';
			if ($histlink = $this->DisplayHistoryLink('pagequotes', $this->id))
			{	echo '<p>', $histlink, '</p>';
			}
		}

		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of class defn AdminPageQuote
?>