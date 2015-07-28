<?php

class AskTheImam extends BlankItem
{	var $cats = array();

	public function __construct($id = 0)
	{	parent::__construct($id, 'asktheimam', 'askid');
	} // end of fn __construct
	
	public function GetExtra()
	{	$this->GetCats();
	} // end of fn GetExtra
	
	public function GetCats()
	{	$this->cats = array();
		if ($this->id)
		{	$sql = 'SELECT atiqcats.* FROM atiqcats, atitocats WHERE atiqcats.catid=atitocats.catid AND atitocats.askid=' . $this->id . ' ORDER BY atiqcats.listorder, atiqcats.catid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->cats[$row['catid']] = $row;
				}
			}
		}
	} // end of fn GetCats
	
	public function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		if ($username = $this->SQLSafe($data['username']))
		{	$fields[] = 'username="' . $username . '"';
		} else
		{	$fail[] = 'you must give your name';
		}
		
		if ($this->ValidEMail($useremail = $data['useremail']))
		{	
			$fields[] = 'useremail="' . $useremail . '"';
		} else
		{	$fail[] = 'you must provide a valid email address';
		}
		
		if ($asked = $this->SQLSafe($data['asked']))
		{	$fields[] = 'asked="' . $asked . '"';
		} else
		{	$fail[] = 'you have not asked a question';
		}
		
		$fields[] = 'userid=' . (int)$data['userid'];
		$fields[] = 'askedtime="' . $this->datefn->SQLDateTime() . '"';
		
		if (!$fail && $set = implode(',', $fields))
		{	$sql = 'INSERT INTO asktheimam SET ' . $set;
			if ($result = $this->db->Query($sql))
			{	$this->Get($this->db->InsertID());
				if ($this->id)
				{	$success[] = 'Your question has been asked';
				} else
				{	$fail[] = 'Your question was not asked';
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
		
	public function InputForm($user = false)
	{	ob_start();
		if (!$data = $_POST)
		{	$data = array();
			if ($user->id)
			{	$data['username'] = trim($user->details['firstname'] . ' ' . $user->details['surname']);
				$data['useremail'] = $user->details['username'];
			}
		}
		echo '<h1>Ask the Imam</h1><div class="col2-wrapper"><div class="inner clearfix"><p>Can\'t find what you are looking for in our <a href="">FAQ</a>? Post a question below and we will get back to you.</p><form class="contactform" action="', $_SERVER["SCRIPT_NAME"], '" method="post"><input type="hidden" name="userid" value="', (int)$user->id, '" /><div class="clearfix"><label>Your name</label> <input type="text" name="username" value="'. $this->InputSafeString($data["username"]) .'" /></div><div class="clearfix"><label>Your email address</label> <input type="text" name="useremail" value="'. $this->InputSafeString($data["useremail"]) .'" /></div><div class="clearfix"><label>Your question</label> <textarea name="asked" cols="50" rows="6">'. $this->InputSafeString($data["asked"]) .'</textarea></div><div class="clearfix"><label>&nbsp;</label><input type="submit" class="submit" value="Submit your question" /></div></form></div></div>';
		
		return ob_get_clean();	
	} // end of fn InputForm
	
} // end of class AskTheImam
?>