<?php
include_once('sitedef.php');

class InstructorEditPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'edit';
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'instructor_tiny_mce.js';
		
		if (!$this->instructor->id)
		{	$this->breadcrumbs->AddCrumb('instructoredit.php', 'new instructor');
		}
	} // end of fn InstructorConstruct
	
	function ConstructFunctions()
	{	if (isset($_POST['instname']))
		{	$saved = $this->instructor->Save($_POST, $_FILES['imagefile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->instructor->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->instructor->Delete())
			{	header('location: instructors.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	function InstructorBody()
	{	return $this->instructor->InputForm();
	} // end of fn InstructorBody
	
} // end of defn InstructorEditPage

$page = new InstructorEditPage();
$page->Page();
?>