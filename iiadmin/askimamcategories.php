<?php
include_once('sitedef.php');

class AskImamQuestionsListPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('categories');
		$this->js[] = 'admin_ask_cats.js';
		$this->css[] = 'course_mm.css';
		$this->breadcrumbs->AddCrumb('askimamcategories.php?id=' . $this->topic->id, 'Categories');
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->topic = new AdminAskImamTopic($_GET['id']);
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	echo $this->topic->CategoriesDisplay();
	} // end of fn AskImamBody
	
} // end of defn AskImamQuestionsListPage

$page = new AskImamQuestionsListPage();
$page->Page();
?>