<?php
class AdminDiscountPage extends AccountsMenuPage
{	var $discount;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->DiscountLoggedInConstruct();
	} // end of fn AccountsLoggedInConstruct
	
	function DiscountLoggedInConstruct()
	{	$this->AssignDiscount();
		$this->DiscountConstructFunctions();
		$this->breadcrumbs->AddCrumb('discounts.php', 'Discount codes');
	} // end of fn DiscountLoggedInConstruct
	
	public function AssignDiscount()
	{	
	} // end of fn AssignDiscount
	
	function DiscountBody()
	{	
	} // end of fn DiscountBody
	
	function AccountsBody()
	{	$this->DiscountBody();
	} // end of fn AccountsBody
	
	function DiscountConstructFunctions()
	{	
	} // end of fn DiscountConstructFunctions
	
} // end of defn AdminDiscountPage
?>