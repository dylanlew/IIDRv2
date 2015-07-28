<?php
class CMSPage extends AdminPage
{	
	function __construct()
	{	parent::__construct("PARAMETERS");
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("web content"))
		{	$this->breadcrumbs->AddCrumb("", "CMS");
			$this->CMSLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function CMSLoggedInConstruct()
	{	
	} // end of fn CMSLoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("web content"))
		{	$this->CMSBodyMain();
		}
	} // end of fn AdminBodyMain
	
	function CMSBodyMain()
	{	
	} // end of fn CMSBodyMain
	
} // end of defn CMSPage
?>