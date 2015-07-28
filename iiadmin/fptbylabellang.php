<?php
include_once("sitedef.php");

class FPTextByLabelPage extends AdminFPTPage
{	var $fptext = "";

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function PagesConstruct()
	{	parent::PagesConstruct();
		$this->fptext = new AdminFPTextLang($_GET["name"], $_GET["lang"]);
		if (isset($_POST["content"]))
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
		$this->breadcrumbs->AddCrumb("fptbylabellang.php?name=" . $this->fptext->name . "&lang=" . $this->fptext->lang, 
										$this->fptext->name . " in " . $this->fptext->language["langname"]);
	} // end of fn PagesConstruct
	
	function PagesContent()
	{	$this->fptext->UpdateForm();
	} // end of fn PagesContent
	
} // end of defn FPTextByLabelPage

$page = new FPTextByLabelPage();
$page->Page();
?>