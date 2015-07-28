<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		echo "<!--bookform-->", $this->BookForm();
		
	} // end of fn CoursesLoggedInConstruct
	
	function BookForm()
	{	ob_start();
		//print_r($_GET);
		$course = new AdminCourse($_GET["courseid"]);
		$user = new AdminStudent($_GET["userid"]);
		$price = round($_GET["price"], 2);
		if ($user->AlreadyBooked($course->id))
		{	
			echo "<div class='nb_error'>", $this->InputSafeString($user->details["firstname"]), " ", $this->InputSafeString($user->details["surname"]), " is already booked on this course (", $this->InputSafeString($course->content->details["ctitle"]), ", ", $course->OutputLocation(), " - ", $course->DateString(), ")</div>";
		} else
		{
			echo "<form class='sb_form' onsubmit='jsSaveBooking();return false;'>\n<input type='hidden' id='sb_userid' value='", $user->id, "' /><input type='hidden' id='sb_courseid' value='", $course->id, "' />\n<label>Booking user ...</label><span>", $this->InputSafeString($user->details["firstname"]), " ", $this->InputSafeString($user->details["surname"]), " (email: ", $user->details["username"], ")</span><br />\n<label>... on course</label><span>", $this->InputSafeString($course->content->details["ctitle"]), ", ", $course->OutputLocation(), " - ", $course->DateString(), "</span><br />\n<label></label><input type='text' class='short' id='sb_price' value='", number_format($price, 2), "' /><br />\n<label>&nbsp;</label><input type='submit' class='submit' value='Create Booking' /><br />\n</form>\n";
		}
		return ob_get_clean();
	} // end of fn BookForm
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>