<?php
include_once("sitedef.php");

class CourseResourceDownload extends AdminCoursesPage
{	var $course;
	var $resource;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();

		$this->resource = new AdminCourseResource($_GET["id"]);
		$this->course  = new AdminCourse($this->resource->details["cid"]);
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	//$this->VarDump($this->resource->details);
		if ($this->resource->ResourceAvailableForUser())
		{	if ($fhandle = fopen($this->resource->FileLocation(), "r"))
			{	if (($contenttype = $this->resource->HeaderContentType()) && ($filename = $this->resource->FileName()))
				{	header("Pragma: ");
					header("Cache-Control: ");
					header("Content-Type: $contenttype");
					header("Content-Disposition: attachment; filename=\"$filename\"");
					fpassthru($fhandle);
					fclose($fhandle);
					return true;
				}
			}
		}
		// default if nothing
		echo "file not available";
	} // end of fn CoursesBody
	
} // end of defn CourseResourceDownload

$page = new CourseResourceDownload();
$page->AdminBodyMain();
?>