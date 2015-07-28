<?php
include_once("sitedef.php");

class CourseMultiMediaPage extends AdminCourseEditPage
{	private $date;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('dates');
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		
		if (isset($_POST['timetext']))
		{	$saved = $this->date->Save($_POST, $this->course->id);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->date->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->date->Delete())
			{	header('location: coursedates.php?id=' . $this->course->id);
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('coursedates.php?id=' . $this->course->id, 'Dates');
		if ($this->date->id)
		{	$this->breadcrumbs->AddCrumb('coursedate.php?id=' . $this->date->id, date('d/m/y', strtotime($this->date->details['startdate'])) . ' to ' . date('d/m/y', strtotime($this->date->details['enddate'])));
		} else
		{	$this->breadcrumbs->AddCrumb('coursedate.php?cid=' . $this->course->id, 'Adding new');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->date = new AdminCourseDate($_GET['id']);
		if ($this->date->id)
		{	$this->course = new AdminCourse($this->date->details['cid']);
		} else
		{	$this->course = new AdminCourse($_GET['cid']);
		}
	} // end of fn AssignCourse
	
	function CoursesBodyContent()
	{	echo $this->date->InputForm($this->course->id);
	} // end of fn CoursesBodyContent
	
} // end of defn CourseMultiMediaPage

$page = new CourseMultiMediaPage();
$page->Page();
?>