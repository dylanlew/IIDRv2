<?php
class AdminCourseEditPage extends AdminCoursesPage
{	var $course;
	var $course_option = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct($course_option = 'edit')
	{	parent::CoursesLoggedInConstruct();
		
		$this->course_option = $course_option;
		$this->css[] = 'admincoursepage.css';
		$this->js[] = 'instructors.js';

		$this->AssignCourse();
		
		$this->CoursesConstructFunctions();
		
		if ($this->course->id)
		{	
			$this->breadcrumbs->AddCrumb('courseedit.php?id=' . $this->course->id, $this->InputSafeString($this->course->content['ctitle']) . ', ' . date('d/m/y', strtotime($this->course->details['starttime'])) . ' - ' . date('d/m/y', strtotime($this->course->details['endtime'])));
		}
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->course = new AdminCourse($_GET['id']);
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	
	} // end of fn CoursesConstructFunctions
	
	function CoursesBody()
	{	$this->CoursesBodyMenu();
		$this->CoursesBodyContent();
	} // end of fn CoursesBody
	
	public function BodyMenuOptions()
	{	$options = array();
		if ($this->course->id)
		{	$options['edit'] = array('link'=>'courseedit.php?id=' . $this->course->id, 'text'=>'Schedule #' . $this->course->id);
			$options['dates'] = array('link'=>'coursedates.php?id=' . $this->course->id, 'text'=>'Dates');
			$options['tickets'] = array('link'=>'coursetickets.php?id=' . $this->course->id, 'text'=>'Tickets');
			$options['galleries'] = array('link'=>'coursegalleries.php?id=' . $this->course->id, 'text'=>'Galleries');
			$options['instructors'] = array('link'=>'courseinstructors.php?id=' . $this->course->id, 'text'=>'Instructors');
			$options['bookings'] = array('link'=>'coursebookings.php?id=' . $this->course->id, 'text'=>'Bookings');
			$options['bookingsperday'] = array('link'=>'coursebookingsperday.php?id=' . $this->course->id, 'text'=>'Bookings per day');
			$options['content'] = array('link'=>'coursecontentedit.php?id=' . $this->course->details['ccid'], 'text'=>'Content');
		}
		return $options;
	} // end of fn BodyMenuOptions
	
	function CoursesBodyMenu()
	{	
		if ($this->course->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->course_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div>';
		}
	} // end of fn CoursesBodyMenu
	
	function CoursesBodyContent()
	{	
	} // end of fn CoursesBodyContent
	
} // end of defn AdminCourseEditPage
?>