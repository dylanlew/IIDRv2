<?php
include_once('sitedef.php');

class AskImamQuestionEditPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct('question');
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'course_tiny_mce.js';
		
		if (!$this->question->id)
		{	$this->breadcrumbs->AddCrumb('askimamquestions.php?id=' . $this->topic->id, 'Questions');
			$this->breadcrumbs->AddCrumb('askimamquestions.php?topicid=' . $this->topic->id, 'New question');
		}
	} // end of fn AskImamLoggedInConstruct
	
	function AskImamConstructFunctions()
	{	if (isset($_POST['qanswer']))
		{	$saved = $this->question->Save($_POST, $this->topic->id);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->question->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->question->Delete())
			{	header('location: askimamquestions.php?id=' . $this->topic->id);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn AskImamConstructFunctions
	
	public function AssignTopic()
	{	$this->question = new AdminAskImamQuestion($_GET['id']);
		if ($this->question->id)
		{	$this->topic = $this->question->GetTopic();
		} else
		{	if ($_POST['topicid'])
			{	$this->topic = new AdminAskImamTopic($_POST['topicid']);
			} else
			{	$this->topic = new AdminAskImamTopic($_GET['topicid']);
			}
		}
	} // end of fn AssignTopic
	
	public function BodyMenuOptions()
	{	$options = parent::BodyMenuOptions();
		if (!$this->question->id)
		{	$options['question'] = array('link'=>'askimamquestion.php?topicid=' . $this->topic->id, 'text'=>'New question');
		}
		return $options;
	} // end of fn BodyMenuOptions
	
	function AskImamBody()
	{	echo $this->question->InputForm($this->topic->id);
	} // end of fn AskImamBody
	
} // end of defn AskImamQuestionEditPage

$page = new AskImamQuestionEditPage();
$page->Page();
?>