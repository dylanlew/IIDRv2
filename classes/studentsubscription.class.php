<?php
class StudentSubscription extends BlankItem
{	
	public function __construct($id = 0)
	{	parent::__construct($id, 'subscriptions', 'subid');
	} // fn __construct
	
	public function CreateFromOrderItem(Student $student, $product = false, $item = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		if ($userid = (int)$student->id)
		{	if ($student->CanHaveSubscription())
			{	$fields[] = 'userid=' . $userid;
				$created = $student->LastSubscribeDate();
				$fields[] = 'created="' . $created . '"';
				if ($months = (int)$product->details['months'])
				{	$fields[] = 'expires="' . $this->datefn->SQLDateTime(strtotime($created . ' +' . $months . ' months')) . '"';
					$fields[] = 'months=' . $months;
				} else
				{	$fail[] = 'months missing';
				}
			} else
			{	$fail[] = 'student can\'t have subscription';
			}
		} else
		{	$fail[] = 'no student given';
		}
		
		if ($orderitemid = (int)$item['id'])
		{	$fields[] = 'orderitemid=' . $orderitemid;
		}
		
		if (!$fail)
		{	$sql = 'INSERT INTO subscriptions SET ' . implode(', ', $fields);
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows() && ($id = $this->db->InsertID()))
				{	$this->Get($id);
					return $this->id;
				}
			}
		}
		
		return false;		
		
	} // end of fn CreateFromOrderItem
	
	public function SendStudentEmail()
	{
		if (($student = new Student($this->details['userid'])) && $student->id && $this->ValidEmail($student->details['username']))
		{
			$fields = array();
			$fields['site_url'] = $this->link->GetLink();
			$fields['site_link_html'] = '<a href="' . $fields['site_url'] . '">visit us here</a>';
			$fields['firstname'] = $student->details['firstname'];
			$fields['sub_title'] = $this->InputSafeString($this->details['title']);
			$fields['sub_months'] = (int)$this->details['months'];
			$fields['sub_startdate'] = $this->OutputDate($this->details['created']);
			$fields['sub_enddate'] = $this->OutputDate($this->details['expires']);
			
			$t = new MailTemplate('subscription');
			$mail = new HTMLMail;
			$mail->SetSubject($t->details['subject']);
			$mail->Send($this->student->details['username'], $t->BuildHTMLEmailText($fields), $t->BuildHTMLPlainText($fields));
		}	
	} // end of fn SendStudentEmail
	
} // end of class StudentSubscription
?>