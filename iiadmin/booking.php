<?php
include_once("sitedef.php");

class AdminBookingPage extends AdminCourseEditPage
{	var $booking;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('bookings');
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		$this->js[] = 'adminbookings.js';
		
		$this->breadcrumbs->AddCrumb('coursebookings.php?id=' . $this->course->id, 'Bookings');
		$this->breadcrumbs->AddCrumb('booking.php?id=' . $this->booking->id, $this->InputSafeString($this->booking->student->details['firstname'] . ' ' . $this->booking->student->details['surname']));
		
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->booking = new AdminCourseBooking($_GET['id']);
		$this->course = $this->booking->course;
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	if (isset($_POST['adminnotes']))
		{	$saved = $this->booking->Amend($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->booking->id && $_GET['delete'] && $_GET['confirm'])
		{	$courseid = $this->booking->course->id;
			if ($this->booking->Delete())
			{	$this->RedirectBack('coursebookings.php?id=' . $courseid);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	$this->booking->BookingInfo();
		//echo $this->booking->AttendanceBlock();
		$this->booking->AmendForm();
		//echo $this->booking->PaymentsList();
	} // end of fn CoursesBodyContent
	
} // end of defn AdminBookingPage

$page = new AdminBookingPage();
$page->Page();
?>