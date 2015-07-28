<?php
include_once('sitedef.php');

class AskTheImamViewPage extends AskTheImamPage
{	private $question;
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function ATIConstructor()
	{	parent::ATIConstructor();
		$this->question = new AdminAskTheImam($_GET['id']);
		
		if (isset($_POST['answer']))
		{	$saved = $this->question->SaveResponse($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if (isset($_POST['answeredit']))
		{	$saved = $this->question->SaveEdit($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($_GET['sendemail'])
		{	if ($this->question->JustSendEmail())
			{	$this->successmessage = 'email has been sent to questioner';
			} else
			{	$this->failmessage = 'email not sent';
			}
		}
		
		if ($_GET['createfaq'])
		{	$saved = $this->question->CreateFAQ();
			if ($saved['successmessage'])
			{	header('location: faq.php?id=' . $this->question->details['faqid']);
				exit;
			} else
			{	$this->failmessage = 'Creation of FAQ failed: ' . $saved['failmessage'];
			}
		}
		
		$this->breadcrumbs->AddCrumb('ati_question.php', 'question #' . $this->question->id);
	} // end of fn ATIConstructor
	
	public function ATIMainContent()
	{	echo $this->question->Display(), $this->question->RespondForm();
	} // end of fn ATIMainContent

} // end of defn AskTheImamViewPage

$page = new AskTheImamViewPage();
$page->Page();
?>