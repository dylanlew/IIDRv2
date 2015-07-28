<?php
include_once('sitedef.php');

class AjaxPayBooking extends AdminCoursesPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$booking = new AdminCourseBooking($_GET['id']);
		$course = new AdminCourse($booking->details['course']);
		if ($booking->id && $course->CanAttend())
		{	
			$booking->RecordAttendance($_GET['date'], $_GET['attend']);
			$booking->details['attended'] = $booking->attendance;
			echo '<!--attend-->', $course->AttendanceDateLink($booking->details, $_GET['date']);
		}
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>