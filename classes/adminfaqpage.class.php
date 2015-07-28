<?php
include_once('sitedef.php');

class AdminFAQPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->FAQConstructor();
		}
	} // end of fn LoggedInConstruct
	
	public function FAQConstructor()
	{	$this->breadcrumbs->AddCrumb('faqlist.php', 'FAQ');
		$this->css[] = 'adminfaq.css';
	} // end of fn FAQConstructor
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	$this->FAQMainContent();
		}
	} // end of fn AdminBodyMain
	
	public function FAQMainContent(){}
	
} // end of defn AdminFAQPage
?>