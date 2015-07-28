<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		echo "<!--price-->", $this->GetPrice($_GET["courseid"]);
		
	} // end of fn CoursesLoggedInConstruct
	
	function GetPrice($courseid = 0)
	{	ob_start();
		$course = new Course($courseid);
		if ($course->id)
		{	echo "<label>Price</label><span class='original_price'>", $course->CurrencySymbol(), number_format($course->details["price"], 2), "</span><br />\n<label>Today's price</label><input class='short' value='", number_format($course->details["price"], 2, ".", ""), "' id='next_price' /><br />\n";
		}
		
		return ob_get_clean();
	} // end of fn GetSchedule
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>