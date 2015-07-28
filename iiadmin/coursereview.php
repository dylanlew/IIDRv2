<?php
include_once("sitedef.php");

class CourseReviewsPage extends AdminCourseContentEditPage
{	var $review;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct('reviews');
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'adminreviews.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		
		if (isset($_POST['review']))
		{	$saved = $this->review->AdminSave($_POST, $this->course->id, 'course');
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		$this->breadcrumbs->AddCrumb('coursereviews.php?id=' . $this->course->id, 'Reviews');
		if ($this->review->id)
		{	$this->breadcrumbs->AddCrumb('coursereview.php?id=' . $this->review->id, 'by ' . $this->InputSafeString($this->review->details['reviewertext']));
		} else
		{	$this->breadcrumbs->AddCrumb('coursereview.php?pid=' . $this->course->id, 'Adding review');
		}
		
	} // end of fn CoursesLoggedInConstruct
	
	function AssignCourse()
	{	$this->review = new AdminProductReview($_GET['id']);
		if ($this->review->id)
		{	$this->course = new AdminCourseContent($this->review->details['pid']);
		} else
		{	$this->course = new AdminCourseContent($_GET['pid']);
		}
	} // end of fn AssignCourse
	
	function CoursesBodyContent()
	{	echo $this->review->AdminInputForm($this->course->id);
	} // end of fn CoursesBodyContent
	
} // end of defn CourseReviewsPage

$page = new CourseReviewsPage();
$page->Page();
?>