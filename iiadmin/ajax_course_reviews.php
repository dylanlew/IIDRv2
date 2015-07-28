<?php
include_once('sitedef.php');

class AjaxCourseReviews extends AdminCourseContentEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('reviews');
		echo $this->course->ReviewsTable();
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxCourseReviews

$page = new AjaxCourseReviews();
?>