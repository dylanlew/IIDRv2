<?php
class AdminInstructorPage extends AdminCourseContentPage
{	var $instructor;
	public $inst_option = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->InstructorConstruct();
	} // end of fn CoursesLoggedInConstruct
	
	public function InstructorConstruct()
	{	$this->AssignInstructor();
		$this->ConstructFunctions();
		$this->css[] = 'admincoursepage.css';
		$this->breadcrumbs->AddCrumb('instructors.php', 'Instructors');
		if ($this->instructor->id)
		{	$this->breadcrumbs->AddCrumb('instructoredit.php?id=' . $this->instructor->id, $this->InputSafeString($this->instructor->details['instname']));
		}
	} // end of fn InstructorConstruct
	
	public function ConstructFunctions()
	{	
	} // end of fn ConstructFunctions
	
	public function AssignInstructor()
	{	$this->instructor  = new AdminInstructor($_GET['id']);
	} // end of fn AssignInstructor
	
	function CoursesBody()
	{	echo $this->InstructorBodyMenu(), $this->InstructorBody();
	} // end of fn CoursesBody
	
	function InstructorBody()
	{	
	} // end of fn InstructorBody
	
	function InstructorBodyMenu()
	{	ob_start();
		if ($this->instructor->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->inst_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div>';
		}
		return ob_get_clean();
	} // end of fn InstructorBodyMenu
	
	public function BodyMenuOptions()
	{	$options = array();
		if ($this->instructor->id)
		{	$options['edit'] = array('link'=>'instructoredit.php?id=' . $this->instructor->id, 'text'=>'Instructor');
			$options['multimedia'] = array('link'=>'instructormm.php?id=' . $this->instructor->id, 'text'=>'Multimedia');
			$options['galleries'] = array('link'=>'instructorgalleries.php?id=' . $this->instructor->id, 'text'=>'Galleries');
			$options['reviews'] = array('link'=>'instructorreviews.php?id=' . $this->instructor->id, 'text'=>'Reviews');
			$options['activities'] = array('link'=>'instructoracts.php?id=' . $this->instructor->id, 'text'=>'Activities');
			$options['interviews'] = array('link'=>'instructorivs.php?id=' . $this->instructor->id, 'text'=>'Interviews');
		}
		return $options;
	} // end of fn BodyMenuOptions
	
} // end of defn AdminInstructorPage
?>