<?php
include_once("sitedef.php");

class PPAccountEditPage extends AccountsMenuPage
{	var $account;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->account  = new AdminPaypalAccount($_GET["id"]);
		
		if (isset($_POST["username"]))
		{	$saved = $this->account->Save();
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: ppaccounts.php");
				exit;
			}
		}
		
		if ($this->account->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->account->Delete())
			{	header("location: ppaccounts.php");
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}

		$this->breadcrumbs->AddCrumb("ppaccounts.php", "Paypal Accounts");
		
		if ($this->account->id)
		{	$this->breadcrumbs->AddCrumb("ppedit.php?id={$this->account->id}", $this->InputSafeString($this->account->details["username"]));
		} else
		{	$this->breadcrumbs->AddCrumb("ppedit.php", "Creating new account");
		}
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	$this->account->InputForm();
	} // end of fn AccountsBody
	
} // end of defn PPAccountEditPage

$page = new PPAccountEditPage();
$page->Page();
?>