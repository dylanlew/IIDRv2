<?php
include_once("sitedef.php");
class MapDirlistPage extends MapBuildPage
{	var $mapsearch = Object;

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)
	
	function AdminMapBuildLoggedInConstruct()
	{	parent::AdminMapBuildLoggedInConstruct();
		$this->mapsearch = new MapSearcher();
		$this->breadcrumbs->AddCrumb("mapdirlist.php", "Code directories");
	} // end of fn AdminMapBuildLoggedInConstruct

	function AdminMapBuildBody()
	{	$this->DirList();
	} // end of fn AdminMapBuildBody

	function DirList()
	{	echo "directories<ul class='dirList'>\n";
		foreach ($this->mapsearch->Directories() as $dir=>$dirdet)
		{	echo "<li><a href='mapfilelist.php?dir=", urlencode($dir), "'>", htmlentities($dir), 
					"</a> - ", $dirdet["filecount"], " files - last scanned ", 
					date("j M y - H:i", mktime(substr($dirdet["lastscan"], 11, 2),
							substr($dirdet["lastscan"], 14, 2),0,substr($dirdet["lastscan"], 5, 2),
							substr($dirdet["lastscan"], 8, 2),substr($dirdet["lastscan"], 0, 4))), 
					" - <a href='mapbuild.php?dir=", urlencode($dir), "'>rebuild this</a></li>\n";
		}
		echo "</ul>\n";
	} // end of fn DirForm

} // end of class defn MapDirlistPage

$page = new MapDirlistPage();
$page->Page();
?>