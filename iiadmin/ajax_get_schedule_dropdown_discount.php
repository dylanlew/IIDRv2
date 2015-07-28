<?php
include_once("sitedef.php");

class AjaxPayBooking extends AccountsMenuPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$discount = new AdminDiscount();
		echo "<!--schedule-->", $discount->ScheduleDropDown($_GET["content"], 0, $_GET["namextra"]);
		
	} // end of fn AccountsLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>