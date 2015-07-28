<?php
include_once("sitedef.php");

class NewsDocsPage extends AdminPage
{
	function __construct()
	{	parent::__construct();
		$this->css[] = "adminnews.css";
		$this->breadcrumbs->AddCrumb("newsdocs.php", "Documents for Download");
		
		if ($deldoc = (int)$_GET["deldoc"])
		{	$doc = new NewsDoc($deldoc);
			$doc->Delete();
		}
		
	} //  end of fn __construct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("news") || $this->user->CanUserAccess("web content"))
		{	$this->ListDocs();
		}
	} // end of fn AdminBodyMain
	
	function ListDocs()
	{	echo "<div id='stories'>\n";
		$docs = new NewsDocs();
		$docs->AdminListDocs();
		echo "</div>\n";
	} // end of fn ListDocs
	
} // end of defn NewsDocsPage

$page = new NewsDocsPage();
$page->Page();
?>