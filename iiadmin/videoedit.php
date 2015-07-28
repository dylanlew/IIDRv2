<?php
include_once("sitedef.php");

class VideoEditPage extends AdminPage
{	
	public $video;

	function __construct()
	{	parent::__construct();
		$this->js[] = "adminvideo.js";
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'instructor_tiny_mce.js';
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	
		parent::LoggedInConstruct();
	
		$this->video  = new AdminVideo($_GET["id"]);
		
		if (isset($_POST["vtitle"]))
		{	$saved = $this->video->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	if ($_POST["redirect"])
				{	$redirect = urldecode($_POST["redirect"]);
				} else
				{	$redirect = "videos.php";
				}
				header("location: " . $redirect);
				exit;
			}
		}
		
		if ($this->video->id && $_GET["delete"] && $_GET["confirm"])
		{	if ($this->video->Delete())
			{	header("location: videos.php");
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}
		
		$this->breadcrumbs->AddCrumb("videos.php", "Videos");

		if ($this->video->id)
		{	$this->breadcrumbs->AddCrumb("videoedit.php?id={$this->video->id}", 
						$this->InputSafeString($this->video->details["vtitle"]));
		} else
		{	$this->breadcrumbs->AddCrumb("videoedit.php?id={$this->video->id}", "Creating new video");
		}
	} // end of fn CountriesLoggedInConstruct
	
	function AdminBodyMain()
	{	echo $this->video->InputForm();
		//$this->country->CityList($this->user);
		//$this->country->PaymentOptionsList();
	} // end of fn CountriesBodyMain
	
} // end of defn CountryEditPage

$page = new VideoEditPage();
$page->Page();
?>