<?php
include_once('sitedef.php');

class CourseSchedulePage extends AdminCourseContentEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('schedule');
		
		$this->css[] = 'course_edit.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursecontentschedule.php?id=' . $this->course->id, 'Schedule');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->ScheduleListing();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseSchedulePage

$page = new CourseSchedulePage();
$page->Page();
?>