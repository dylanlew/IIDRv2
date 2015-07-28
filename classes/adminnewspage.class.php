<?php
class AdminNewsPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('NEWS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('news'))
		{	$this->AdminNewsLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AdminNewsLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('newsstories.php', 'News');
		$this->css[] = 'adminnews.css';
	} // end of fn AdminNewsLoggedInConstruct
	
	function AdminNewsBody()
	{	
	} // end of fn AdminNewsBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('news'))
		{	$this->AdminNewsBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminNewsPage
?>