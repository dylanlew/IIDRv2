<?php 
require_once('init.php');

class SubProductAjax extends Base
{	
	function __construct()
	{	parent::__construct();		
		
		$product = new SubscriptionProduct($_GET['id']);
		//$this->VarDump($product);
		echo '<h3>', $product->details['title'], '</h3>', stripslashes($product->details['description']), '<p id="sub_popup_buynow">', $product->BuyButton(), '</p>';
	
	} // end of fn __construct
	
} // end of defn SubProductAjax

$page = new SubProductAjax();
?>