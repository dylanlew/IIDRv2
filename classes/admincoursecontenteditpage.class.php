<?php
class AdminCourseContentEditPage extends AdminCoursesPage
{	var $course;
	var $course_option = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct($course_option = 'edit')
	{	parent::CoursesLoggedInConstruct();
		
		$this->course_option = $course_option;
		$this->css[] = 'admincoursepage.css';

		$this->AssignCourse();
		
		$this->CoursesConstructFunctions();
		
		$this->breadcrumbs->AddCrumb('coursescontent.php', 'Content');
		if ($this->course->id)
		{	
			$this->breadcrumbs->AddCrumb('coursecontentedit.php?id=' . $this->course->id, $this->InputSafeString($this->course->details['ctitle']));
		}
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->course = new AdminCourseContent($_GET['id']);
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
		{	$options['edit'] = array('link'=>'coursecontentedit.php?id=' . $this->course->id, 'text'=>'Course #' . $this->course->id);
			$options['schedule'] = array('link'=>'coursecontentschedule.php?id=' . $this->course->id, 'text'=>'Schedule');
			$options['categories'] = array('link'=>'coursetocats.php?id=' . $this->course->id, 'text'=>'Categories');
			$options['multimedia'] = array('link'=>'coursemultimedia.php?id=' . $this->course->id, 'text'=>'Multimedia');
			$options['reviews'] = array('link'=>'coursereviews.php?id=' . $this->course->id, 'text'=>'Reviews');
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
	
} // end of defn AdminCourseContentEditPage
?>