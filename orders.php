<?php 
require_once('init.php');

class OrdersPage extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct('orders');
	} // end of fn LoggedInConstruct
	
	function LoggedInMainBody()
	{	echo '<div id="myOrdersContainer">', $this->user->OrdersList(), '</div>';
	} // end of fn LoggedInMainBody
	
} // end of defn OrdersPage

$page = new OrdersPage();
$page->Page();
?>