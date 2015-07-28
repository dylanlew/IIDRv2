<?php
include_once('init.php');
class AjaxCourses extends CourseContentListingPage
{
	function __construct()
	{	parent::__construct();
		
		echo $this->CoursesList();
		
	} // end of fn __construct
	
} // end of defn AjaxCourses

$page = new AjaxCourses();
?>