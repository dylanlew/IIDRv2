<?php
include_once('sitedef.php');

class AskImamQuestionsListPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('questions');
		$this->breadcrumbs->AddCrumb('askimamquestions.php?id=' . $this->topic->id, 'Questions');
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->topic = new AdminAskImamTopic($_GET['id']);
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	echo $this->topic->QuestionsList();
	} // end of fn AskImamBody
	
} // end of defn AskImamQuestionsListPage

$page = new AskImamQuestionsListPage();
$page->Page();
?>