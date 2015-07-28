<?php
include_once("sitedef.php");

class StaffCatEditPage extends CMSPage
{	var $cat;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();

		$this->cat  = new AdminStaffCategory($_GET["id"]);
		
		if (isset($_POST["scname"]))
		{	$saved = $this->cat->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	$this->Redirect("staffcats.php");
			}
		}
		
		if ($this->cat->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->cat->Delete())
			{	$this->Redirect("staffcats.php");
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("staffcats.php", "Staff categories");
		if ($this->cat->id)
		{	$this->breadcrumbs->AddCrumb("staffcatedit.php?id={$this->cat->id}", $this->InputSafeString($this->cat->details["scname"]));
		} else
		{	$this->breadcrumbs->AddCrumb("staffcatedit.php", "Creating new");
		}
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->cat->InputForm();
	} // end of fn CMSBodyMain
	
} // end of defn StaffCatEditPage

$page = new StaffCatEditPage();
$page->Page();
?>