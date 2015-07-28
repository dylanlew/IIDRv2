<?php 
require_once('init.php');

class CategoryPage extends BasePage
{	private $cat;
	private $courses = array();
	
	function __construct($course = 0)
	{	parent::__construct('courses');

		$this->cat = new CourseCategory($course);
		if (!$this->cat->id || !$this->courses = $this->cat->GetCourses(true))
		{	$this->Redirect('courses.php');
		}
		$this->title .= ' - ' . $this->InputSafeString($this->cat->details['ctitle']);
		
		$this->AddBreadcrumb('Courses', $this->link->GetLink('courses.php'));
		$this->AddBreadcrumb($this->InputSafeString($this->cat->details['ctitle']));
	} // end of fn __construct
	
	function MainBodyContent()
	{
		$this->VarDump($this->cat->details);
	} // end of fn MainBodyContent
	
} // end of defn CategoryPage

$page = new CategoryPage($_GET['id']);
$page->Page();
?>