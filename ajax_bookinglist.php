<?php 
require_once('init.php');

class AjaxCourseBookings extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct('bookings');
		echo $this->user->BookedCoursesListTable();
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxCourseBookings

$page = new AjaxCourseBookings();
?>