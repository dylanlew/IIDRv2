<?php
include_once("sitedef.php");

class FPTextByLangPage extends AdminFPTPage
{	var $fptext = "";

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function PagesConstruct()
	{	parent::PagesConstruct();
		$this->fptext = new AdminFPTLang($_GET["lang"]);
		if (is_array($_POST["content"]))
		{	$saved = $this->fptext->Save($_POST["content"]);
			if ($saved["failmessage"])
			{	$this->failmessage = $saved["failmessage"];
			}
			if ($saved["successmessage"])
			{	$this->successmessage = $saved["successmessage"];
			}
			if ($this->successmessage && !$this->failmessage)
			{	header("location: fptlist.php");
				exit;
			}
		}
		$this->breadcrumbs->AddCrumb("fptbylang.php?lang=" . $this->fptext->language->code, $this->fptext->language->details["langname"]);
	} // end of fn PagesConstruct

	function PagesContent()
	{	$this->fptext->UpdateForm();
	} // end of fn PagesContent
	
} // end of defn FPTextByLangPage

$page = new FPTextByLangPage();
$page->Page();
?>