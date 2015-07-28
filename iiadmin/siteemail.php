<?php
include_once("sitedef.php");

class SiteEmailEditPage extends AdminAKMembersPage
{	var $email;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	$this->email  = new SiteEmail($_GET["id"]);
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "siteemail_tiny_mce.js";
		$this->js[] = "adminsiteemail.js";
		$this->css[] = "siteemails.css";
	
		if (isset($_POST["mailbody"]))
		{	$saved = $this->email->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: siteemails.php");
				exit;
			}
		}
		
		if ($this->email->id && $_GET["del"] && $_GET["confirm"])
		{	if ($this->email->Delete())
			{	header("location: siteemails.php");
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
	//	if ($this->email->id && $_GET["test"])
	//	{	$this->email->SendEmail(array(array("email"=>$this->user->email, "name"=>$this->user->fullname . " [test]")), true);
	//	}

		$this->breadcrumbs->AddCrumb("siteemails.php", "Site Emails");
		if ($this->email->id)
		{	$this->breadcrumbs->AddCrumb("siteemail.php?id={$this->email->id}", 
						$this->InputSafeString($this->email->details["emaildesc"]));
		} else
		{	$this->breadcrumbs->AddCrumb("siteemail.php", "Creating new email");
		}
		
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	$this->email->InputForm();
		$this->email->HistoryTable();
	} // end of fn AKMembersBody
	
} // end of defn SiteEmailEditPage

$page = new SiteEmailEditPage();
$page->Page();
?>