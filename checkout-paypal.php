<?php 
require_once('init.php');

class CheckoutPayPal extends BasePage
{	private $order;
	
	public function __construct()
	{	parent::__construct('checkout');
		
		if (!$this->user-id)
		{	$this->Redirect('checkout-login.php');	
		}
		
		$this->order = new StoreOrder($_SESSION['order_id']);
		//$this->order = new StoreOrder(24);
		
		if (!$this->order->id)
		{	$this->Redirect('cart.php');	
		}
		
		$this->AddBreadcrumb('Checkout');
		$this->AddBreadcrumb('Payment');
		
	} //  end of fn __construct
	
	public function MainBodyContent()
	{	$paypal = new PayPalStandard();
		echo '<h1>Redirecting to PayPal</h1>',
			'<script>$(function() { $(".paypalform").submit(); });</script>', 
			$paypal->OutputOrderFormBuyNow($this->order);
	} //  end of fn MainBodyContent
	
} // end of defn CheckoutPayPal

$page = new CheckoutPayPal;
$page->Page();

?>