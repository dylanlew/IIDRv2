<?php
include_once('sitedef.php');

class GrantAppsPage extends AdminGrantAppsPage
{	var $app;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function GrantAppsLoggedInConstruct()
	{	parent::GrantAppsLoggedInConstruct();
		$this->app = new AdminGrantApp($_GET['id']);
		
		if (isset($_POST['adminnotes']))
		{	$saved = $this->app->AdminSave($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		$this->breadcrumbs->AddCrumb('grantapp.php?id=' . $this->app->id, $this->app->AdminTitle());
		
	} // end of fn GrantAppsLoggedInConstruct
	
	function GrantAppsBody()
	{	echo $this->app->Display();
	//	$this->app->AdminEmail();
	} // end of fn GrantAppsBody
	
} // end of defn GrantAppsPage

$page = new GrantAppsPage();
$page->Page();
?>