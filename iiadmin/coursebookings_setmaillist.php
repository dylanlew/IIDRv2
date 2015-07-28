<?php
include_once("sitedef.php");

class CourseBookingsSetMailList extends AdminCourseEditPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->course  = new AdminCourse($_GET["course"]);
		if (!$this->user->CanAccessCity($this->course->details["city"]) || !$this->CanAdminUser("site-emails"))
		{	exit;
		}
		$this->breadcrumbs->AddCrumb("coursebookings.php?course={$this->course->id}", "Bookings");
		$this->breadcrumbs->AddCrumb("coursebookings_setemaillist.php?course={$this->course->id}", "Setting email list");
		$this->bodyOnLoadJS[] = "window.location='" . CIT_FULLLINK . "suadmin/siteemails.php';";
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	echo "<p>you will be redirected to the emails ready to send when the list has been built ...</p>";
		$maillist = array();
		if ($this->user->CanUserAccess("course-bookings"))
		{	foreach ($this->course->bookings as $bookingrow)
			{	$bookingrow["user"] = $this->course->UserRowByID($bookingrow["userid"]);
				$bookingrow["attended"] = $this->course->BookingAttended($bookingrow["bookid"]);
				$bookingrow["booking_amountpaid"] = $this->course->BookingAmountPaid($bookingrow["bookid"]);
				if ($this->course->BookingArrayFilterOK($bookingrow, $_GET))
				{	if ($this->ValidEmail($bookingrow["user"]["username"]))
					$maillist[$bookingrow["user"]["userid"]] = array(
										"userid"=>$bookingrow["user"]["userid"], 
										"email"=>$bookingrow["user"]["username"], 
										"name"=>trim($bookingrow["user"]["firstname"] . " " . $bookingrow["user"]["surname"]));
				}
			}
		}
		$_SESSION["adminmailist"] = $maillist;
		//$this->VarDump($maillist);
	} // end of fn CoursesBody
	
} // end of defn CourseBookingsSetMailList

$page = new CourseBookingsSetMailList();
$page->Page();
?>