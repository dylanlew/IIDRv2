<?php
include_once("sitedef.php");

class CourseInstructorPage extends AdminCourseEditPage
{	var $instructor;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct("instructors");
		
		$this->css[] = "course_edit.css";

		$this->breadcrumbs->AddCrumb("courseinstructors.php?cid={$this->course->id}", "Instructors");
		$this->breadcrumbs->AddCrumb("courseinstructor.php?cid={$this->course->id}&inid={$this->instructor->id}", $this->instructor->id ? ($this->InputSafeString($this->instructor->details["instname"])) : "Add new");
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->course  = new AdminCourse($_GET["cid"]);
		$this->instructor  = new AdminInstructor($_GET["inid"]);
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	if ($this->can_schedule)
		{	if ($this->instructor->id && $_GET["remove"])
			{	if ($this->course->RemoveInstructor($this->instructor->id))
				{	$this->RedirectBack("courseinstructors.php?cid=" . $this->course->id);
				}
			}
			
			if (!$this->instructor->id && $_POST["instructor"])
			{	if ($this->course->AddInstructor($_POST["instructor"]))
				{	$this->RedirectBack("courseinstructors.php?cid=" . $this->course->id);
				}
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	if ($this->can_schedule)
		{	
			echo $this->course->HeaderInfo(), "<div class='clear'></div>\n", $this->instructor->id ? $this->RemoveInfo() : $this->AddInstructorForm();
		}
	} // end of fn CoursesBodyContent
	
	function RemoveInfo()
	{	ob_start();
		echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?cid=", $this->course->id, "&inid=", $this->instructor->id, "&remove=1'>Remove ", $this->InputSafeString($this->instructor->details["instname"]), " from this course</a></p>";
		if (file_exists($this->instructor->ThumbFile()))
		{	echo "<p><img src='", $this->instructor->ThumbSRC(), "' /></p>\n";
		}
		echo "<h3>", $this->InputSafeString($this->instructor->details["instname"]), "</h3>\n<div>", stripslashes($this->instructor->details['instdesc']), "</div>";
		return ob_get_clean();
	} // end of fn RemoveInfo
	
	function AddInstructorForm()
	{	ob_start();
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?cid=" . $this->course->id, "");
		$form->AddSelect("New instructor to add", "instructor", "", "", $this->InstructorList("instname", true), true);
		$form->AddSubmitButton("", "Add Instructor", "submit");
		$form->Output();
		return ob_get_clean();
	} // end of fn AddInstructorForm
	
	function InstructorList($field = "array", $liveonly = false, $extra_inst = 0)
	{	$instlist = array();
		$sql = "SELECT inid, instname FROM instructors WHERE live=1 ORDER BY instname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$this->course->instructors[$row["inid"]])
				{	$instlist[$row["inid"]] = $row["instname"];
				}
			}
		}
		return $instlist;
	} // end of fn InstructorList
	
} // end of defn CourseInstructorPage

$page = new CourseInstructorPage();
$page->Page();
?>