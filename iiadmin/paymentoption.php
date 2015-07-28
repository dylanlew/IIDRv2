<?php
include_once("sitedef.php");

class PaymentOptionPage extends AccountsMenuPage
{	var $option;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();

		$this->option  = new AdminPaymentOption($_GET["id"]);
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "course_tiny_mce.js";
		
		if (isset($_POST["optname"]))
		{	$saved = $this->option->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	$this->Redirect("paymentoptions.php");
			}
		}
		
		if ($this->option->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->option->Delete())
			{	$this->Redirect("paymentoptions.php");
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("paymentoptions.php", "Payment options");
		$this->breadcrumbs->AddCrumb("paymentoption.php?id={$this->option->id}", $this->InputSafeString($this->option->details["optname"]));
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	echo $this->option->InputForm();
	} // end of fn AccountsBody
	
} // end of defn PaymentOptionPage

$page = new PaymentOptionPage();
$page->Page();
?>