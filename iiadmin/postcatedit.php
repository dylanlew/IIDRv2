<?php
include_once('sitedef.php');

class PostCatEditPage extends AdminPostsPage
{	private $cat;

	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function PostLoggedInConstruct()
	{	parent::PostLoggedInConstruct();
		
		$this->cat = new AdminPostCategory($_GET['id']);
		
		if (isset($_POST['ctitle']))
		{	$saved = $this->cat->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->cat->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->cat->Delete())
			{	header('location: postcats.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('postcats.php', 'Categories');
		$this->breadcrumbs->AddCrumb('postcatedit.php?id=' . (int)$this->cat->id, $this->cat->id ? $this->InputSafeString($this->cat->details['ctitle']) : 'New category');
	} // end of fn PostLoggedInConstruct
	
	public function PostBodyMain()
	{	echo $this->cat->InputForm();
	} // end of fn PostBodyMain
	
} // end of defn PostCatEditPage

$page = new PostCatEditPage();
$page->Page();
?>