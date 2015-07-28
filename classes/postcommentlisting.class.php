<?php
class PostCommentListing extends BasePage
{	public $post;
	public $comments = array();
	
	public function __construct(Post $post)
	{
		parent::__construct();
		$this->post = $post;
		$this->ProcessNewComment();
		$this->comments = $this->post->GetComments(); 
	} // end of fn __construct
	
	public function ProcessNewComment()
	{
		if ($this->user->id && isset($_POST['ccomment']))
		{	
			// process form	
			$c = new PostComment;
			$c->details['pid'] = (int)$this->post->id;
			$c->details['sid'] = $this->user->id;
			$c->details['comment'] = $_POST['ccomment'];
			
			if ($c->Save())
			{
				$_POST['ccomment'] = '';
				return true;
			}
		}
	} // end of fn ProcessNewComment
	
	
	public function NewCommentForm()
	{	ob_start();
		
		echo '<h3>Post comment:</h3>';
		
		if ($this->user->id)
		{
			$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->post->id);
			$form->AddTextArea('', 'ccomment', $this->InputSafeString($_POST['ccomment']), '', 0, 0, 5, 60);
			$form->AddSubmitButton('', 'Add Comment', 'submitcomment');
			$form->Output();
			echo '<div class="clear"></div>';
		} else
		{
			echo '<p>You must be logged in to post a comment.</p>';
		}
		
		return ob_get_clean();
	} // end of fn NewCommentForm
	
	public function Output()
	{	ob_start();
		
		echo '<div class="postCommentContainer">', $this->NewCommentForm(), '<h3>Comments (', sizeof($this->comments), ')</h3>';
		
		if ($this->comments)
		{	echo '<ul>';
			foreach($this->comments as $c)
			{
				echo '<li><div class="the-comment">', $this->InputSafeString($c->details["comment"]), '</div><h4>by ', $this->InputSafeString($c->GetAuthorName()), ', ', $this->AgoDateString(strtotime($c->details['dateadded'])), '</h4></li>';
						
			}
			echo '</ul>';
		} else
		{
			
		}
		echo '</div>';
		
		return $output;
	} // end of fn Output
	
} // end of defn PostCommentListing
?>