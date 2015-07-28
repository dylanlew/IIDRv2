<?php
include_once("sitedef.php");

class MainAdminMenuPage extends AdminMenuPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AdminMenuBody()
	{	echo $this->ListFEMenus();
	} // end of fn AdminMenuBody
	
	function ListFEMenus()
	{	ob_start();
		echo "<table>\n<tr><th>Menu</th><th>No of first level items</th><th>Actions</th></tr>\n";
		$sql = "SELECT * FROM menus ORDER BY menuname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$menu = new FrontEndMenu($row);
				echo "<tr>\n<td>", $this->InputSafeString($menu->details["menuname"]), "</td>\n<td>", count($menu->items), "</td>\n<td><a href='femenu.php?id=", $menu->id, "'>view</a></td></tr>\n";
			}
		}
		
		echo "</table>\n";
		return ob_get_clean();
	} // end of fn ListFEMenus
	
} // end of defn MainAdminMenuPage

$page = new MainAdminMenuPage();
$page->Page();
?>