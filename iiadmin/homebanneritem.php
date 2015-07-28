<?php
include_once("sitedef.php");

class HomeBannerItemPage extends CMSPage
{	var $hbitem;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "pageedit_tiny_mce.js";
		$this->css[] = 'jPicker-1.1.6.css';
		$this->js[] = 'jpicker-1.1.6.min.js';

		$this->hbitem  = new AdminHomeBannerItem($_GET["id"]);
		
		if (isset($_POST["hbtitle"]))
		{	$saved = $this->hbitem->Save($_POST, $_FILES["imagefile"]);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: homebanner.php");
				exit;
			}
		}
		
		if ($this->hbitem->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->hbitem->Delete())
			{	header("location: homebanner.php");
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("homebanner.php", "Homepage banner items");
		if ($this->hbitem->id)
		{	$this->breadcrumbs->AddCrumb("homebanneritem.php?id={$this->hbitem->id}", $this->InputSafeString($this->hbitem->admintitle));
		} else
		{	$this->breadcrumbs->AddCrumb("homebanneritem.php", "Creating new item");
		}
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->hbitem->InputForm();
	} // end of fn CMSBodyMain
	
} // end of defn HomeBannerItemPage

$page = new HomeBannerItemPage();
$page->Page();
?>