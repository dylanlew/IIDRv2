<?php
class AdminCourseSchedulePage extends AdminPage
{	
	function __construct()
	{	parent::__construct("COURSES");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("course-schedule"))
		{	$this->CoursesLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function CoursesLoggedInConstruct()
	{	
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	
	} // end of fn CoursesBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("course-schedule"))
		{	$this->CoursesBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminCourseSchedulePage
?>