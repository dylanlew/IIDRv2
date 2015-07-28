<?php
class AdminCoursesPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('COURSES');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('courses'))
		{	$this->CoursesLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function CoursesLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('courses.php', 'Courses');
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	
	} // end of fn CoursesBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('courses'))
		{	$this->CoursesBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminCoursesPage
?>