<?php
include_once("sitedef.php");

class NewsStoryPage extends AdminNewsPage
{	var $story;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function AdminNewsLoggedInConstruct()
	{	parent::AdminNewsLoggedInConstruct();
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "news_tiny_mce.js";
		$this->css[] = "datepicker.css";
		$this->js[] = "datepicker.js";
		
		$this->story = new AdminNewsStory($_GET["id"]);
		
		if ($_POST["newstext"])
		{	$saved = $this->story->Save($_POST);
			$this->failmessage = $saved["failmessage"];
			$this->successmessage = $saved["successmessage"];
		}
		
		if ($_GET["delete"] && $_GET["confirm"])
		{	if ($this->story->Delete())
			{	header("location: newsstories.php");
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("newsstory.php?id=" . $this->story->id, 
										$this->story->id ? "Editing Story" : "Adding New Story");
	} //  end of fn AdminNewsLoggedInConstruct
	
	function AdminNewsBody()
	{	$this->story->InputForm();
	} // end of fn AdminNewsBody
	
} // end of defn NewsStoryPage

$page = new NewsStoryPage();
$page->Page();
?>