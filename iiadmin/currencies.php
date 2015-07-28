<?php
include_once("sitedef.php");

class CurrenciesPage extends AccountsMenuPage
{	var $currencies;

	function __construct()
	{	parent::__construct();
		$this->css[] = "admincurrency.css";
		$this->breadcrumbs->AddCrumb("currencies.php", "Currencies");
		$this->currencies = new Currencies();
		
		if ($_GET["updateall"])
		{	$this->currencies->UpdateAllRates();
		}
		
	} //  end of fn __construct
	
	function AccountsBody()
	{	$this->currencies->AdminList();
	} // end of fn AccountsBody
	
} // end of defn CurrenciesPage

$page = new CurrenciesPage();
$page->Page();
?>