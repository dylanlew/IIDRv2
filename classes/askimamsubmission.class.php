<?php
class AskImamSubmission extends BlankItem
{	
	public function __construct($id = null)
	{	parent::__construct($id, 'askimamsubmissions', 'asid');
	} // fn __construct
	
	public function InputForm(Student $student)
	{	ob_start();
		echo '<form onsubmit="return false;"><h3>What do you want to ask?</h3><p><textarea id="asqText">', $this->InputSafeString($_POST['asktext']), '</textarea></p><p><label>Your name</label><input type="text" id="asqSubName" value="', $this->InputSafeString($_POST['subname'] ? $_POST['subname'] : ($student->id ? $student->GetName() : '')), '" /></p><p><label>Your email</label><input type="text" id="asqSubEmail" value="', $this->InputSafeString($_POST['subemail'] ? $_POST['subemail'] : ($student->id ? $student->details['username'] : '')), '" /></p><p><a id="asqSubmit" onclick="SubmitQuestion();">Submit your question</a></p></form>';
		return ob_get_clean();
	} // end of fn InputForm
	
	public function CreateFromPost($data = array(), Student $student)
	{	$fail = array();
		$success = array();
		$fields = array();
		
		$fields[] = 'asktime="' . $this->datefn->SQLDateTime() . '"';
		$fields[] = 'student=' . (int)$student->id;
		
		if ($asktext = $this->SQLSafe($data['asktext']))
		{	$fields[] = 'asktext="' . $asktext . '"';
		} else
		{	$fail[] = 'you appear not to have asked a question';
		}
		
		if (!$subname = $this->SQLSafe($data['subname']))
		{	if ($student->id)
			{	$subname = $this->SQLSafe($student->GetName());
			}
		}
		
		if ($subname)
		{	$fields[] = 'subname="' . $subname . '"';
		} else
		{	$fail[] = 'you must give your name to submit a question';
		}
		
		if (!$subemail = $this->SQLSafe($data['subemail']))
		{	if ($student->id)
			{	$subemail = $this->SQLSafe($student->details['username']);
			}
		}
		
		if ($this->ValidEMail($subemail))
		{	$fields[] = 'subemail="' . $subemail . '"';
		} else
		{	$fail[] = 'you must give a valid email address to submit a question';
		}
		
	//$fail[] = 'test';	
		if (!$fail)
		{	$sql = 'INSERT INTO askimamsubmissions SET ' . $set = implode(', ', $fields);
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows() && ($id = $this->db->InsertId()))
				{	$this->Get($id);
					$success[] = 'Your question has been submitted';
					$this->SendAdminEmail();
				}
			}// else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
	} // end of fn CreateFromPost
	
	public function SendAdminEmail()
	{	ob_start();
		echo '<p>Name: ', $this->InputSafeString($this->details['subname']), '</p><p>Email: ', $this->InputSafeString($this->details['subemail']), '</p>';
		if ($this->details['student'] && ($student = new Student($this->details['student'])) && $student->id)
		{	echo '<p>Existing student: <a href="', SITE_URL, 'iiadmin/member.php?id=', $student->id, '">', $this->InputSafeString($student->GetName()), ' (', $this->InputSafeString($student->details['username']), ')</a></p>';
		}
		echo '<p>Their question ...</p><p style="padding-left: 10px;">', nl2br($this->InputSafeString($this->details['asktext'])), '</p><p>View it <a href="', $link = SITE_URL . 'iiadmin/askimamsubmission.php?id=' . $this->id, '">here</a></p>';
		$htmlbody = ob_get_clean();
		
		ob_start();
		$nl = "\n";
		echo 'Name: ', $this->InputSafeString($this->details['subname']), $nl, 'Email: ', $this->details['subemail'], $nl;
		if ($student && $student->id)
		{	echo 'Existing student: ', SITE_URL, 'iiadmin/member.php?id=', $student->id, ' - ', $student->GetName(), ' (', $student->details['username'], ')', $nl;
		}
		echo 'Their question ...', $nl, $this->details['asktext'], $nl, 'View it here - ', $link, $nl;
		$plainbody = ob_get_clean();
		
		$mail = new HTMLMail();
		$mail->SetSubject('Question submitted for "Ask the Expert"');
		$mail->SendEMailForArea('ASKEXPERT', $htmlbody, $plainbody);
	} // end of fn SendAdminEmail
	
} // end of class AskImamSubmission
?>