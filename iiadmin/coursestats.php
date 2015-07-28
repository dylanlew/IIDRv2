<?php
include_once("sitedef.php");

class CourseStatsPage extends AdminCourseEditPage
{	var $instructor;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct("resources");
		
		$this->css[] = "course_edit.css";
		$this->css[] = "datepicker.css";
		$this->js[] = "datepicker.js";

		$this->breadcrumbs->AddCrumb("courseresources.php?cid={$this->course->id}", "Resources");
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesConstructFunctions()
	{	if ($this->can_stats)
		{	
		}
	} // end of fn CoursesConstructFunctions
	
	function AssignCourse()
	{	$this->course  = new AdminCourse($_GET["cid"]);
	} // end of fn AssignCourse
	
	function CoursesBodyContent()
	{	if ($this->can_stats)
		{	echo $this->course->HeaderInfo(), "<div class='clear'></div>\n", $this->StatsList();
		}
	} // end of fn CoursesBodyContent
	
	function StatsList()
	{	$stats = new BookingsAnalysis();
		$this->VarDump($stats->CourseEnrolmentDaysBefore($this->course->id));
	} // end of fn StatsList
	
} // end of defn CourseStatsPage

$page = new CourseStatsPage();
$page->Page();
?>