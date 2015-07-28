<?php 
require_once('init.php');

class BookingsPage extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct('bookings');
	//	$this->AddBreadcrumb('Course bookings');
		$this->js[] = 'myacbooking.js';
	} // end of fn LoggedInConstruct
	
	function LoggedInMainBody()
	{	echo $this->user->BookedCoursesList();
	} // end of fn LoggedInMainBody
	
} // end of defn BookingPage

$page = new BookingsPage();
$page->Page();
?>