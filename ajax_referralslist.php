<?php 
require_once('init.php');

class AjaxReferralsList extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		echo $this->AffRewardsTable();
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxReferralsList

$page = new AjaxReferralsList();
?>