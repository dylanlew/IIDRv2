<?php
include_once("sitedef.php");

class YourPasswordPage extends AdminPage
{	
	function __construct()
	{	parent::__construct("PASSWORD");
	} // end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
	//	$this->css[] = "userpword.css";
		$this->breadcrumbs->AddCrumb("yourpword.php", "Your Password");
		
		if ($_POST["pword"])
		{	$this->Save();
		}
		
	} // end of fn LoggedInConstruct
	
	function Save()
	{	$fail = array();
		
		if ($_POST["pword"] !== $_POST["rtpword"])
		{	$fail[] = "password mistyped";
		} else
		{	if ($this->AcceptablePW($_POST["pword"], 8, 20))
			{	$pword = $_POST["pword"];
			} else
			{	$fail[]= "password not acceptable";
			}
		}
		
		if ($fail)
		{	$this->failmessage = implode(", ", $fail);
		} else
		{	$sql = "UPDATE adminusers SET upassword=MD5('$pword') WHERE auserid={$this->user->userid}";
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->successmessage = "password successfully changed";
				}
			}
		}
		
	} // end of fn Save
		
	function AdminBodyMain()
	{	$this->PasswordForm();
	} // end of fn AdminBodyMain
	
	function PasswordForm()
	{	$regform = new Form($_SERVER["SCRIPT_NAME"], "regform");
		$regform->AddPasswordInput("new password", "pword", "password", 20, 1);
		$regform->AddPasswordInput("retype", "rtpword", "password", 20, 1);
		$regform->AddSubmitButton("", "Save Change", "submit");
		echo "<p>Your password must be between 8 and 20 letters or numbers</p>";
		$regform->Output();
	} // end of fn RegisterForm
	
} // end of defn YourPasswordPage

$page = new YourPasswordPage();
$page->Page();
?>