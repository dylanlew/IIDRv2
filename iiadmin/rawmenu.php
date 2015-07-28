<?php
include_once("sitedef.php");

class RawMenuPage extends AdminPage
{	
	function __construct()
	{	parent::__construct();
		
	} // end of fn __construct
		
	function AdminBodyMain()
	{	$adminmenu = new AdminMenu($this->user, $_GET["id"]);
		if ($adminmenu->menuitems)
		{	echo "<ul>";
			foreach ($adminmenu->menuitems as $item)
			{	echo "<li>";
				if ($item->details["menulink"])
				{	echo "<a href='", $item->details["menulink"], "'>", $this->InputSafeString($item->details["menutext"]), "</a>";
				} else
				{	echo $this->InputSafeString($item->details["menutext"]);
				}
				if ($item->submenu->menuitems)
				{	echo " - <a href='rawmenu.php?id=", $item->id, "'>sub menu</a>";
				}
				echo "</li>";
			}
			echo "</ul>";
		}
	} // end of fn AdminBodyMain
	
} // end of defn RawMenuPage

$page = new RawMenuPage();
$page->Page();
?>