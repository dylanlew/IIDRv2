<?php
class AdminAKMembersPage extends AdminPage
{	
	function __construct()
	{	parent::__construct("MEMBERS");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("members"))
		{	$this->AKMembersLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AKMembersLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb("members.php", "Members");
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	
	} // end of fn AKMembersBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("members"))
		{	$this->AKMembersBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminAKMembersPage
?>