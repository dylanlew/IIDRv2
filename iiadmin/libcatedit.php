<?php
include_once("sitedef.php");

class LibCatEditPage extends CMSPage
{	var $libcat;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();

		$this->libcat  = new AdminLibCat($_GET["id"]);
		
		if (isset($_POST["lcname"]))
		{	$saved = $this->libcat->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	$this->Redirect("libcats.php");
			}
		}
		
		if ($this->libcat->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->libcat->Delete())
			{	$this->Redirect("libcats.php");
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("libcats.php", "Library Categories");
		if ($this->libcat->id)
		{	$this->breadcrumbs->AddCrumb("libcatedit.php?id={$this->libcat->id}", $this->InputSafeString($this->libcat->admintitle));
		} else
		{	$this->breadcrumbs->AddCrumb("libcatedit.php", "Creating new");
		}
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->libcat->InputForm();
	} // end of fn CMSBodyMain
	
} // end of defn LibCatEditPage

$page = new LibCatEditPage();
$page->Page();
?>