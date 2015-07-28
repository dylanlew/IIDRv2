<?php
include_once('sitedef.php');

class InstructorReview extends AdminInstructorPage
{	var $review;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function InstructorConstruct()
	{	parent::InstructorConstruct('reviews');
		$this->inst_option = 'reviews';
		
		$this->css[] = 'course_edit.css';
		$this->css[] = 'adminreviews.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		
		if (isset($_POST['review']))
		{	$saved = $this->review->AdminSave($_POST, $this->instructor->id, 'instructor');
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->review->CanDelete() && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->review->Delete())
			{	header('location: instructorreviews.php?id=' . $this->instructor->id);
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('instructorreviews.php?id' . $this->instructor->id, 'Reviews');
		if ($this->review->id)
		{	$this->breadcrumbs->AddCrumb('instructorreview.php?id=' . $this->review->id, 'by ' . $this->InputSafeString($this->review->details['reviewertext']));
		} else
		{	$this->breadcrumbs->AddCrumb('instructorreview.php?pid=' . $this->instructor->id, 'Adding review');
		}
		
	} // end of fn InstructorConstruct
	
	function AssignInstructor()
	{	$this->review = new AdminProductReview($_GET['id']);
		if ($this->review->id)
		{	$this->instructor = new AdminInstructor($this->review->details['pid']);
		} else
		{	$this->instructor = new AdminInstructor($_GET['pid']);
		}
	} // end of fn AssignInstructor
	
	function InstructorBody()
	{	echo $this->review->AdminInputForm($this->instructor->id);
	} // end of fn InstructorBody
	
} // end of defn InstructorReview

$page = new InstructorReview();
$page->Page();
?>