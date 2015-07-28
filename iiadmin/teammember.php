<?php
include_once("sitedef.php");

class TeamMemberPage extends AdminPage
{	var $member;

	function __construct()
	{	parent::__construct();
		if ($this->user->CanUserAccess("team"))
		{	$this->js[] = "tiny_mce/jquery.tinymce.js";
			$this->js[] = "pageedit_tiny_mce.js";
			$this->css[] = "adminpages.css";
			
			$this->member = new AdminTeamMember($_GET["id"]);
			if (isset($_POST["membername"]))
			{	$saved = $this->member->Save($_POST, $_FILES["imagefile"]);
				$this->failmessage = $saved["failmessage"];
				$this->successmessage = $saved["successmessage"];
			}
			
			if ($this->member->id && $_GET["del"] && $_GET["confirm"])
			{	if ($this->member->Delete())
				{	header("location: team.php");
					exit;
				}
			}
		
			$this->breadcrumbs->AddCrumb("team.php", "Team");
			$this->breadcrumbs->AddCrumb("teammember.php?id=" . $this->member->id, 
										$this->member->id ? $this->InputSafeString($this->member->details["membername"]) : "Adding New Member");
		}
	} //  end of fn __construct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("team"))
		{	$this->member->InputForm();
		}
	} // end of fn TeamMemberPage
	
} // end of defn NewsStoryPage

$page = new TeamMemberPage();
$page->Page();
?>