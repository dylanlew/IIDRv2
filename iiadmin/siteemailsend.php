<?php
include_once("sitedef.php");

class SiteEmailEditPage extends AdminAKMembersPage
{	var $email;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	$this->email  = new SiteEmail($_GET["id"]);
		$this->css[] = "siteemails.css";
		
		if ($_GET["send"])
		{	if ($sent = $this->email->SendEmail($_SESSION["adminmailist"]))
			{	$this->successmessage = "sent to $sent recipients";
			}
			if ($alreadysent = count($this->email->alreadySent))
			{	$this->failmessage = "not sent to $alreadysent recipients (already sent)";
			}
		}
		
		if ($this->email->id && $_GET["test"])
		{	if ($this->email->SendEmail(array(array("email"=>$this->user->email, "name"=>$this->user->fullname . " [test]")), true))
			{	$this->successmessage = "test email sent to " . $this->user->email;
			}
		}

		$this->breadcrumbs->AddCrumb("siteemails.php", "Site Emails");
		$this->breadcrumbs->AddCrumb("siteemail.php?id={$this->email->id}", 
						$this->InputSafeString($this->email->details["emaildesc"]));
		$this->breadcrumbs->AddCrumb("siteemailsend.php?id={$this->email->id}", "Sending");
		
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	$this->email->IFrameDisplay();
		echo "<p><a href='siteemailsend.php?id=", $this->email->id, "&send=1'>send to ", count($_SESSION["adminmailist"]), " recipients</a></p>\n";
		echo "<p><a href='siteemailsend.php?id=", $this->email->id, "&test=1'>send to me as a test</a></p>\n";
		$this->email->HistoryTable();
	} // end of fn MembersBody
	
} // end of defn AKMembersBody

$page = new SiteEmailEditPage();
$page->Page();
?>