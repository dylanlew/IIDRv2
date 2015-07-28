<?php
include_once("sitedef.php");

class CourseEditPage extends AdminCourseEditPage
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
	{	if ($this->can_resources)
		{	if (isset($_POST["dresend"]))
			{	$saved = $this->course->SaveResourceDate($_POST);
				$this->successmessage = $saved["successmessage"];
				$this->failmessage = $saved["failmessage"];
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function AssignCourse()
	{	$this->course  = new AdminCourse($_GET["cid"]);
	} // end of fn AssignCourse
	
	function CoursesBodyContent()
	{	if ($this->can_resources)
		{	echo $this->course->HeaderInfo(), "<div class='clear'></div>\n", $this->course->ResourceDateInputForm(), $this->course->ListResources();
		}
	} // end of fn CoursesBodyContent
	
} // end of defn CourseEditPage

$page = new CourseEditPage();
$page->Page();
?>