<?php 
require_once('init.php');

class AjaxMyBookings extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		echo $this->user->BookedCoursesList($_GET['limit'], $_GET['perpage']);
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxMyBookings

$page = new AjaxMyBookings();
?>