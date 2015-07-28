<?php 
require_once('init.php');

class CheckoutAddress extends CheckoutPage
{	private $option;	
	private $options = array();
	private $payment_address = array();
	private $delivery_address = array();
	protected $bc_to_show = array('course_reg'=>true, 'address'=>true);
	
	function __construct()
	{	parent::__construct('store');
		
		$this->next_stage = 'checkout-delivery.php';
		
		if (!$this->user-id || ($this->GetStage() < 3))
		{	$this->RedirectToPreviousStage();	
		}
		
		// Set address info
		$this->delivery_address = &$_SESSION['order']['delivery_address'];
		$this->payment_address = &$_SESSION['order']['payment_address'];
		
		if (!$this->delivery_address)
		{	$this->delivery_address = $this->PrePopulateAddress();
		}
		
		if (!$this->payment_address)
		{	$this->payment_address = $this->PrePopulateAddress();
		}
		
		if (isset($_POST['submit_address']))
		{	if (isset($_POST['delivery_address']))
			{	foreach ($_POST['delivery_address'] as $key => $value)
				{	$this->delivery_address[$key] = $value;	
				}
			}
			
			foreach($_POST['payment_address'] as $key => $value)
			{	$this->payment_address[$key] = $value;	
			}
			
			$fail = array();
			
			// validate
			if (!isset($_POST['payment_address']['address1']) || trim($_POST['payment_address']['address1']) == '')
			{	$fail[] = 'Please enter your payment address line 1';
			}
			
			if (!isset($_POST['payment_address']['postcode']) || trim($_POST['payment_address']['postcode']) == '')
			{	$fail[] = 'Please enter your payment address postcode';
			}
			
			// Only validate delivery address if cart has shipping
			if ($this->cart->HasShipping())
			{	if (!isset($_POST['delivery_address']['address1']) || trim($_POST['delivery_address']['address1']) == '')
				{	$fail[] = "Please enter your delivery address line 1";
				}
				if (!isset($_POST['delivery_address']['postcode']) || trim($_POST['delivery_address']['postcode']) == '')
				{	$fail[] = "Please enter your delivery address postcode";
				}	
			}
			
			if(!$fail)
			{	$this->SetPreviousStage('checkout-address.php');
				$this->RedirectToNextStage();
			} else
			{	$this->failmessage = implode(', ', $fail);	
			}
		}
		
	} // end of fn __construct
	
	public function PrePopulateAddress()
	{
		$data = array();
		$data['firstname'] = $this->user->details['firstname'];
		$data['surname'] = $this->user->details['surname'];
		$data['address1'] = $this->user->details['address1'];
		$data['address2'] = $this->user->details['address2'];
		$data['address3'] = $this->user->details['address3'];
		$data['phone'] = $this->user->details['phone'];
		$data['city'] = $this->user->details['city'];
		$data['postcode'] = $this->user->details['postcode'];
		$data['country'] = $this->user->details['country'];
		
		return $data;
	} // end of fn PrePopulateAddress
	
	public function MainBodyContent()
	{
		echo '<div class="checkout-address-boxes clearfix"><form action="" method="post">', $this->cart->HasShipping() ? $this->OutputAddressBlock('delivery') : '', $this->OutputAddressBlock('payment'), '<p><input type="submit" name="submit_address" value="Continue" class="button-link checkout-link" /></p></form></div>';
	} // end of fn MainBodyContent
	
	public function OutputAddressBlock($type = 'delivery')
	{
		ob_start();
		
		if ($type == 'delivery')
		{	$data = $this->delivery_address;
			$dname = 'delivery_address';
			$title = 'Delivery Address';
			echo '<div class="checkout-address-deliverybox">';
		} else
		{	$data = $this->payment_address;
			$dname = 'payment_address';
			$title = 'Payment Address';
			echo '<div class="checkout-address-paymentbox">';
		}
		echo '<h2>', $title, '</h2><div class="inner clearfix"><p><label>First name</label> <input type="text" name="', $dname, '[firstname]" value="', $this->InputSafeString($data['firstname']), '" /></p><p><label>Surname</label> <input type="text" name="', $dname, '[surname]" value="', $this->InputSafeString($data['surname']), '" /></p><p><label>Address Line 1</label> <input type="text" name="', $dname, '[address1]" value="', $this->InputSafeString($data['address1']), '" /></p><p><label>Address Line 2</label> <input type="text" name="', $dname, '[address2]" value="', $this->InputSafeString($data['address2']), '" /></p><p><label>Address Line 3</label> <input type="text" name="', $dname, '[address3]" value="', $this->InputSafeString($data['address3']), '" /></p><p><label>City</label> <input type="text" name="', $dname,'[city]" value="', $this->InputSafeString($data['city']),'" /></p><p><label>Postcode</label> <input type="text" name="', $dname, '[postcode]" value="', $this->InputSafeString($data['postcode']),'" /></p><p><label>Country</label> <select name="', $dname, '[country]">';
		foreach ($this->GetCountries() as $ccode=>$cname)
		{	echo '<option value="', $ccode, '"', $data['country'] == $ccode ? ' selected="selected"' : '', '>', $this->InputSafeString($cname), '</option>';
		}
		echo '</select></p><p><label>Phone</label> <input type="text" name="', $dname, '[phone]" value="', $this->InputSafeString($data['phone']), '" /></p></div></div>';
		
		return ob_get_clean();
	} // end of fn OutputAddressBlock

} // end of defn CheckoutAddress

$page = new CheckoutAddress;
$page->Page();

?>