<?php
include_once('sitedef.php');

class CourseTicketPage extends AdminCourseEditPage
{	var $ticket;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('tickets');
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';

		$this->breadcrumbs->AddCrumb('coursetickets.php?id=' . $this->course->id, 'Tickets');
		if ($this->ticket->id)
		{	$this->breadcrumbs->AddCrumb('courseticket.php?id=' . $this->ticket->id, $this->InputSafeString($this->ticket->details['tname']));
		} else
		{	$this->breadcrumbs->AddCrumb('courseticket.php?cid=' . $this->course->id, 'new ticket');
		}
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->ticket = new AdminCourseTicket($_GET['id']);
		if ($this->ticket->id && $this->ticket->details['cid'])
		{	$this->course = new AdminCourse($this->ticket->details['cid']);
		} else
		{	$this->course = new AdminCourse($_GET['cid']);
		}
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	if (isset($_POST['tname']))
		{	$saved = $this->ticket->Save($_POST, $this->course->id);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->ticket->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->ticket->Delete())
			{	$this->RedirectBack('coursetickets.php?id=' . $this->course->id);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	echo $this->ticket->InputForm($this->course->id), $this->ticket->BundlesList();
	} // end of fn CoursesBodyContent
	
} // end of defn CourseTicketsPage

$page = new CourseTicketPage();
$page->Page();
?>