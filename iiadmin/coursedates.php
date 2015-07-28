<?php
include_once("sitedef.php");

class CourseMultiMediaPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('dates');
		
		$this->css[] = 'course_edit.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursedates.php?id=' . $this->course->id, 'Dates');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->DatesTable();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseMultiMediaPage

$page = new CourseMultiMediaPage();
$page->Page();
?>