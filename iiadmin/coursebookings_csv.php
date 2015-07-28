<?php
include_once("sitedef.php");

class CourseBookingsCSV extends AdminCoursesPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->course  = new AdminCourse($_GET["id"]);
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	
		header("Pragma: ");
		header("Cache-Control: ");
		header("Content-Type: application/csv;charset=UTF-8");
		header("Content-Disposition: attachment; filename=\"iidr_bookings.csv\"");
		echo "firstname,surname,email,phone,address,postcode,city,date of birth,enroldate,";
		if ($canattend = $this->course->CanAttend())
		{	foreach ($dates = $this->course->GetDates() as $stamp=>$date)
			{	echo date("D", $stamp), ",";
			}
		}
		echo "price,price with tax,notes\n";
		foreach ($this->course->GetBookings() as $bookingrow)
		{	$bookingrow["user"] = $this->course->UserRowByID($bookingrow["student"]);
			$bookingrow["attended"] = $this->course->BookingAttended($bookingrow["id"]); //array();
			$order = $this->course->GetOrderFromBooking($bookingrow);
			$orderitem = $this->course->GetOrderItemFromBooking($bookingrow);
			if ($this->course->BookingArrayFilterOK($bookingrow, $_GET))
			{	$phones = array();
				if ($bookingrow["user"]["phone"])
				{	$phones[] = stripslashes($bookingrow["user"]["phone"]);
				}
				if ($bookingrow["user"]["phone2"])
				{	$phones[] = stripslashes($bookingrow["user"]["phone2"]);
				}
				echo "\"", $this->CSVSafeString($bookingrow["user"]["firstname"]), "\",\"", $this->CSVSafeString($bookingrow["user"]["surname"]), "\",\"", $this->CSVSafeString($bookingrow["user"]["username"]), "\",\"", implode(", ", $phones), "\",\"", $this->CSVSafeString($bookingrow["user"]["address"]), "\",\"", $this->CSVSafeString($bookingrow["user"]["postcode"]), "\",\"", $this->CSVSafeString($bookingrow["user"]["city"]), "\",", date("d/m/Y", strtotime($bookingrow["user"]["dob"])), ",", date("d/m/Y", strtotime($order['orderdate'])), ",";
				if ($canattend)
				{	foreach ($dates as $stamp=>$date)
					{	echo $bookingrow["attended"][$date] ? "\"Yes\"" : "\"No\"", ",";
					}
				}
				echo $orderitem["price"], ",", $orderitem["pricetax"], ",\"", $this->CSVSafeString($bookingrow["adminnotes"]), "\"\n";
			}
		}
	} // end of fn CoursesBody
	
} // end of defn CourseBookingsCSV

$page = new CourseBookingsCSV();
$page->AdminBodyMain();
?>