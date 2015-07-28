<?php 
require_once('init.php');

class EditProfilePage extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		$this->AddBreadcrumb('Edit Details');
		$this->css[] = 'page.css';
		
		if (isset($_POST['username']))
		{	$saved = $this->user->SaveDetails($_POST);
			$this->failmessage = $saved['fail'];
			$this->successmessage = $saved['success'];
		}
		
	} // end of fn LoggedInConstruct
	
	function LoggedInMainBody()
	{	
		echo '<h1>Edit your details</h1>'; 
		echo '<div class="register-page">', $this->user->EditDetailsForm(), '</div>';
	} // end of fn LoggedInMainBody
	
} // end of defn EditProfilePage

$page = new EditProfilePage();
$page->Page();
?>