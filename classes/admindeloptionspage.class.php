<?php
class AdminDelOptionsPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('ACCOUNTS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('accounts'))
		{	$this->DelOptionsLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function DelOptionsLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('delivery.php', 'Delivery options');
	} // end of fn DelOptionsLoggedInConstruct
	
	function DelOptionsBody()
	{	
	} // end of fn DelOptionsBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('accounts'))
		{	$this->DelOptionsBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminDelOptionsPage
?>