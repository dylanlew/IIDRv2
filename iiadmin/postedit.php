<?php
include_once('sitedef.php');

class PostEditPage extends AdminPostsPage
{
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function PostLoggedInConstruct()
	{	parent::PostLoggedInConstruct('edit');
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'pageedit_tiny_mce.js';
		
		if (!$this->post->id)
		{	$this->breadcrumbs->AddCrumb('postedit.php', 'new post');
		}
	} // end of fn PostLoggedInConstruct

	protected function ConstructFunctions()
	{	parent::ConstructFunctions();
		if ($_POST['ptitle'])
		{	$saved = $this->post->Save($_POST, $_FILES['imagefile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($_GET['delete'] && $_GET['confirm'])
		{	if ($this->post->Delete())
			{	header('location: posts.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	protected function PostBodyMain()
	{	parent::PostBodyMain();
		echo $this->post->InputForm();
	} // end of fn PostBodyMain
	
} // end of defn PostEditPage

$page = new PostEditPage();
$page->Page();
?>