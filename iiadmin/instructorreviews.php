<?php
include_once('sitedef.php');

class InstructorReviewsPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'reviews';
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'admin_coursereviews.js';
		$this->css[] = 'adminreviews.css';
		
		$this->breadcrumbs->AddCrumb('instructorreviews.php?id' . $this->instructor->id, 'Reviews');
	} // end of fn InstructorConstruct
	
	function InstructorBody()
	{	return $this->instructor->ReviewsDisplay();
	} // end of fn InstructorBody
	
} // end of defn InstructorReviewsPage

$page = new InstructorReviewsPage();
$page->Page();
?>