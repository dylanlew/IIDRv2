<?php
include_once("sitedef.php");

class NewsDocsList extends AdminPage
{	
	function __construct()
	{	parent::__construct();
		$this->css[] = "adminnews.css";
	} //  end of fn __construct
	
	function Header()
	{	
	} // end of fn Header
	
	function DisplayBreadcrumbs()
	{	
	} // end of fn DisplayBreadcrumbs
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("news") || $this->user->CanUserAccess("web content"))
		{	$docs = new NewsDocs();
			foreach ($docs->docs as $doc)
			{	echo "<div class='viewimage'>\n<p>for \"", $doc->details["filename"], "\"use this link:<br /><b>", $doc->DocLink(), "</b></p>\n</div>\n";
			}
		}
	} // end of fn AdminBodyMain
	
} // end of defn NewsDocsList

$page = new NewsDocsList();
$page->Page();
?>