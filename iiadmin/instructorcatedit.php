<?php
include_once('sitedef.php');

class InstructorCatEditPage extends AdminInstructorPage
{	private $cat;

	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->css[] = 'adminpages.css';
		
		$this->cat = new AdminInstructorCategory($_GET['id']);
		
		if (isset($_POST['catname']))
		{	$saved = $this->cat->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->cat->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->cat->Delete())
			{	header('location: instructorcats.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('instructorcats.php', 'Categories');
		$this->breadcrumbs->AddCrumb('instructorcatedit.php?id=' . (int)$this->cat->id, $this->cat->id ? $this->InputSafeString($this->cat->details['catname']) : 'New category');
	} // end of fn CoursesLoggedInConstruct
	
	public function CoursesBody()
	{	echo $this->cat->InputForm();
	} // end of fn CoursesBody
	
} // end of defn InstructorCatEditPage

$page = new InstructorCatEditPage();
$page->Page();
?>