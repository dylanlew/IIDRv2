<?php
include_once('sitedef.php');

class InstructorMMPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'multimedia';
		$this->js[] = 'admin_instructor_mm.js';
		$this->css[] = 'course_mm.css';
		
		$this->breadcrumbs->AddCrumb('instructormm.php?id' . $this->instructor->id, 'Multimedia');
	} // end of fn InstructorConstruct
	
	function InstructorBody()
	{	return $this->instructor->MultiMediaDisplay();
	} // end of fn InstructorBody
	
} // end of defn InstructorMMPage

$page = new InstructorMMPage();
$page->Page();
?>