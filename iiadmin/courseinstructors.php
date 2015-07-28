<?php
include_once('sitedef.php');

class CourseInstructorsPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('instructors');
		
		//$this->css[] = 'course_edit.css';
		$this->js[] = 'admin_courseinst.js';

		$this->breadcrumbs->AddCrumb('courseinstructors.php?cid=' . $this->course->id, 'Instructors');
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesConstructFunctions()
	{	if (is_array($_POST['listorder']))
		{	if ($changed = $this->course->SaveInstListOrder($_POST['listorder']))
			{	$this->successmessage = $changed . ' changes saved';
			} else
			{	$this->failmessage = 'no changes saved';
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	echo $this->course->InstructorListContainer();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseInstructorsPage

$page = new CourseInstructorsPage();
$page->Page();
?>