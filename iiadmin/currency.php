<?php
include_once("sitedef.php");

class CurrencyPage extends AccountsMenuPage
{	var $currency;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->css[] = "admincurrency.css";
		
		$this->currency = new Currency($_GET["code"]);
		
		if (isset($_POST["curname"]))
		{	$saved = $this->currency->Save($_POST);
			$this->failmessage = $saved["failmessage"];
			$this->successmessage = $saved["successmessage"];
		}
		
		if ($_GET["update"])
		{	$this->currency->GoogleUpdateRate();
		}
		
		$this->breadcrumbs->AddCrumb("currencies.php", "Currencies");
		$this->breadcrumbs->AddCrumb("currency.php?code=" . $this->currency->code, 
										$this->currency->code ? "{$this->currency->code} ({$this->currency->details["cursymbol"]})" : "Adding New Currency");
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	$this->currency->InputForm();
		//$this->currency->HistoryTable();
		if ($this->user->CanUserAccess("web content"))
		{	$this->currency->CountriesList();
		}
	} // end of fn AccountsBody
	
} // end of defn CurrencyPage

$page = new CurrencyPage();
$page->Page();
?>