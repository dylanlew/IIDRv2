<?php
include_once("sitedef.php");

class NewsImagePage extends CMSPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminnews.css";
	} // end of fn CMSLoggedInConstruct
	
	function Header(){}
	function DisplayBreadcrumbs(){}
	function AdminMenu(){}
	
	function CMSBodyMain()
	{	$newsimages = new NewsImages();
		foreach ($newsimages->images as $image)
		{	echo "<div class='viewimage'>\n<p>use this url:<br /><b>", $image->ImageLink(), "</b></p>\n<img src='", $image->ImageLink(), "' /></div>\n";
		}
	} // end of fn CMSBodyMain
	
} // end of defn NewsImagePage

$page = new NewsImagePage();
$page->Page();
?>