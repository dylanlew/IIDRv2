<?php
include_once("sitedef.php");

class GalleryPhotoPage extends AdminGalleryPage
{	private $gallery;
	private $photo;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function GalleryConstruct()
	{	parent::GalleryConstruct();
		$this->photo = new AdminGalleryPhoto($_GET['id']);
		
		if (isset($_POST['title']))
		{	$saved = $this->photo->Save($_POST, $_FILES['photofile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->photo->id && $_GET['delete'] && $_GET['confirm'])
		{	$gallery_id = $this->photo->details['gid'];
			if ($this->photo->Delete())
			{	$this->RedirectBack('gallery.php?id=' . $gallery_id);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		if ($this->photo->id)
		{	$this->gallery = new AdminGallery($this->photo->details['gid']);
		} else
		{	$this->gallery = new AdminGallery($_GET['gid']);
		}
		
		$this->breadcrumbs->AddCrumb('gallery.php?id=' . $this->gallery->id, $this->InputSafeString($this->gallery->details['title']));
		$this->breadcrumbs->AddCrumb('galleryphoto.php?id=' . $this->photo->id, $this->photo->id ? $this->InputSafeString($this->photo->details['title']) : 'new photo');
		
	} // end of fn GalleryConstruct
	
	protected function GalleryMainContent()
	{	ob_start();
		echo $this->photo->InputForm($this->gallery->id);
		return ob_get_clean();
	} // end of fn GalleryMainContent
	
} // end of defn GalleryPhotoPage

$page = new GalleryPhotoPage();
$page->Page();
?>