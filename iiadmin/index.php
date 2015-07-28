<?php
include_once("sitedef.php");

class IndexPage extends AdminPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("web content"))
		{	header("location: pagelist.php");
			exit;
		}
		if ($this->user->CanUserAccess("administration"))
		{	header("location: userlist.php");
			exit;
		}
	} // end of fn LoggedInConstruct
	
} // end of defn IndexPage

$page = new IndexPage();
$page->Page();
?>