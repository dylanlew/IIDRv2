<?php
include_once("sitedef.php");
class_exists('Form');

class MapBuilderPage extends MapBuildPage
{	var $mapbuild = Object;

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)
	
	function AdminMapBuildLoggedInConstruct()
	{	parent::AdminMapBuildLoggedInConstruct();
		$this->mapbuild = new MapBuilder();
		if ($_POST["dir"])
		{	$this->BuildDir($_POST["dir"]);
		}
		
		if ($_GET["delobs"])
		{	$this->DeleteObsolete();
		}
		$this->breadcrumbs->AddCrumb("mapdirlist.php", "Code directories");
		$this->breadcrumbs->AddCrumb("mapbuild.php", "Build map");
	} // end of fn AdminMapBuildLoggedInConstruct

	function BuildDir($dir = "")
	{	if ($scanned = $this->mapbuild->ScanDirectory($dir))
		{	$this->successmessage =  "$scanned files scanned - <a href='mapfilelist.php?dir=$dir'>view these</a>";
		} else
		{	$this->failmessage = "no files found";
		}
	} // end of fn BuildDir

	function DeleteObsolete()
	{	$this->successmessage =  $this->mapbuild->DeleteObsolete() . " non-existant files removed";
	} // end of fn DeleteObsolete

	function AdminMapBuildBody()
	{	
		
		$this->DirForm();
		echo "<p><a href='mapbuild.php?delobs=1'>remove non-existant files from database</a></p>\n";
	} // end of fn AdminMapBuildBody

	function DirForm()
	{	$form = new Form("mapbuild.php", "dirForm");
		$form->AddTextInput("directory (relative to this)", "dir", $_GET["dir"] ? urldecode($_GET["dir"] ) : $_POST["dir"], 
								"inpDir");
		$form->AddSubmitButton("", "scan directory");
		$form->OutPut();
	} // end of fn DirForm

} // end of class defn MapBuilder

$page = new MapBuilderPage();
$page->Page();
?>