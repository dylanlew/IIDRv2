<?php
include_once("sitedef.php");

class UserCountriesPage extends AdminPage
{	var $edituser;

	function __construct()
	{	parent::__construct("ADMIN");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		$this->css[] = "adminuserctry.css";
		if ($this->user->CanUserAccess("administration"))
		{	$this->edituser = new AdminUser((int)$_GET["userid"], 1);
			
			if (isset($_POST["ctry_save"]))
			{	$saved = $this->edituser->SaveCountries($_POST["country"]);
				$this->successmessage = $saved["successmessage"];
				$this->failmessage = $saved["failmessage"];
			}
			
			$this->breadcrumbs->AddCrumb("userlist.php", "Admin Users");
			$this->breadcrumbs->AddCrumb("useredit.php?userid={$this->edituser->userid}", $this->edituser->username);
			$this->breadcrumbs->AddCrumb("usercountries.php?userid={$this->edituser->userid}", "Country access");
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("administration"))
		{	echo $this->edituser->CountriesForm();
		}
	} // end of fn AdminBodyMain
	
} // end of defn UserCountriesPage

$page = new UserCountriesPage();
$page->Page();
?>