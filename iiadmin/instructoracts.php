<?php
include_once('sitedef.php');

class InstructorActivitiesPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'activities';
		
		$this->breadcrumbs->AddCrumb('instructoracts.php>id' . $this->instructor->id, 'Activities');
	} // end of fn InstructorConstruct
	
	function InstructorBody()
	{	return $this->instructor->ActivitiesTable();
	} // end of fn InstructorBody
	
} // end of defn InstructorActivitiesPage

$page = new InstructorActivitiesPage();
$page->Page();
?>