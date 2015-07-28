<?php
class AdminStudentComment extends StudentComment
{	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // fn __construct
	
	public function StatusString()
	{	ob_start();
		if ($this->details['suppressed'])
		{	if ($this->details['suppressedby'] && ($adminuser = new AdminUser($this->details['suppressedby'])) && $adminuser->userid)
			{	echo 'suppressed by <a href="useredit.php?userid=', $adminuser->userid, '">',  $adminuser->username, '</a>';
			} else
			{	echo 'not yet moderated';
			}
		} else
		{	if ($this->details['suppressedby'] && ($adminuser = new AdminUser($this->details['suppressedby'])) && $adminuser->userid)
			{	echo 'made live by by <a href="useredit.php?userid=', $adminuser->userid, '">',  $adminuser->username, '</a>';
			} else
			{	echo 'live';
			}
		}
		return ob_get_clean();
	} // end of fn StatusString
	
	public function AjaxForm()
	{	ob_start();
		//$this->VarDump($this->details);
		$student = $this->GetAuthor();
		echo '<h3>by ', $this->InputSafeString($student->GetName()), ' on ', date('d/m/y @H:i', strtotime($this->details['postdate'])), '</h3><form onsubmit="return false;"><label>Suppressed?</label><input type="checkbox" name="suppress" id="revSuppress"', $this->details['suppressed'] ? ' checked="checked"' : '', ' /><br /><label>Admin notes</label><textarea id="revAdminNotes">', $this->InputSafeString($this->details['adminnotes']), '</textarea><br /><label>&nbsp;</label><a class="submit" onclick="CommentSave(', $this->id, ');">Save</a><br /></form><h3>Original review</h3><p id="ajaxRevText">', nl2br($this->InputSafeString($this->details['commenttext'])), '</p>';
		
		return ob_get_clean();
	} // end of fn AjaxForm
	
	public function AdminSave($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		
		$fields[] = 'adminnotes="' . $this->SQLSafe($data['adminnotes']) . '"';
		
		$suppressed = ($data['suppressed'] ? 1 : 0);
		if ($this->details['suppressed'] != $suppressed)
		{	$fields[] = 'suppressed=' . $suppressed;
			$fields[] = 'suppressedby=' . (int)$_SESSION[SITE_NAME]['auserid'];
		}
		
		if (!$fail)
		{	$sql = 'UPDATE studentcomments SET ' . implode(', ', $fields) . ' WHERE scid=' . $this->id;
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$success[] = 'Changes saved';
					$this->Get($this->id);
				
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AdminSave
	
} // end of class defn AdminStudentComment
?>