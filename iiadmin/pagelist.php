<?php
include_once("sitedef.php");

class PageListPage extends CMSPage
{	var $pages = "";

	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct

	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->breadcrumbs->AddCrumb("pagelist.php", "Pages");
		if ($this->user->CanUserAccess("web content"))
		{	
			if ($delpage = (int)$_GET["delpage"])
			{	$pagetodel = new AdminPageContent($delpage, $this->user->CanUserAccess("administration"));
				if ($_GET["delconfirm"])
				{	$deleted = $pagetodel->Delete();
					if ($deleted["failmessage"])
					{	$this->failmessage = $deleted["failmessage"];
					}
					if ($deleted["successmessage"])
					{	$this->successmessage = $deleted["successmessage"];
					}
				} else
				{	$this->failmessage = "<a href='pagelist.php?delpage={$pagetodel->id}&delconfirm=1'>Please confirm deletion of page \"{$pagetodel->details["pagetitle"]}\"</a>";
				}
			}
			$this->pages = new AdminPageContents($this->user->CanUserAccess("administration"));
		}
	} // end of fn CMSLoggedInConstruct

	function CMSBodyMain()
	{	$this->pages->PageList();
	} // end of fn CMSBodyMain
	
} // end of defn PageListPage

$page = new PageListPage();
$page->Page();
?>