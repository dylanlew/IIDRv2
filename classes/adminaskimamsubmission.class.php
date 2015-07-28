<?php
class AdminAskImamSubmission extends AskImamSubmission
{	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // fn __construct
	
	public function AdminDisplay()
	{	ob_start();
		if ($this->CanDelete())
		{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this submission</a></p>';
		}
		echo '<h3>Submission details</h3><table class="adminDetailsHeader"><tr><td class="label">Date/time of submission</td><td>', date('d-M-Y @H:i', strtotime($this->details['asktime'])), '</td></tr><tr><td class="label">Name</td><td>', $this->InputSafeString($this->details['subname']), '</td></tr><tr><td class="label">Email</td><td><a href="mailto:', $this->InputSafeString($this->details['subemail']), '">', $this->InputSafeString($this->details['subemail']), '</a></td></tr>';
		if ($this->details['student'])
		{	echo '<tr><td class="label">Registered Student</td><td>';
			if (($student = new Student($this->details['student'])) && $student->id)
			{	echo '<a href="member.php?id=', $student->id, '">', $this->InputSafeString($student->GetName()), '</a>';
			} else
			{	echo 'student id ', $this->details['student'], ' not found';
			}
			echo '</td></tr>';
		}
		echo '<tr><td class="label">Question asked</td><td>', nl2br($this->InputSafeString($this->details['asktext'])), '</td></tr><tr><td class="label">&nbsp;</td><td><form method="post" action="askimamquestion.php"><input type="hidden" name="qtext" value="', $this->InputSafeString($this->details['asktext']), '" /><input type="submit" value="Create question from this" /></form></td></tr></table>';
		return ob_get_clean();
	} // end of fn AdminDisplay
	
	public function InputForm()
	{	ob_start();
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id , 'course_edit');
		$form->AddTextArea('Admin notes', 'adminnotes', $this->InputSafeString($this->details['adminnotes']), '', 0, 0, 5, 60);
		
		$form->AddSubmitButton('', 'Save Changes', 'submit');
		
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	public function AdminSave($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		$fields[] = 'adminnotes="' . $this->SQLSafe($data['adminnotes']) . '"';
		
	//$fail[] = 'test';	
		if (!$fail && ($set = implode(', ', $fields)))
		{	$sql = 'UPDATE askimamsubmissions SET ' . $set . 'WHERE asid=' . $this->id;
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = 'Changes saved';
					$this->Get($this->id);
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AdminSave
	
	public function QuestionSample()
	{	if (strlen($this->details['asktext']) > 88)
		{	return substr($this->details['asktext'], 0, 80) . ' [... more]';
		} else
		{	return $this->details['asktext'];
		}
	} // end of fn QuestionSample
	
	public function CanDelete()
	{	return $this->id && !$this->details['adminnotes'];
	} // end of fn CanDelete
	
} // end of class AdminAskImamSubmission
?>