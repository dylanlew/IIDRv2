<?php
include_once("sitedef.php");

class SiteEmailsListPage extends AdminAKMembersPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb("siteemails.php", "Site Emails");
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	$emails = new SiteEmails();
		$emails->ListEmails();
	} // end of fn AKMembersBody
	
} // end of defn SiteEmailsListPage

$page = new SiteEmailsListPage();
$page->Page();
?>