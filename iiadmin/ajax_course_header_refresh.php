<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$course = new AdminCourse($_GET["id"]);
		echo "<!--courseheader-->", $course->BookingsSummary();
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>