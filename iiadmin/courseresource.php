<?php
include_once("sitedef.php");

class CourseResourceEditPage extends AdminCourseEditPage
{	var $resource;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct("resources");

		$this->breadcrumbs->AddCrumb("courseresources.php?cid={$this->course->id}", "Resources");
		if ($this->resource->id)
		{	$this->breadcrumbs->AddCrumb("courseresource.php?id={$this->resource->id}", $this->InputSafeString($this->resource->details["crlabel"]));
		} else
		{	$this->breadcrumbs->AddCrumb("courseresource.php?cid={$this->course->id}", "Add new");
		}
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->resource = new AdminCourseResource($_GET["id"]);
		if ($this->resource->id)
		{	$this->course  = new AdminCourse($this->resource->details["cid"]);
		} else
		{	$this->course  = new AdminCourse($_GET["cid"]);
		}
	} // end of fn AssignCourse
	
	function CoursesConstructFunctions()
	{	if ($this->can_resources)
		{	if (isset($_POST["crlabel"]))
			{	$saved = $this->resource->Save($this->course->id, $_POST, $_FILES["crfile"]);
				$this->successmessage = $saved["successmessage"];
				$this->failmessage = $saved["failmessage"];
				if ($this->successmessage && !$this->failmessage)
				{	$this->Redirect("courseresources.php?cid=" . $this->course->id);
				}
			}
			
			if ($this->resource->id && $_GET["delete"] && $_GET["confirm"])
			{	$courseid = $this->resource->details["cid"];
				if ($this->resource->Delete())
				{	$this->Redirect("courseresources.php?cid=" . $this->course->id);
				} else
				{	$this->failmessage = "Delete failed";
				}
			}
		}
	} // end of fn CoursesConstructFunctions
	
	function CoursesBodyContent()
	{	if ($this->can_resources)
		{	echo $this->course->HeaderInfo(), "<div class='clear'></div>\n", $this->resource->InputForm($this->course->id);
		}
	} // end of fn CoursesBodyContent
	
} // end of defn CourseResourceEditPage

$page = new CourseResourceEditPage();
$page->Page();
?>