<?php
include_once("sitedef.php");

class NewsStoriesPage extends AdminNewsPage
{	var $stories;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function AdminNewsLoggedInConstruct()
	{	parent::AdminNewsLoggedInConstruct();
		$this->stories = new AdminNewsStories();
	} //  end of fn AdminNewsLoggedInConstruct
	
	function AdminNewsBody()
	{	$this->ListStories();
	} // end of fn AdminNewsBody
	
	function ListStories()
	{	echo "<div>\n<table>\n<tr class='newlink'><th colspan='5'><a href='newsstory.php'>Add new story</a></th></tr>\n<tr>\n<th>Date / Time</th>\n<th>Headline</th>\n<th>Live?</th>\n<th>Languages</th><th>Actions</th>\n</tr>\n";
		if ($this->stories->stories)
		{	foreach ($this->stories->stories as $story)
			{	
				echo "<tr class='stripe", $i++ % 2, $story->details["live"] ? " livenews" : "", "'>\n<td>", date("d/m/Y @ H:i", strtotime($story->details["submitted"])), "</td>\n<td>", $this->InputSafeString($story->details["headline"]), "</td>\n<td>", $story->details["live"] ? "Yes" : "No", "</td>\n<td>", $story->LangUsedString(), "</td>\n<td><a href='newsstory.php?id=", $story->id, "'>edit</a>";
				if ($histlink = $this->DisplayHistoryLink("news", $story->id))
				{	echo "&nbsp;|&nbsp;", $histlink;
				}
				if ($story->CanDelete())
				{	echo "&nbsp;|&nbsp;<a href='newsstory.php?id=", $story->id, "&delete=1'>delete</a>";
				}
				echo "</td>\n</tr>\n";
			}
		}
		echo "</table>\n</div>\n";
	} // end of fn ListStories
	
} // end of defn NewsStoriesPage

$page = new NewsStoriesPage();
$page->Page();
?>