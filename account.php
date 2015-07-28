<?php 
require_once('init.php');

class EditProfilePage extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		
		if ($_GET['show'])
		{	switch ($_GET['show'])
			{	case 'justreg': 
					$this->successmessage = 'Welcome to IIDR, you are now registered';
					break;
			}
		}
	} // end of fn LoggedInConstruct
	
} // end of defn EditProfilePage

$page = new EditProfilePage();
$page->Page();
//$page = new AccountPage;
//$page->Page();
?>