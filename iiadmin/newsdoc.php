<?php
include_once("sitedef.php");

class NewsDocPage extends AdminPage
{	var $doc;

	function __construct()
	{	parent::__construct();
		$this->css[] = "adminnews.css";
		
		$this->doc = new NewsDoc($_GET["id"]);
		
		if (isset($_POST["docname"]) && $_FILES["newsdoc"])
		{	$saved = $this->doc->Save($_POST, $_FILES["newsdoc"]);
			$this->failmessage = $saved["failmessage"];
			$this->successmessage = $saved["successmessage"];
		}
		
		$this->breadcrumbs->AddCrumb("newsdocs.php", "Documents for Download");
		$this->breadcrumbs->AddCrumb("newsdoc.php?id=" . $this->doc->id, 
										$this->doc->id ? "Editing Document" : "Adding New Document");
	} //  end of fn __construct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("news") || $this->user->CanUserAccess("web content"))
		{	$this->doc->UploadForm();
		}
	} // end of fn AdminBodyMain
	
} // end of defn NewsDocPage

$page = new NewsDocPage();
$page->Page();
?>