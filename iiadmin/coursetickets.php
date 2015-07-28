<?php
include_once('sitedef.php');

class CourseTicketsPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('tickets');
		
		$this->css[] = 'course_edit.css';

		$this->breadcrumbs->AddCrumb('coursetickets.php?id=' . $this->course->id, 'Tickets');
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->TicketsList();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseTicketsPage

$page = new CourseTicketsPage();
$page->Page();
?>