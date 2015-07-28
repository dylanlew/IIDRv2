<?php
require_once('init.php');

class OrderSuccessPage extends BasePage
{
	public function __construct()
	{
		parent::__construct();	
		unset($_SESSION['order_id']);
			
	}
	
	public function MainBodyContent()
	{
		echo '<h1>Your order was successful</h1><p>Thank you for your order. You can view the progress of your order from \'<a href="'. $this->link->GetLink('account.php') .'">My Account</a>\'.</p>';
	}
}

$page = new OrderSuccessPage();
$page->Page();

?>