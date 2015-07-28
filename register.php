<?php 
require_once('init.php');

class RegisterPage extends BasePage
{	
	function __construct($pageName = '')
	{	parent::__construct('register');
		
		if ($this->user->id)
		{	$this->Redirect('account.php?show=justreg');
		}
		$this->css[] = 'page.css';
		$this->AddBreadcrumb('Sign up', $this->link->GetLink('register.php'));
	} // end of fn __construct

	function MainBodyContent()
	{	
		if ($_GET['login'] == 'failed')
		{	echo '<div class="failmessage">Your login attempt was not successful. Please try again.</div>';
		}
		echo '<div class="register-page"><h2>Existing Users</h2>', $this->user->loginForm($this->link->GetLink('account.php')), '<h2>New Users</h2>', $this->user->RegisterForm1('', ''), '</div>';
	} // end of fn MemberBody
	
} // end of defn RegisterPage

$page = new RegisterPage();
$page->Page();
?>