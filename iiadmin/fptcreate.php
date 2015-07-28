<?php
include_once("sitedef.php");

class FPTextCreatePage extends AdminFPTPage
{	var $fptext = "";

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function PagesConstruct()
	{	parent::PagesConstruct();
		if ($this->user->CanUserAccess("technical"))
		{	$this->fptext = new AdminFPText();
			if (isset($_POST["fptlabel"]))
			{	$saved = $this->fptext->Create($_POST);
				if ($saved["failmessage"])
				{	$this->failmessage = $saved["failmessage"];
				}
				if ($saved["successmessage"])
				{	header("location: fptlist.php");
					exit;
				}
			}
		}
		$this->breadcrumbs->AddCrumb("fptcreate.php", "Creating New Label");
	} // end of fn PagesConstruct

	function PagesContent()
	{	if ($this->user->CanUserAccess("technical"))
		{	$this->fptext->CreateForm();
		}
	} // end of fn PagesContent
	
} // end of defn FPTextCreatePage

$page = new FPTextCreatePage();
$page->Page();
?>