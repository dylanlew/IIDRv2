<?php
include_once("sitedef.php");

class TeamPage extends AdminPage
{	var $team;

	function __construct()
	{	parent::__construct();
		if ($this->user->CanUserAccess("team"))
		{	$this->js[] = "tiny_mce/jquery.tinymce.js";
			$this->js[] = "pageedit_tiny_mce.js";
			$this->css[] = "adminpages.css";
			$this->breadcrumbs->AddCrumb("team.php", "Team");
			$this->team = new AdminTeamMembers();
			
			if (isset($_POST["deftext"]))
			{	if ($this->team->SaveDefText($_POST["deftext"]))
				{	$this->successmessage = "Default text saved";
				}
			}
			
		}
		
	} //  end of fn __construct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("team"))
		{	$this->ListMembers();
		}
	} // end of fn AdminBodyMain
	
	function ListMembers()
	{	echo "<div id='stories'><h3><a href='teammember.php'>Add new team member</a></h3><br class='clear' />\n";
		if ($this->team->members)
		{	echo "<table>\n<tr>\n<th></th>\n<th>Name</th>\n<th>Order in list</th>\n<th>Actions</th>\n</tr>\n";
			foreach ($this->team->members as $member)
			{	echo "<tr class='stripe", $i++ % 2, "'>\n<td>";
				if (file_exists($member->ThumbFile()))
				{	echo "<img src='", $member->ThumbSRC(), "?", time(), "' />";
				}
				echo "</td>\n<td>", $this->InputSafeString($member->details["membername"]), "</td>\n<td>", (int)$member->details["listorder"], "</td>\n<td><a href='teammember.php?id=", $member->id, "'>edit</a>&nbsp;|&nbsp;<a href='teammember.php?id=", $member->id, "&del=1'>delete</a></td>\n</tr>\n";
			}
			echo "</table>\n";
		}
		echo "</div>\n";
		$this->team->DefTextInputForm();
	} // end of fn ListStories
	
} // end of defn TeamPage

$page = new TeamPage();
$page->Page();
?>