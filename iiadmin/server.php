<?php
include_once('sitedef.php');

class AdminServerPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('ADMIN');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('technical'))
		{	$this->AdminMenuLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AdminMenuLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('server.php', 'Server info');
	} // end of fn AdminMenuLoggedInConstruct
	
	function AdminMenuBody()
	{	
	} // end of fn AdminMenuBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("technical"))
		{	echo '<p><a href="server.php?fred=1">with  ?fred=1</a></p>';
			phpinfo();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminServerPage

$page= new AdminServerPage();
$page->Page();
?>