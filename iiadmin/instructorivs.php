<?php
include_once('sitedef.php');

class InstructorInterviewsPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'interviews';
		
		$this->breadcrumbs->AddCrumb('instructorivs.php?id' . $this->instructor->id, 'Interviews');
	} // end of fn InstructorConstruct
	
	function InstructorBody()
	{	return $this->instructor->InterviewsTable();
	} // end of fn InstructorBody
	
} // end of defn InstructorInterviewsPage

$page = new InstructorInterviewsPage();
$page->Page();
?>