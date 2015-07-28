<?php
include_once('sitedef.php');

class AskImamCommentsPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('comments');
		$this->js[] = 'admin_studentcomments.js';
		$this->css[] = 'adminreviews.css';
		
		$this->breadcrumbs->AddCrumb('askimamcomments.php?id=' . $this->question->id, 'Comments');
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->question = new AdminAskImamQuestion($_GET['id']);
		$this->topic = $this->question->GetTopic();
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	//echo $this->question->MultiMediaDisplay();
		$comments = new AdminStudentComments('askimamquestions', $this->question->id);
		echo $comments->CommentsDisplay();
	} // end of fn AskImamBody
	
} // end of defn AskImamCommentsPage

$page = new AskImamCommentsPage();
$page->Page();
?>