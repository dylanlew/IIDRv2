<?php
include_once('sitedef.php');

class AskImamQuestionsListPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('instructors');
		if (is_array($_POST['listorder']))
		{	if ($changed = $this->topic->SaveInstListOrder($_POST['listorder']))
			{	$this->successmessage = $changed . ' changes saved';
			} else
			{	$this->failmessage = 'no changes saved';
			}
		}
		$this->breadcrumbs->AddCrumb('askimaminstructors.php?id=' . $this->topic->id, 'Instructors');
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->topic = new AdminAskImamTopic($_GET['id']);
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	echo $this->topic->InstructorListContainer();
	} // end of fn AskImamBody
	
} // end of defn AskImamQuestionsListPage

$page = new AskImamQuestionsListPage();
$page->Page();
?>