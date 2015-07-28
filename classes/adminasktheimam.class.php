<?php

class AdminAskTheImam extends AskTheImam
{	
	public function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function SaveResponse($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		if ($answer = $this->SQLSafe($data['answer']))
		{	$fields[] = 'answer="' . $answer . '"';
		} else
		{	$fail[] = 'you have not answered the question';
		}
		
		if ($answeredby = (int)$this->GetAdminUser()->userid)
		{	$fields[] = 'answeredby=' . $answeredby;
		} else
		{	$fail[] = 'admin user not known';
		}
		
		$fields[] = 'answertime="' . $this->datefn->SQLDateTime() . '"';
		
		if (!$fail && $set = implode(',', $fields))
		{	$sql = 'UPDATE asktheimam SET ' . $set . ' WHERE askid=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = 'Question has now been answered';
					$this->Refresh();
					if ($data['sendemail'])
					{	if ($this->SendEmailToAsker())
						{	$success[] = 'email sent to questioner';
						}
					}
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn SaveResponse
	
	public function SaveEdit($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		if ($answer = $this->SQLSafe($data['answeredit']))
		{	$fields[] = 'answer="' . $answer . '"';
		} else
		{	$fail[] = 'the answer cannot be removed completely';
		}

		$publish = ($data['publish'] ? '1' : '0');
		$fields[] = 'publish=' . $publish;
		if ($this->id && ($publish != $this->details['publish']))
		{	$admin_actions[] = array('action'=>'Published', 'actionfrom'=>$this->details['publish'], 'actionto'=>$publish, 'actiontype'=>'boolean');
		}
		
		
		
		if (!$fail && $set = implode(',', $fields))
		{	$sql = 'UPDATE asktheimam SET ' . $set . ' WHERE askid=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = 'Changes saved';
					$this->Refresh();
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
					$sql = 'DELETE FROM atitocats WHERE askid=' . $this->id . ' AND catid=' . (int)$catid;
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
						$sql = 'INSERT INTO atitocats SET askid=' . $this->id . ', catid=' . (int)$catid;
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
	
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn SaveEdit
	
	public function SendEmailToAsker()
	{	
		if ($this->id && $this->details['answer'] && !(int)$this->details['answersent'])
		{	$html_fields = array();
			$plain_fields = array();
			$html_fields['askimam_question'] = nl2br($this->InputSafeString($this->details['asked']));
			$html_fields['askimam_answer'] = nl2br($this->InputSafeString($this->details['answer']));
			$plain_fields['askimam_question'] = stripslashes($this->details['asked']);
			$plain_fields['askimam_answer'] = stripslashes($this->details['answer']);
			
			
			$t = new MailTemplate('askimam_response');
			$mail = new HTMLMail;
			$mail->SetSubject($t->details['subject']);
			$mail->Send($this->details['useremail'], $t->BuildHTMLEmailText($html_fields), $t->BuildHTMLPlainText($plain_fields));
			
			$sql = 'UPDATE asktheimam SET answersent="' . $this->datefn->SQLDateTime() . '" WHERE askid=' . (int)$this->id;
			$this->db->Query($sql);
			$this->Refresh();
			return true;
		}
	} // end of fn SendEmailToAsker
	
	public function JustSendEmail()
	{	return $this->SendEmailToAsker();
	} // end of fn JustSendEmail
	
	public function CreateFAQ()
	{	
		if ($this->id && $this->details['asked'] && $this->details['answer'])
		{	if ($this->details['faqid'])
			{	return array('failmessage'=>'this question cannot be saved to an FAQ');
			} else
			{	$faq = new AdminFAQ();
				$data = array('question'=>stripslashes($this->details['asked']), 'answer'=>stripslashes($this->details['answer']), 'askid'=>$this->id);
				$created = $faq->Save($data);
				if ($faq->id)
				{	$sql = 'UPDATE asktheimam SET faqid=' . $faq->id . ' WHERE askid=' . (int)$this->id;
					$this->db->Query($sql);
					$this->Refresh();
					return $created;
				} else
				{	return array('failmessage'=>'FAQ create failed');
				}
			}
		} else
		{	return array('failmessage'=>'this question cannot be saved to an FAQ');
		}
	} // end of fn CreateFAQ
	
	public function Display()
	{	ob_start();
		echo '<table class="ati_display"><tr><td class="ati_label">Questioner\'s name</td><td>', $this->InputSafeString($this->details['username']);
		if ($this->details['userid'] && ($student = new Student($this->details['userid'])) && $student->id)
		{	
			echo ' (Student: <a href="member.php?id=', $student->id, '">', trim($this->InputSafeString($student->details['firstname'] . ' ' . $student->details['surname'])), '</a>)';
		}
		echo '</td></tr><tr><td class="ati_label">Questioner\'s email</td><td><a href="mailto:', $this->InputSafeString($this->details['useremail']), '">', $this->InputSafeString($this->details['useremail']), '</a></td></tr><tr><td class="ati_label">Asked</td><td>', date('d-M-y @H:i', strtotime($this->details['askedtime'])), '</td></tr><tr><td class="ati_label">Question</td><td>', nl2br($this->InputSafeString($this->details['asked'])), '</td></tr>';
		if ($this->details['answeredby'])
		{	$adminuser = new AdminUser($this->details['answeredby']);
			echo '<tr><td class="ati_label">Answered on</td><td>', date('d-M-y @H:i', strtotime($this->details['askedtime'])), '</td></tr><tr><td class="ati_label">By</td><td>';
			if ($this->CanAdminUser('administration'))
			{	echo '<a href="useredit.php?userid=', $adminuser->userid, '">', $adminuser->username, '</a>';
			} else
			{	echo $adminuser->username;
			}
			echo '</td></tr><tr><td class="ati_label">Answer</td><td>', nl2br($this->InputSafeString($this->details['answer'])), '</td></tr><tr><td class="ati_label">Answer sent</td><td>', (int)$this->details['answersent'] ? date('d-M-y @H:i', strtotime($this->details['answersent'])) : ('not sent - <a href="' . $_SERVER['SCRIPT_NAME'] . '?id=' . $this->id . '&sendemail=1">send now</a>'), '</td></tr>';
			echo '<tr><td class="ati_label">FAQ</td><td>';
			if ($this->details['faqid'])
			{	echo 'this has been added to the FAQ - <a href="faq.php?id=', $this->details['faqid'], '">view here</a>';
			} else
			{	echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?id=' . $this->id . '&createfaq=1">add to FAQ</a> (will be available for editing before being made live)';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn Display
	
	public function RespondForm()
	{	ob_start();
		if (!$this->details['answeredby'])
		{	if (!$data = $_POST)
			{	$data = array();
				$data['sendemail'] = 1;
			}
			
			$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
			
			$form->AddTextArea('Your answer', 'answer', $this->InputSafeString($data['answer']), '', 0, 0, 10, 60);
			$form->AddCheckBox('Send email?', 'sendemail', '', $data['sendemail']);
			
			$form->AddSubmitButton('', 'Submit Response', 'submit');
			$form->Output();
		} else
		{	echo $this->EditForm();
		}
		return ob_get_clean();
	} // end of fn RespondForm
	
	public function EditForm()
	{	ob_start();
		
		$data = $this->details;
		$data['catid'] = $this->cats;
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextArea('Your answer', 'answeredit', $this->InputSafeString($data['answer']), '', 0, 0, 10, 60);
		$form->AddCheckBox('Published?', 'publish', '', $data['publish']);
		
		if ($cats = $this->GetPossibleCats())
		{	$form->AddRawText('<label style="font-style: italic;">Categories</label><br />');
			foreach ($cats as $catid=>$catname)
			{	$form->AddCheckBox($this->InputSafeString($catname), 'catid['. $catid . ']', '1', $data['catid'][$catid]);
			}
		}
		
		$form->AddSubmitButton('', 'Submit Response', 'submit');
		$form->Output();

		return ob_get_clean();
	} // end of fn EditForm
	
	function GetPossibleCats()
	{
		$cats = array();
		$sql = 'SELECT * FROM atiqcats ORDER BY listorder';
		
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
	
} // end of class AdminAskTheImam
?>