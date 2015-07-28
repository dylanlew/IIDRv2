<?php
include_once('sitedef.php');

class CourseCatEditPage extends AdminCoursesPage
{	private $cat;

	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->css[] = 'adminpages.css';
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'pageedit_tiny_mce.js';
		$this->js[] = 'admin_bannerselect.js';
		
		$this->cat = new AdminCourseCategory($_GET['id']);
		
		if (isset($_POST['ctitle']))
		{	$saved = $this->cat->Save($_POST, $_FILES['bgfile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->cat->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->cat->Delete())
			{	header('location: coursecats.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('coursecats.php', 'Categories');
		$this->breadcrumbs->AddCrumb('coursecatedit.php?id=' . (int)$this->cat->id, $this->cat->id ? $this->InputSafeString($this->cat->details['ctitle']) : 'New category');
	} // end of fn CoursesLoggedInConstruct
	
	public function CoursesBody()
	{	echo $this->cat->InputForm();
	} // end of fn CoursesBody
	
} // end of defn CourseCatEditPage

$page = new CourseCatEditPage();
$page->Page();
?>