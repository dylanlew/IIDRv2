<?php
include_once("sitedef.php");

class AdminFPTPage extends AdminPage
{
	function __construct()
	{	parent::__construct("CONTENT");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->css[] = "adminfpt.css";
		$this->breadcrumbs->AddCrumb("fptlist.php", "Site Text");
		if ($this->user->CanUserAccess("web content"))
		{	$this->PagesConstruct();
		}
	} // end of fn LoggedInConstruct

	function PagesConstruct()
	{	
	} // end of fn PagesConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("web content"))
		{	$this->PagesContent();
		}
	} // end of fn AdminBodyMain
	
	function PagesContent()
	{	
	} // end of fn PagesContent
	
} // end of defn AdminFPTPage
?>