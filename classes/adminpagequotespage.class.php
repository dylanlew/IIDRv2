<?php
include_once('sitedef.php');

class AdminPageQuotesPage extends AdminPage
{	
	public function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	public function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->PageQuotesConstruct();
		}
	} // end of fn LoggedInConstruct
	
	protected function PageQuotesConstruct()
	{	$this->breadcrumbs->AddCrumb('pagequotes.php', 'Page quotes');
	} // end of fn PageQuotesConstruct
	
	public function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	echo $this->PageQuotesMainContent();
		}
	} // end of fn AdminBodyMain
	
	protected function PageQuotesMainContent(){}

} // end of defn AdminPageQuotesPage
?>