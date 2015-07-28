<?php
include_once("sitedef.php");

class MapDirlistPage extends MapBuildPage
{	var $mapsearch = Object;
	var $dir = "";

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)
	
	function AdminMapBuildLoggedInConstruct()
	{	parent::AdminMapBuildLoggedInConstruct();
		$this->mapsearch = new MapSearcher();
		$this->dir = urldecode($_GET["dir"]);
		$this->breadcrumbs->AddCrumb("mapdirlist.php", "Code directories");
		$this->breadcrumbs->AddCrumb("mapfilelist.php", "viewing: " . $this->InputSafeString($this->dir));
	} // end of fn AdminMapBuildLoggedInConstruct

	function AdminMapBuildBody()
	{	$this->DirList();
		$this->SubDirList();
	} // end of fn AdminMapBuildBody

	function DirList()
	{	echo "files in \"", $this->dir, "\":<ul class='dirList'>\n";
		foreach ($this->mapsearch->FileList($this->dir) as $fileid=>$filename)
		{	echo "<li><a href='mapfiledetails.php?fileid=", $fileid, "'>", $filename, "</a></li>\n";
		}
		echo "</ul>\n";
	} // end of fn DirForm

	function SubDirList()
	{	echo "sub-directories in \"", $this->dir, "\":<ul class='dirList'>\n";
		foreach ($this->mapsearch->SubDirList($this->dir) as $subdir)
		{	echo "<li><a href='mapfilelist.php?dir=", urlencode($path = $this->dir . $subdir . "/"), 
					"'>", $path, "</a> - <a href='mapbuild.php?dir=", urlencode($path), "'>rebuild this</a></li>\n";
		}
		echo "</ul>\n";
	} // end of fn DirForm

} // end of class defn MapDirlistPage

$page = new MapDirlistPage();
$page->Page();
?>