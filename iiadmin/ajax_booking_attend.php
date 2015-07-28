<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$booking = new AdminBooking($_GET["id"]);
		if ($booking->id && $booking->course->CanAttend())
		{	$sql = "UPDATE bookings SET attended=1 WHERE bookid=" . $booking->id;
			if ($result = $this->db->Query($sql))
			{	$booking->Get($booking->id);
				echo "<!--payattend-->", $booking->BookingListAttendContents();
			}
			
		}
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>