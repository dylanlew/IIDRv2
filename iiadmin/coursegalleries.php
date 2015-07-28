<?php
include_once("sitedef.php");

class CourseMultiMediaPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('galleries');
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'admin_coursegallery.js';
		$this->css[] = 'course_mm.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursegalleries.php?id=' . $this->course->id, 'Galleries');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->GalleriesDisplay();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseMultiMediaPage

$page = new CourseMultiMediaPage();
$page->Page();
?>