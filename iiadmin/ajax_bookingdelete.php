<?php
include_once("sitedef.php");

class AjaxBookingDelete extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		echo $this->DeleteForm();
		
	} // end of fn CoursesLoggedInConstruct
	
	function DeleteForm()
	{	ob_start();
		$booking = new AdminBooking($_GET["bookid"]);
		
		if ($_GET["confirm"])
		{	if ($booking->Delete())
			{	echo "1|~|booking deleted";
			} else
			{	echo "0|~|<!--bookdelete-->sorry, this booking cannot be deleted";
			}
			
		} else
		{	echo "0|~|<!--bookdelete--><p>Please confirm you want to delete ", $this->InputSafeString($booking->user->details["firstname"] . " " . $booking->user->details["surname"]), "'s booking</p><p><a class='bdConfirm' onclick='BookingDeletePopUp(", $booking->details["course"], ",", $booking->id, ", true);'>Confirm Deletion</a></p>";
		}
		
		return ob_get_clean();
	} // end of fn DeleteForm
	
} // end of defn AjaxBookingDelete

$page = new AjaxBookingDelete();
?>