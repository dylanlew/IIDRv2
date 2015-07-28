<?php
include_once("sitedef.php");

class AdminBookingPage extends AdminCourseEditPage
{	var $pmt;
	var $booking;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct("bookings");
		
		$this->css[] = "course_edit.css";
		$this->css[] = "datepicker.css";
		$this->js[] = "datepicker.js";

		$this->breadcrumbs->AddCrumb("coursebookings.php?course={$this->course->id}", "Bookings");
		$this->breadcrumbs->AddCrumb("booking.php?id={$this->booking->id}", $this->InputSafeString($this->booking->user->details["firstname"] . " " . $this->booking->user->details["surname"]));
		$this->breadcrumbs->AddCrumb("bookingpmt.php?id={$this->pmt->id}&bookid={$this->booking->id}", $this->pmt->id ? "Edit payment" : "New payment");
		
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->pmt = new AdminBookingPmt($_GET["id"]);
		if ($this->pmt->id)
		{	$this->booking  = new AdminBooking($this->pmt->details["bookid"]);
		} else
		{	$this->booking  = new AdminBooking($_GET["bookid"]);
		}
		$this->course  = $this->booking->course;
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	if (isset($_POST["amount"]))
		{	$saved = $this->pmt->Save($_POST, $this->booking->id);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	$this->RedirectBack("booking.php?id=" . $this->booking->id);
			}
		}
		
		if ($this->pmt->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->pmt->Delete())
			{	$this->RedirectBack("booking.php?id=" . $this->booking->id);
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	echo $this->RedirectBackLink("booking.php?id=" . $this->booking->id);
		$this->booking->BookingInfo();
		echo $this->pmt->InputForm($this->booking);
	} // end of fn CoursesBodyContent
	
} // end of defn AdminBookingPage

$page = new AdminBookingPage();
$page->Page();
?>