<?php
include_once('sitedef.php');

class AskImamSubmissionPage extends AdminAskImamPage
{	var $submission;

	function __construct()
	{	parent::__construct();

		$this->submission = new AdminAskImamSubmission($_GET['id']);
		
		if (isset($_POST['adminnotes']))
		{	$saved = $this->submission->AdminSave($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->submission->CanDelete() && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->submission->Delete())
			{	header('location: askimamsubmissions.php');
				exit;
			}
		}

		$this->breadcrumbs->AddCrumb('askimamsubmissions.php', 'Submissions');
		$this->breadcrumbs->AddCrumb('askimamsubmission.php?id=' . $this->submission->id, date('d/m/y', strtotime($this->submission->details['asktime'])) . ' by ' . $this->InputSafeString($this->submission->details['subname']));
	} //  end of fn __construct
	
	function AskImamBody()
	{	echo $this->submission->AdminDisplay(), $this->submission->InputForm();
	} // end of fn AskImamBody
	
} // end of defn AskImamSubmissionPage

$page = new AskImamSubmissionPage();
$page->Page();
?>