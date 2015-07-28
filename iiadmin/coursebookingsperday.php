<?php
include_once('sitedef.php');

class CourseBookingsPerDayPage extends AdminCourseEditPage
{	var $sortoptions = array('name'=>'Name', 'sname'=>'Surname', 'date'=>'Date booked');
	var $payoptions = array(''=>'--- any ---', 'full'=>'fully paid only', 'paid'=>'paid at all', 'not'=>'not fully paid', 'none'=>'not paid at all');

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('bookingsperday');
		
		$this->js[] = 'adminbookings.js';
		$this->css[] = 'admincoursebookings.css';

		$this->breadcrumbs->AddCrumb('coursebookingsperday.php?course=' . $this->course->id, 'Bookings per day');
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->course->ListBookingsPerDay();
	} // end of fn CoursesBodyContent
	
	function CourseHeader()
	{	ob_start();
		echo $this->course->HeaderInfo();
		return ob_get_clean();
	} // end of fn CourseHeader
	
} // end of defn CourseBookingsPage

$page = new CourseBookingsPerDayPage();
$page->Page();
?>