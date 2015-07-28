<?php
include_once("sitedef.php");

class CourseMultiMediaPage extends AdminCourseContentEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('multimedia');
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'course_mm.js';
		$this->css[] = 'course_mm.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursemultimedia.php?id=' . $this->course->id, 'Multimedia');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->MultiMediaDisplay();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseMultiMediaPage

$page = new CourseMultiMediaPage();
$page->Page();
?>