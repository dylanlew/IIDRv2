<?php
include_once("sitedef.php");

class CourseEditPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		
		if ($this->course->id)
		{	$this->breadcrumbs->AddCrumb('courseedit.php?id=' . $this->course->id, 'edit details');
		} else
		{	$this->breadcrumbs->AddCrumb('courseedit.php', 'new schedule');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesConstructFunctions()
	{	if (isset($_POST['cvenue']))
		{	$saved = $this->course->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->course->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->course->Delete())
			{	$this->RedirectBack('courses.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	echo $this->course->InputForm();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseEditPage

$page = new CourseEditPage();
$page->Page();
?>