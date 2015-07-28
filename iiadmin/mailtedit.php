<?php
include_once("sitedef.php");

class MailTemplateEditPage extends CMSPage
{	var $template = "";

	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct

	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->css[] = "admin_emt.css";
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "mailtemplate_tiny_mce.js";
		$this->template = new AdminMailTemplate($_GET["id"]);
		
		if (isset($_POST["subject"]))
		{	$saved = $this->template->Save($_POST);
			if ($saved["failmessage"])
			{	$this->failmessage = $saved["failmessage"];
			}
			if ($saved["successmessage"])
			{	$this->successmessage = $saved["successmessage"];
			}
		}
		
		$this->breadcrumbs->AddCrumb("mailtlist.php", "Mail templates");
		$this->breadcrumbs->AddCrumb("mailtedit.php?id=" . $this->template->id, $this->template->details["mailname"]);
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->template->InputForm();
	} // end of fn CMSBodyMain
	
} // end of defn MailTemplateEditPage

$page = new MailTemplateEditPage();
$page->Page();
?>