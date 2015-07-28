<?php
include_once('sitedef.php');

class AskImamQuestionEditPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('multimedia');
		$this->js[] = 'admin_askimam_mm.js';
		$this->css[] = 'course_mm.css';
		
		$this->breadcrumbs->AddCrumb('askimammultimedia.php?id=' . $this->question->id, 'Multimedia');
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->question = new AdminAskImamQuestion($_GET['id']);
		$this->topic = $this->question->GetTopic();
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	echo $this->question->MultiMediaDisplay();
	} // end of fn AskImamBody
	
} // end of defn AskImamQuestionEditPage

$page = new AskImamQuestionEditPage();
$page->Page();
?>