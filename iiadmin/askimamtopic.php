<?php
include_once('sitedef.php');

class AskImamEditPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('topic');
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		if (!$this->topic->id)
		{	$this->breadcrumbs->AddCrumb('askimamtopic.php', 'new theme');
		}
	} // end of fn AskImamLoggedInConstruct
	
	function AskImamConstructFunctions()
	{	if (isset($_POST['title']))
		{	$saved = $this->topic->Save($_POST, $_FILES['imagefile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->topic->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->topic->Delete())
			{	header('location: askimamtopics.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn AskImamConstructFunctions
	
	public function AssignTopic()
	{	$this->topic = new AdminAskImamTopic($_GET['id']);
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	echo $this->topic->InputForm();
	} // end of fn AskImamBody
	
} // end of defn AskImamEditPage

$page = new AskImamEditPage();
$page->Page();
?>