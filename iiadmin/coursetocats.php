<?php
include_once("sitedef.php");

class CourseMultiMediaPage extends AdminCourseContentEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('categories');
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'course_cats.js';
		$this->css[] = 'course_mm.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursetocats.php?id=' . $this->course->id, 'Categories');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->CategoriesDisplay();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseMultiMediaPage

$page = new CourseMultiMediaPage();
$page->Page();
?>