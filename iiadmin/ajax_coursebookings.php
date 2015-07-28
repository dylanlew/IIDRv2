<?php
include_once("sitedef.php");

class AjaxBookingList extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->course  = new AdminCourse($_GET["course"]);
		echo "<!--booklist-->", $this->course->ListBookingsTable($_GET);
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxBookingList

$page = new AjaxBookingList();
?>