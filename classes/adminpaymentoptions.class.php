<?php
class AdminPaymentOptions extends PaymentOptions
{	var $items = array();

	function __construct($country = "")
	{	parent::__construct($country);
	} // end of fn __construct
	
	function AssignOption($row = array(), $country = "")
	{	return new AdminPaymentOption($row, $country);
	} // end of fn AssignOption
	
} // end of defn AdminPaymentOptions
?>