<?php 
class CheckoutPage extends BasePage
{	protected $cart;
	protected $products = array();
	protected $next_stage;
	protected $eligible_gifts = array();
	protected $bc_to_show = array();
	
	function __construct($pageName = '')
	{	//parent::__construct($pageName ? $pageName : 'checkout');
		parent::__construct('store');
		$this->css[] = 'store.css';
		
		// Make sure a cart exists with no errors
		$this->cart = new StoreCart();
		$this->products = $this->cart->GetProducts();
		
		$this->RedirectIfEmpty();
		$this->SetCartBreadcrumbs();
		
	} // end of fn __construct
	
	public function SetCartBreadcrumbs()
	{	$this->AddBreadcrumb('Shopping Cart', $this->link->GetLink('cart.php'));
		if (!$this->user->id)
		{	$this->AddBreadcrumb('Log in / Register', $this->link->GetLink('checkout-login.php'), '');
		}
		if ($this->cart->GetAttendeeProducts())
		{	$this->AddBreadcrumb('Course registration', $this->link->GetLink('checkout-gift.php'), '', !$this->bc_to_show['course_reg']);
		}
		$this->AddBreadcrumb('Address', $this->link->GetLink('checkout-address.php'), '', !$this->bc_to_show['address']);
		if ($this->cart->HasShipping())
		{	$this->AddBreadcrumb('Delivery', $this->link->GetLink('checkout-delivery.php'), '', !$this->bc_to_show['delivery']);
		}
		$this->AddBreadcrumb('Checkout', '', '', !$this->bc_to_show['checkout']);
	} // end of fn SetCartBreadcrumbs
	
	protected function RedirectIfEmpty()
	{	if ($this->cart->GetErrors() || !$this->cart->Count())
		{
			$this->Redirect('cart.php');
		}
	} // end of fn RedirectIfEmpty
	
	public function Handler()
	{	
		$_SESSION['checkout_stage'] = 1;	
		$this->Redirect('checkout-login.php');
	} // end of fn Handler
	
	protected function RedirectToNextStage()
	{	$this->NextStage();
		$this->Redirect($this->next_stage);	
	} // end of fn RedirectToNextStage
	
	protected function RedirectToPreviousStage()
	{
		if(isset($_SESSION['checkout_previous_stage']))
		{	return $this->Redirect($_SESSION['checkout_previous_stage']);
		} else
		{	return $this->Redirect('cart.php');	
		}
	} // end of fn RedirectToPreviousStage
	
	protected function SetPreviousStage($filename = '')
	{	$_SESSION['checkout_previous_stage'] = $filename;	
	} // end of fn SetPreviousStage
	
	protected function NextStage()
	{	$_SESSION['checkout_stage']++;
	} // end of fn NextStage
	
	protected function PreviousStage()
	{	$_SESSION['checkout_stage']--;
	} // end of fn PreviousStage
	
	protected function GetStage()
	{	return $_SESSION['checkout_stage'];	
	} // end of fn GetStage
	
	
} // end of defn CheckoutPage
?>