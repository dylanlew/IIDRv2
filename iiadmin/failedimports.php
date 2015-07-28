<?php
include_once("sitedef.php");

class CourseBookingsPage extends AdminCoursesPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$this->js[] = "adminbookings.js";
		$this->css[] = "admincoursebookings.css";

		$this->course  = new AdminCourse($_GET["course"]);
		
		if (!$this->user->CanAccessCity($this->course->details["city"]))
		{	header("location: courses.php");
			exit;
		}

		$this->breadcrumbs->AddCrumb("courses.php", "Course schedule");
		$this->breadcrumbs->AddCrumb("coursecontentedit.php?id={$this->course->details["coursecontent"]}", $this->InputSafeString($this->course->content->details["ctitle"]));
		$this->breadcrumbs->AddCrumb("courseedit.php?id={$this->course->id}", $this->CityString($this->course->details["city"]) . ", " . $this->course->DateString("j", "M", "y", "-"));
		$this->breadcrumbs->AddCrumb("coursebookings.php?course={$this->course->id}", "Bookings");
		$this->breadcrumbs->AddCrumb("failedimports.php?course={$this->course->id}", "Failed imports");
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	echo $this->RedirectBackLink("courseedit.php?id={$this->course->id}"), $this->Header(), $this->course->ListFailedImports();
	} // end of fn CoursesBody
	
	function Header()
	{	ob_start();
		echo $this->course->HeaderInfo(), "<div id='course_booking_summary'>\n<div id='bs_tab_sum'>", $this->course->BookingsSummary(), "</div>\n</div>\n<div class='clear'></div>\n";
		return ob_get_clean();
	} // end of fn Header
	
} // end of defn CourseBookingsPage

$page = new CourseBookingsPage();
$page->Page();
?>