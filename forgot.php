<?php 
require_once('init.php');

class ForgotPasswordPage extends BasePage
{	
	function __construct($pageName = '')
	{	parent::__construct('register');
		
		if (isset($_POST['fp_user']))
		{	$this->SendNewPassword($_POST['fp_user']);
		}
		
		$this->css[] = 'page.css';
		$this->css[] = 'fpassword.css';
		$this->AddBreadcrumb('Forgot your password?', $this->link->GetLink('forgot.php'));
	} // end of fn __construct

	function MainBodyContent()
	{	
		echo '<div class="register-page"><h1>Forgotten your password?</h1><h3>A new password will be sent to your email address</h3>', $this->user->ForgotPasswordForm($this->link->GetLink('forgot.php')), '</div>';
	} // end of fn MemberBody
	
	public function SendNewPassword($username = '')
	{	
		if ($this->ValidEmail($username))
		{	// check for username on database
			$sql = 'SELECT * FROM students WHERE username="' . $this->SQLSafe($username) . '"';
			if ($result = $this->db->Query($sql))
			{	if (($row = $this->db->FetchArray($result)) && ($student = new Student($row)) && $student->id)
				{	$saved = $student->SendNewPassword();
					$this->successmessage = $saved['success'];
					$this->failmessage = $saved['fail'];
				} else
				{	$this->failmessage = 'Your email address has not been found - are you sure this was the email address you registered with?';
				}
			}
		} else
		{	$this->failmessage = 'You must enter a valid email address';
		}
		
	} // end of fn SendNewPassword
	
} // end of defn ForgotPasswordPage

$page = new ForgotPasswordPage();
$page->Page();
?>