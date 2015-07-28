<?php
include_once("sitedef.php");

class GalleryPage extends AdminGalleryPage
{	private $gallery;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function GalleryConstruct()
	{	parent::GalleryConstruct();
		$this->gallery = new AdminGallery($_GET['id']);
		
		if (isset($_POST['title']))
		{	$saved = $this->gallery->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->gallery->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->gallery->Delete())
			{	$this->RedirectBack('galleries.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('gallery.php?id=' . $this->gallery->id, $this->gallery->id ? $this->InputSafeString($this->gallery->details['title']) : 'new gallery');
		
	} // end of fn GalleryConstruct
	
	protected function GalleryMainContent()
	{	ob_start();
		echo $this->gallery->InputForm();
		return ob_get_clean();
	} // end of fn GalleryMainContent
	
} // end of defn GalleryPage

$page = new GalleryPage();
$page->Page();
?>