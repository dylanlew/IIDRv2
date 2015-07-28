<?php
include_once("sitedef.php");

class CourseReviewsPage extends AdminCourseContentEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('reviews');
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'admin_coursereviews.js';
		$this->css[] = 'adminreviews.css';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('coursereviews.php?id=' . $this->course->id, 'Reviews');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->ReviewsDisplay();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseReviewsPage

$page = new CourseReviewsPage();
$page->Page();
?>