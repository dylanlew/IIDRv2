<?php
include_once('sitedef.php');

class InstructorActivityPage extends AdminInstructorPage
{	private $act;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'activities';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		
		if (isset($_POST['acttitle']))
		{	$saved = $this->act->Save($_POST, $this->instructor->id);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->act->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->act->Delete())
			{	header('location: instructoracts.php?id=' . $this->instructor->id);
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('instructoracts.php?id=' . $this->instructor->id, 'Activities');
		if ($this->act->id)
		{	$this->breadcrumbs->AddCrumb('instructoract.php?id=' . $this->act->id, $this->InputSafeString($this->act->details['acttitle']));
		} else
		{	$this->breadcrumbs->AddCrumb('instructoract.php?inid=' . $this->instructor->id, 'New activity');
		}
	} // end of fn InstructorConstruct
	
	public function AssignInstructor()
	{	$this->act = new AdminInstructorAct($_GET['id']);
		if ($this->act->id)
		{	$this->instructor  = new AdminInstructor($this->act->details['inid']);
		} else
		{	$this->instructor  = new AdminInstructor($_GET['inid']);
		}
	} // end of fn AssignInstructor
	
	function InstructorBody()
	{	return $this->act->InputForm($this->instructor->id);
	} // end of fn InstructorBody
	
} // end of defn InstructorActivityPage

$page = new InstructorActivityPage();
$page->Page();
?>