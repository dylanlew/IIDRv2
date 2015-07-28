<?php
include_once("sitedef.php");

class NewsImagePage extends CMSPage
{	var $image;

	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminnews.css";
		
		$this->image = new NewsImage($_GET["id"]);
	
		if ($_FILES["newsimage"])
		{	$saved = $this->image->Upload($_FILES["newsimage"]);
			$this->failmessage = $saved["failmessage"];
			$this->successmessage = $saved["successmessage"];
		}
		
		$this->breadcrumbs->AddCrumb("newsimages.php", "Images");
		$this->breadcrumbs->AddCrumb("newsimage.php?id=" . $this->image->id, 
										$this->image->id ? "Editing Image" : "Adding New Image");
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	$this->image->UploadForm();
	} // end of fn CMSBodyMain
	
} // end of defn NewsImagePage

$page = new NewsImagePage();
$page->Page();
?>