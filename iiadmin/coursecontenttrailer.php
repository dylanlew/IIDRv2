<?php
include_once("sitedef.php");

class CourseContentEditPage extends AdminCoursesPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$this->css[] = "course_edit.css";
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "course_tiny_mce.js";

		$this->course  = new AdminCourseContent($_GET["id"]);
		
		$this->breadcrumbs->AddCrumb("coursescontent.php", "Courses");
		$this->breadcrumbs->AddCrumb("coursecontentedit.php?id={$this->course->id}", $this->InputSafeString($this->course->details["ctitle"]));
		$this->breadcrumbs->AddCrumb("coursecontenttrailer.php?id={$this->course->id}", "Trailer");
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	echo $this->course->Header(), stripslashes($this->course->details["trailer"]);
	} // end of fn CoursesBody
	
} // end of defn CourseContentEditPage

$page = new CourseContentEditPage();
$page->Page();
?>