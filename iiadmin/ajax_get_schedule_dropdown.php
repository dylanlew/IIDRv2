<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$user = new AdminStudent($_GET["userid"]);
		echo "<!--schedule-->", $user->ScheduleDropDown($_GET["content"]);
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>