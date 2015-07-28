<?php 
require_once('init.php');

class ForgotPasswordPage extends BasePage
{	var $password_sent = "";

	function __construct()
	{	parent::__construct("retrieve-password");
		$this->css[] = "fpassword.css";
		
		if (!$this->user->id)
		{	
			if ($_POST["fpemail"])
			{	$saved = $this->SendNewPassword($_POST["fpemail"]);
				$this->failmessage = $saved["fail"];
				$this->successmessage = $saved["success"];
			}
		}
		
	} // end of fn __construct

	function MainBodyContent()
	{	
		echo "<div class='course-content-wrapper'>\n", $this->page->HTMLMainContent();
		if (!$this->user->id)
		{	if ($this->password_sent)
			{	echo $this->SentConfirmation();
			} else
			{	echo $this->GetPasswordForm();
			}
		}
		echo "<div class='clear'></div></div>\n";
	} // end of fn MemberBody
	
	function SentConfirmation()
	{	ob_start();
		echo "<p>Your password has been sent to $this->password_sent, please check your junk mail folder for the email</p>\n";
		return ob_get_clean();
	} // end of fn SentConfirmation
	
	function GetPasswordForm()
	{	ob_start();
		echo "<form action='forgot_password.php' class='form' method='post' />\n",
				"<p><label>The email address you used to book</label><input type='text' name='fpemail' value='", $this->InputSafeString($_POST["fpemail"]), "' /></p>\n",
				"<p><input type='submit' class='submit' value='Get new password' /></p></form>\n";
		
		return ob_get_clean();
	} // end of fn GetPasswordForm

	function MainBodyBanner()
	{	ob_start();
		echo "<p class='page-banner'><img src='img/banners/courses.jpg' alt='' /></p>";
		return ob_get_clean();
	} // end of fn MainBodyBanner
	
	function SendNewPassword($email = "")
	{	
		$fields = array();
		$fail = array();
		$success = array();

		// check for email in user list
		$checksql = "SELECT userid FROM akusers WHERE username='" . $this->SQLSafe($email) . "'";
		if ($result = $this->db->Query($checksql))
		{	if ($row = $this->db->FetchArray($result))
			{	if ($userid = (int)$row["userid"])
				{	
					if ($this->ValidEMail($email))
					{	$newpassword = $this->ConfirmCode(12);
						$pwsql = "UPDATE akusers SET upassword=MD5('$newpassword') WHERE userid=$userid";
						$this->db->Query($pwsql);
						
						// now send email
						$subject = "Important message from " . $this->GetParameter("comptitle");
						$htmlbody = "<p>Your new password is $newpassword</p>\n<p>You can now log in at <a href='" . SITE_URL . "'>" . SITE_URL . "</a> with the username '$email'</p>";
						$plainbody = "Your new password is $newpassword\nYou can now log in at " . SITE_URL . " with the username '$email'</p>";
						$mail = new HTMLMail();
						$mail->Send($email, $htmlbody, $plainbody);
						$success[] = "Your new password has been sent to you at $email";
					} else
					{	$fail[] = "Your username is not an email address. Please contact " . $this->GetParameter("compemail") . " to get your new password";
					}
				}
			} else
			{	$fail[] = "we do not have you registered with that email address, please check your typing";
			}
		}
		
		return array("fail"=>implode(", ", $fail), "success"=>implode(", ", $success));
		
	} // end of fn SendNewPassword
	
} // end of defn ForgotPasswordPage

$page = new ForgotPasswordPage();
$page->Page();
?>