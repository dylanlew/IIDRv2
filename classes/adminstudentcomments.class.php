<?php
class AdminStudentComments extends StudentComments
{	
	public function __construct($sctype = '', $parentid = 0)
	{	parent::__construct($sctype, $parentid);
	} // fn __construct
	
	public function Get()
	{	parent::Get(false);
	} // end of fn Get
	
	public function CommentsDisplay()
	{	ob_start();
		echo '<div class="mmdisplay"><div id="mmdContainer">', $this->CommentsTable(), '</div><script type="text/javascript">scParentid=', $this->parentid, ';scParentType="', $this->sctype, '";$().ready(function(){$("body").append($(".jqmWindow"));$("#rlp_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="rlp_modal_popup" class="jqmWindow"><a href="#" class="jqmClose submit">Close</a><div id="rlpModalInner"></div></div></div>';
		return ob_get_clean();
	} // end of fn CommentsDisplay
	
	public function CommentsTable()
	{	ob_start();
		echo '<table><tr><th>Student</th><th>Date</th><th>Comments</th><th>Status</th><th>Admin notes</th><th>Actions</th></tr>';
		foreach ($this->comments as $comment_row)
		{	$comment = new AdminStudentComment($comment_row);
			if (!$students[$comment->details['sid']])
			{	$students[$comment->details['sid']] = new Student($comment->details['sid']);
			}
			echo '<tr><td>', $this->InputSafeString($students[$comment->details['sid']]->GetName()), '</td><td>', date('d/m/y @H:i', strtotime($comment->details['postdate'])), '</td><td>', nl2br($this->InputSafeString($comment->details['commenttext'])), '</td><td>', $comment->StatusString(), '</td><td>', nl2br($this->InputSafeString($comment->details['adminnotes'])), '</td><td><a onclick="CommentPopUp(', $comment->id, ');">change status</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn CommentsTable
	
} // end of class defn AdminStudentComments
?>