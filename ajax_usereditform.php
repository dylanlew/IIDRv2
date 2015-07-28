<?php 
require_once('init.php');

class AjaxUserFormPage extends DashboardPage
{
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	
		echo $this->user->EditDetailsForm("dashboard.php?tab=edit");

	} // end of fn LoggedInConstruct
	
} // end of class AjaxUserFormPage

$page = new AjaxUserFormPage();
?>