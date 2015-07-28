<?php 
require_once('init.php');

class AjaxStudentComments extends BasePage
{		
	function __construct()
	{	parent::__construct();
		
		switch ($_GET['action'])
		{	case 'create': echo $this->CreateFromPost();
							$comment = new StudentComment();
							echo $comment->CreateForm($_GET['parentid'], $_GET['sctype'], $this->user->id > 0);
							break;
			case 'list': echo $this->CommentsList();
							break;
		}
		
	//	$this->VarDump($_GET);
	//	$this->VarDump($_POST);
	//	echo $this->user->id;
	
	} // end of fn __construct
	
	public function CommentsList()
	{	if ($comments = new StudentComments($_GET['sctype'], $_GET['parentid']))
		{	echo $comments->OutputList((int)$_GET['plimit']);
		}
	} // end of fn CommentsList
	
	public function CreateFromPost()
	{	ob_start();
		$fail = array();
		$success = array();
		
		if ($sid = (int)$this->user->id)
		{	$suppressed = $this->FlagFileSet('mod_' . $_GET['sctype']);
			$fields = array('postdate="' . $this->datefn->SQLDateTime() . '"', 'sid=' . $sid, 'suppressed=' . ($suppressed ? '1' : '0'));
			
			if ($pid = (int)$_GET['parentid'])
			{	$fields[] = 'parentid=' . $pid;
			} else
			{	$fail[] = 'Comment subject not found';
			}
			
		/*	if ($rating = round($_POST['rating'], 1))
			{	$fields[] = 'rating=' . $rating;
			} else
			{	$fail[] = 'You must give a rating';
			}*/
			
			if ($sctype = $this->SQLSafe($_GET['sctype']))
			{	$fields[] = 'sctype="' . $sctype . '"';
			} else
			{	$fail[] = 'Comment type not found';
			}
			
			if ($commenttext = $this->SQLSafe($_POST['text']))
			{	$fields[] = 'commenttext="' . $commenttext . '"';
			} else
			{	$fail[] = 'You have not left any comments';
			}
			
			
			if (!$fail)
			{	$sql = 'INSERT INTO studentcomments SET ' . implode(', ', $fields);
				if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	$success[] = 'Thank you for submitting your comments';
						if ($suppressed)
						{	$success[] = 'All comments are moderated before being published';
						}
					}
				}
			}
		} else
		{	$fail[] = 'You must be logged in to leave comments';
		}

		if ($fail)
		{	echo '<div class="revFail">', implode(', ', $fail), '</div>';
		}
		if ($success)
		{	echo '<div class="revSuccess">', implode(', ', $success), '</div>';
		}
		
		return ob_get_clean();
	} // end of fn CreateFromPost
	
} // end of defn AjaxStudentComments

$page = new AjaxStudentComments();
?>