<?php
class AccountsMenuPage extends AdminPage
{	
	function __construct()
	{	parent::__construct("ACCOUNTS");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("accounts"))
		{	$this->AccountsLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AccountsLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb("", "Accounts");
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	
	} // end of fn AccountsBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("accounts"))
		{	$this->AccountsBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AccountsMenuPage
?>