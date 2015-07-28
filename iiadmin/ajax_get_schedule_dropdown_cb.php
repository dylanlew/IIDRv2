<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		echo "<!--schedule-->", $this->ScheduleDropDown($_GET["content"]);
		
	} // end of fn CoursesLoggedInConstruct
	
	function GetSchedule($coursecontent = 0)
	{	$schedule = array();
		$sql = "SELECT * FROM courses WHERE coursecontent=" . (int)$coursecontent . " AND endtime>'" . $this->datefn->SQLDateTime() . "' AND live=1 ";
		if ($omitcourse  = (int)$_GET["omitcourse"])
		{	$sql .= "AND NOT cid=$omitcourse ";
		}
		$sql .= "ORDER BY starttime";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($this->user->CanAccessCity($row["city"]))
				{	$course = new Course($row);
					$schedule[$course->id] = $this->CityString($course->details["city"]) . " - " . $course->DateString("j", "M", "y", "-");
				}
			}
		}
		
		return $schedule;
	} // end of fn GetSchedule

	function ScheduleDropDown($contentid = 0)
	{	ob_start();
		if ($schedule = $this->GetSchedule($contentid))
		{	class_exists("Form");
			$select = new FormLineSelect("Schedule", "next_courseid", "", "", $schedule, true, false, "onchange='jsNextGetPrice();'");
			$select->Output();
		} else
		{	echo "<p><label></label><span>Sorry nothing available for this course</span></p>";
		}
		return ob_get_clean();
	} // end of fn ScheduleDropDown
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>