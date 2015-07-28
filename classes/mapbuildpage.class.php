<?php
class MapBuildPage extends AdminPage
{
	function __construct()
	{	parent::__construct("ADMIN");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('technical'))
		{	$this->css[] = 'mapbuild.css';
			$this->AdminMapBuildLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AdminMapBuildLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb("", "Map builder");
	} // end of fn AdminMapBuildLoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("technical"))
		{	$this->AdminMapBuildBody();
		}
	} // end of fn AdminBodyMain

	function AdminMapBuildBody()
	{	/*echo "<ul>\n<li><a href='index.php'>home</a></li>\n",
					"<li><a href='mapdirlist.php'>directories</a></li>\n",
					"<li><a href='mapbuild.php'>build map</a></li>\n",
					"<li><a href='datastructure.php'>database</a></li>\n",
				"</ul><br />\n";*/
	} // end of fn AdminMapBuildBody

} // end of class defn MapBuildPage
?>