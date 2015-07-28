<?php
include_once("sitedef.php");

class MailTemplateListPage extends CMSPage
{	var $templates = "";

	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct

	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->breadcrumbs->AddCrumb("mailtlist.php", "Mail templates");
		$this->templates = new MailTemplates();
	} // end of fn CMSLoggedInConstruct

	function CMSBodyMain()
	{	echo $this->templates->AdminList();
	} // end of fn CMSBodyMain
	
} // end of defn MailTemplateListPage

$page = new MailTemplateListPage();
$page->Page();
?>