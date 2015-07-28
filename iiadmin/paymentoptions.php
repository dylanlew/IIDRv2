<?php
include_once("sitedef.php");

class PaymentOptionsPage extends AccountsMenuPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("paymentoptions.php", "Payment options");
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	$this->OptionsList();
	} // end of fn AccountsBody
	
	function OptionsList()
	{	echo "<table><tr><th>Option</th><th>Description</th><th>Order</th><th>Paypal?</th><th>Fee (if any)</th><th>On door?</th><th>Default?</th><th>Suppressed</th><th>Languages</th><th>Actions</th></tr>";
		$homebanner = new AdminPaymentOptions();
		foreach ($homebanner->options as $option)
		{	
			echo "<tr class='stripe", $i++ % 2, "'>\n<td>", $this->InputSafeString($option->id), "</td>\n<td>", $this->InputSafeString($option->details["optname"]), "</td>\n<td>", (int)$option->details["optorder"], "</td>\n<td>", $option->details["paypal"] ? "Yes" : "No", "</td>\n<td>", $option->details["percentfee"] ? $option->details["percentfee"] . "%" : "", "</td>\n<td>", $option->details["payondoor"] ? "Yes" : "No", "</td>\n<td>", $option->details["defoption"] ? "Yes" : "", "</td>\n<td>", $option->SuppressedCountriesList(), "</td>\n<td>", $option->LangUsedString(), "</td>\n<td><a href='paymentoption.php?id=", $option->id, "'>edit</a>";
			if ($histlink = $this->DisplayHistoryLink("pmtoptions", $option->id))
			{	echo "&nbsp;|&nbsp;", $histlink;
			}
			if ($option->CanDelete())
			{	echo "&nbsp;|&nbsp;<a href='paymentoption.php?id=", $option->id, "&delete=1'>delete</a>";
			}
			echo "</td>\n</tr>\n";
		}
		echo "</table>\n";
	} // end of fn OptionsList
	
} // end of defn PaymentOptionsPage

$page = new PaymentOptionsPage();
$page->Page();
?>