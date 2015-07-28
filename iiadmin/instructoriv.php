<?php
include_once('sitedef.php');

class InstructorInterviewPage extends AdminInstructorPage
{	private $act;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'interviews';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'interviews_tiny_mce.js';
		
		if (isset($_POST['ivtitle']))
		{	$saved = $this->iv->Save($_POST, $this->instructor->id);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->iv->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->iv->Delete())
			{	header('location: instructorivs.php?id=' . $this->instructor->id);
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('instructorivs.php?id' . $this->instructor->id, 'Interviews');
		if ($this->iv->id)
		{	$this->breadcrumbs->AddCrumb('instructoract.php?id=' . $this->iv->id, $this->InputSafeString($this->iv->details['ivtitle']));
		} else
		{	$this->breadcrumbs->AddCrumb('instructoract.php?inid=' . $this->instructor->id, 'New interview');
		}
	} // end of fn InstructorConstruct
	
	public function AssignInstructor()
	{	$this->iv = new AdminInstructorInterview($_GET['id']);
		if ($this->iv->id)
		{	$this->instructor  = new AdminInstructor($this->iv->details['inid']);
		} else
		{	$this->instructor  = new AdminInstructor($_GET['inid']);
		}
	} // end of fn AssignInstructor
	
	function InstructorBody()
	{	return $this->iv->InputForm($this->instructor->id);
	} // end of fn InstructorBody
	
} // end of defn InstructorInterviewPage

$page = new InstructorInterviewPage();
$page->Page();
?>