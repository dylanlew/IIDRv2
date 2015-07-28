<?php
include_once("sitedef.php");

class UserEditPage extends AdminPage
{	var $edituser;

	function __construct()
	{	parent::__construct("ADMIN");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("administration"))
		{	$this->edituser = new AdminUser((int)$_GET["userid"], 1);
			$this->breadcrumbs->AddCrumb("userlist.php", "Admin Users");
			
			if ($_POST["username"])
			{	if ($this->Save())
				{	header("location: userlist.php");
					exit;
				}
			}
			
			if ($this->edituser->userid && ($this->edituser->userid != $this->user->userid) && $_GET["del"] && $_GET["confirm"])
			{	if ($this->edituser->Delete())
				{	header("location: userlist.php");
					exit;
				}
			}
			
			$this->breadcrumbs->AddCrumb("useredit.php?userid={$this->edituser->userid}", $this->edituser->userid ? $this->edituser->username : "New User");
		}
	} // end of fn LoggedInConstruct

	function Save()
	{	$fail = array();
		
		if ($this->edituser->userid == $this->user->userid && !$_POST["access"][1])
		{	$fail[] = "you can't remove your own admin privileges";
		}
		
		if ($_POST["pword"] || $_POST["rtpword"])
		{	if ($_POST["pword"] !== $_POST["rtpword"])
			{	$fail[] = "password mistyped";
			} else
			{	if ($this->AcceptablePW($_POST["pword"], 8, 20))
				{	$pword = $_POST["pword"];
				} else
				{	$fail[]= "password not acceptable";
				}
			}
		} else
		{	if (!$this->edituser->userid)
			{	$fail[] = "password needed";
			}
		}

		if (!preg_match("{^[A-Za-z0-9]{3,30}$}i", $_POST["username"]))
		{	$fail[] = "invalid username";
		}

		if (!preg_match("{^[A-Za-z0-9 ]*$}i", $_POST["firstname"]))
		{	$fail[] = "invalid first name";
		}

		if (!preg_match("{^[A-Za-z0-9 ]*$}i", $_POST["surname"]))
		{	$fail[] = "invalid surname";
		}
		
		if ($_POST["email"])
		{	if (!$this->ValidEMail($_POST["email"]))
			{	$fail[] = "invalid e-mail";
			}
		}
		
		if (!$this->failmessage = implode(", ", $fail))
		{	if ($this->edituser->Save($_POST["username"], $_POST["pword"], $_POST["firstname"], 
						$_POST["surname"], $_POST["access"], $_POST["email"]))
			{	$this->successmessage = "changes saved";
				return true;
			}
		}
	} // end of fn Save
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("administration"))
		{	$this->UserEditForm();
		}
	} // end of fn AdminBodyMain
	
	function UserEditForm()
	{	$editform = new Form("useredit.php?userid={$this->edituser->userid}", "regform");
		if ($this->edituser->userid)
		{	if ($this->edituser->userid != $this->user->userid)
			{	$editform->AddLabelLine($this->edituser->DeleteLink(), "");
			}
		} else
		{	$editform->AddLabelLine("new user", "");
		}
		$editform->AddTextInput("Log in (3 to 30 letters)", "username", $this->edituser->username, "");
		$editform->AddTextInput("First name", "firstname", $this->edituser->firstname, "");
		$editform->AddTextInput("Surname", "surname", $this->edituser->surname, "");
		$editform->AddTextInput("Email", "email", $this->edituser->email, "");
		$editform->AddPasswordInput("Password (8 to 20 letters or numbers)", "pword", "", 20);
		$editform->AddPasswordInput("... retype", "rtpword", "", 20);
		$editform->AddLabelLine("User has access to ...", "");
		foreach ($this->edituser->accessAreas as $area=>$digit)
		{	$editform->AddCheckBox($area, "access[$digit]", pow(2, $digit), $this->edituser->CanUserAccess($area), "");
		}
		$editform->AddSubmitButton("", $this->edituser->userid ? "Save Changes" : "Create New User", "submit");
		$editform->Output();
	} // end of fn UserEditForm
	
} // end of defn UserEditPageorm

$page = new UserEditPage();
$page->Page();
?>