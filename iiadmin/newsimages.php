<?php
include_once("sitedef.php");

class NewsImagesPage extends CMSPage
{
	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminnews.css";
		$this->breadcrumbs->AddCrumb("newsimages.php", "Images");
		
		if ($delimage = (int)$_GET["delimage"])
		{	$image = new NewsImage($delimage);
			$image->Delete();
		}
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	$this->ListImages();
	} // end of fn CMSBodyMain
	
	function ListImages()
	{	//echo "<div id='stories'>\n";
		$images = new NewsImages();
		$images->AdminListImages();
		//echo "</div>\n";
	} // end of fn ListImages
	
} // end of defn NewsImagesPage

$page = new NewsImagesPage();
$page->Page();
?>