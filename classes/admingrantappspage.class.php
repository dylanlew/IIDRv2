<?php
class AdminGrantAppsPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('MEMBERS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('members'))
		{	$this->GrantAppsLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function GrantAppsLoggedInConstruct()
	{	$this->css[] = 'admingrants.css';
		$this->breadcrumbs->AddCrumb('grantapps.php', 'Grant Applications');
	} // end of fn GrantAppsLoggedInConstruct
	
	function GrantAppsBody()
	{	
	} // end of fn GrantAppsBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('members'))
		{	$this->GrantAppsBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminGrantAppsPage
?>