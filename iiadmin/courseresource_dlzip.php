<?php
include_once("sitedef.php");

class CourseResourceDownloadZip extends AdminCourseEditPage
{	var $course;
	var $resource;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	//$this->VarDump($this->resource->details);
		if ($this->course->resources && $this->can_resources)
		{	
			$zip = new ZipDownload();
			foreach ($this->course->resources as $resourcerow)
			{	$resource = new CourseResource($resourcerow);
				$zip->AddToZipFiles($resource->FileLocation(), $resource->FileName());
			}
			if ($zip->DownloadZipFile('course_resources_' . $this->course->id . '.zip'))
			{	exit;
			} else
			{	echo 'download failed';
			}
		} else
		{	echo "file not available";
		}
	} // end of fn CoursesBody
	
} // end of defn CourseResourceDownloadZip

$page = new CourseResourceDownloadZip();
$page->AdminBodyMain();
?>