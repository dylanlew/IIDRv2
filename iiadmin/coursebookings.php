<?php
include_once('sitedef.php');

class CourseBookingsPage extends AdminCourseEditPage
{	var $sortoptions = array('name'=>'Name', 'sname'=>'Surname', 'date'=>'Date booked');
	var $payoptions = array(''=>'--- any ---', 'full'=>'fully paid only', 'paid'=>'paid at all', 'not'=>'not fully paid', 'none'=>'not paid at all');

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('bookings');
		
		$this->js[] = 'adminbookings.js';
		$this->css[] = 'admincoursebookings.css';

		$this->breadcrumbs->AddCrumb('coursebookings.php?course=' . $this->course->id, 'Bookings');
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBodyContent()
	{	echo $this->CourseHeader(), $this->FilterForm(), $this->course->ListBookings();
	} // end of fn CoursesBodyContent
	
	function FilterForm()
	{	ob_start();
		echo '<form class="akFilterForm" method="get" onsubmit="jsBookingsApplyFilter(', $this->course->id, '); return false;"><div id="cbffInner"><span>Male or Female</span><select name="sex" id="aff_sex">';
		foreach (array(''=>'all', 'M'=>'Male', 'F'=>'Female') as $option=>$text)
		{	echo '<option value="', $option, '">', $text, '</option>';
		}
		echo '</select><span>Name (part)</span><input type="text" name="name" id="aff_name" value="" /><span>Booking ID</span><input type="text" name="bookid" id="aff_bookid" value="" class="number" /><span>sort by</span><select id="aff_sort">';
		foreach ($this->sortoptions as $value=>$text)
		{	echo '<option value="', $value, '">', $this->InputSafeString($text), '</option>';
		}
		echo '</select>';
		if ($this->course->CanAttend())
		{	echo '<br /><span>Attended any of: </span>';
			foreach ($this->course->GetDates(true) as $stamp=>$date)
			{	echo '<span class="akffDates">', date('D<b\r />j/n', $stamp), '<input type="checkbox" id="aff_att', ++$datecount, '" value="', $date, '" /></span>';
			}
		}
		echo '</div><input type="submit" class="submit" value="Apply Filter" /><div class="clear"></div></form><div class="clear"></div>';
		return ob_get_clean();
	} // end of fn FilterForm
	
	function CourseHeader()
	{	ob_start();
		echo $this->course->HeaderInfo();
	/*	if ($this->course->bookings)
		{
			echo '<div id="course_booking_summary"><div class="cb_bs_tabs"><ul><li><a href="#bs_tab_sum">Summary</a></li><li><a href="#bs_tab_next">Next Course</a></li></ul><div class="clear"></div></div><div id="bs_tab_sum">', $this->course->BookingsSummary(), '</div><div id="bs_tab_next">', $this->NextCourseForm(), '</div><script type="text/javascript">my_id_tabs = $(".cb_bs_tabs").idTabs();</script></div>';
		}*/
		echo '<div class="clear"></div>';
		return ob_get_clean();
	} // end of fn CourseHeader
	
/*	function NextCourseForm()
	{	
		echo "<form onsubmit='return false;'>\n<label>Course</label><select id='content_select' onchange='jsNextGetCourses(", $this->course->id, ");'>\n<option value=''>-- select course --</option>\n";
		foreach ($this->GetAvailableCourses(true, $this->user, $this->course->id) as $courseid=>$text)
		{	echo "<option value='", $courseid, "'>", $this->InputSafeString($text), "</option>\n";
		}
		echo "</select><br />\n<div id='nb_schedule'></div>\n<div id='nb_schedule_price'></div>\n</form>\n";
	} // end of fn NextCourseForm*/
	
} // end of defn CourseBookingsPage

$page = new CourseBookingsPage();
$page->Page();
?>