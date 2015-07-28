<?php 
require_once('init.php');

class AjaxMyOrders extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		echo $this->user->OrdersList($_GET['limit'], $_GET['perpage']);
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxMyOrders

$page = new AjaxMyOrders();
?>