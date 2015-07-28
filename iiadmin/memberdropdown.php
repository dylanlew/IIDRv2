<?php
include_once("sitedef.php");

class AdminMemberDropdownEditPage extends CMSPage
{	var $dropdown;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminmemberdropdown.css";

		$this->dropdown  = new AdminMemberDropdown($_GET["id"]);
		
		if (isset($_POST["textdesc"]))
		{	$saved = $this->dropdown->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
		//	if ($this->successmessage && !$this->failmessage)
		//	{	$this->Redirect("memberdropdowns.php");
		//	}
		}
		
		$this->breadcrumbs->AddCrumb("memberdropdowns.php", "Registration dropdown lists");
		$this->breadcrumbs->AddCrumb("memberdropdown.php?id={$this->dropdown->id}", $this->InputSafeString($this->dropdown->admintitle));
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->dropdown->InputForm();
	} // end of fn CMSBodyMain
	
} // end of defn AdminMemberDropdownEditPage

$page = new AdminMemberDropdownEditPage();
$page->Page();
?>