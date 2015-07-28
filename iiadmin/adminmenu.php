<?php
include_once("sitedef.php");

class MainAdminMenuPage extends AdminMenuPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AdminMenuBody()
	{	$adminmenu = new AdminMenu($this->user, 0, true);
		$adminmenu->MenuListForAdmin();
	} // end of fn AdminMenuBody
	
} // end of defn MainAdminMenuPage

$page = new MainAdminMenuPage();
$page->Page();
?>