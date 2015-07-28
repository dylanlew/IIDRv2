<?php
include_once('sitedef.php');

class AdminBookingAjax extends AdminCourseEditPage
{	var $booking;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('bookings');
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->booking = new AdminCourseBooking($_GET['id']);
		$this->course = $this->booking->course;
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	
		switch ($_GET['action'])
		{	case 'popup':
				echo $this->booking->TransferBookingForm();
				break;
			case 'courseselect':
				echo $this->booking->TransferBookingFormTickets($_GET['course']);
				exit;
			case 'submit':
				if ($this->booking->TransferBooking($_GET['course'], $_GET['ticket']))
				{	echo '<div class="xferSuccess">Course transfer succeeded, refresh page to see results</div>';
				} else
				{	echo '<div class="xferError">Coures transfer failed</div>', $this->booking->TransferBookingForm($_GET['course']);
				}
				exit;
		}
		
	} // end of fn CoursesConstructFunctions
	
} // end of defn AdminBookingAjax

$page = new AdminBookingAjax();
?>