<?php
include_once("sitedef.php");

class MapFileDetailsPage extends MapBuildPage
{	var $mapsearch = Object;
	var $fileid = 0;
	var $file = Array();

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)
	
	function AdminMapBuildLoggedInConstruct()
	{	parent::AdminMapBuildLoggedInConstruct();
		$this->js[] = "mb_collapse.js";
		$this->mapsearch = new MapSearcher();
		$this->fileid = (int)$_GET["fileid"];
		$this->file = $this->mapsearch->GetFileByID($this->fileid);
		$this->breadcrumbs->AddCrumb("mapdirlist.php", "Code directories");
		$this->breadcrumbs->AddCrumb("mapfilelist.php?dir=" . urlencode($this->file["directory"]), $this->InputSafeString($this->file["directory"]));
		$this->breadcrumbs->AddCrumb("mapfilelist.php", $this->InputSafeString($this->file["filename"]));
	} // end of fn AdminMapBuildLoggedInConstruct

	function AdminMapBuildBody()
	{	$this->FileDetails();
	} // end of fn AdminMapBuildBody

	function FileDetails()
	{	
		echo $this->file["filename"], "<br />\n<a href='mapfilelist.php?dir=", urlencode($this->file["directory"]), "'>directory:\"", htmlentities($this->file["directory"]), "\"</a>\n<ul class='classList'>\n";
		foreach ($this->file["classes"] as $classid=>$class)
		{	
			echo "\t<li>class <a onclick='CollpaseObject(\"cl_", $classid, "\");'><b>", $class["name"], " +</b></a>";
			if ($class["extendname"])
			{	echo " extends <b>";
				if ($class["extendid"])
				{	echo "<a href='mapfiledetails.php?fileid=", $class["extendid"], "'>";
				}
				echo $class["extendname"], $class["extendid"] ? "</a>" : "", "</b>";
			}
			echo "<div id='cl_", $classid, "' style='display:none'>\n\t\t<i>vars</i>\n\t\t<ul class='varList'>\n";
			foreach ($class["vars"] as $varid=>$var)
			{	echo "<li>", $var, "</li>\n";
			}
			echo "</ul>\n<i>functions</i>\n<ul class='funcList'>\n";
			foreach ($class["functions"] as $functionid=>$function)
			{	echo "<li>", trim(preg_replace("/^function( |\()/i", "", $function)), "</li>\n";
			}
			echo "</ul></div>\n</li>\n";
		}
		echo "</ul>\n<i>objects created</i><ul class='incList'>\n";
		foreach ($this->file["objects"] as $objid=>$object)
		{	echo "<li>";
			if ($object["objectid"])
			{	echo "<a href='mapfiledetails.php?fileid=", $object["objectid"], "'>";
			}
			echo $object["objectname"], $object["objectid"] ? "</a>" : "", "</li>\n";
		}
		echo "</ul>\n";
		echo "</ul>\n<i>includes</i><ul class='incList'>\n";
		foreach ($this->file["includes"] as $incid=>$inc)
		{	echo "<li>", $inc, "</li>\n";
		}
		echo "</ul>\n";
	} // end of fn FileDetails

} // end of class defn MapFileDetailsPage

$page = new MapFileDetailsPage();
$page->Page();
?>