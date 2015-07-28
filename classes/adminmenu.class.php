<?php
class AdminMenu extends Base
{	var $user = 0;
	var $parent = 0;
	var $menuitems = array();

	function __construct(AdminUser $user, $parent = 0, $showall = false) // constructor
	{	parent::__construct();
		$this->user = $user;
		$this->parent = (int)$parent;
		$this->Get($showall);
	} // end of fn __construct
	
	function Reset()
	{	$this->menuitems = array();
	} // end of fn Reset
	
	function Get($showall = false)
	{	$this->Reset();
	
		$sql = "SELECT * FROM adminmenus WHERE parentid={$this->parent} ORDER BY disporder";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($showall || !$row["menuaccess"] || $this->user->CanUserAccess($row["menuaccess"]))
				{	$this->menuitems[$row["menuid"]] = new AdminMenuItem($this->user, $row, $showall);
				}
			}
		}
		
		return count($this->menuitems);
		
	} // end of fn Get
	
	function MenuListForAdmin()
	{	if ($this->menuitems)
		{	echo "<table>\n<tr><th>Text</th><th>Link (if any)</th><th>Access limited to</th><th>Items</th>",
					"<th>Order</th><th>Actions</th></tr>\n";
			foreach ($this->menuitems as $item)
			{	echo "<tr>\n<td>", $this->InputSafeString($item->details["menutext"]), "</td>\n<td>", 
						$this->InputSafeString($item->details["menulink"]), "</td>\n<td>", 
						$this->InputSafeString($item->details["menuaccess"]), "</td>\n<td>", $item->submenu->menuitems ? count($item->submenu->menuitems) : "--", "</td>\n<td>", (int)$item->details["disporder"], "</td>\n<td><a href='adminmenuitem.php?id=", $item->id, "'>edit</a>";
				if ($item->CanDelete())
				{	echo "&nbsp;|&nbsp;<a href='adminmenuitem.php?id=", $item->id, "&delete=1'>delete</a>";
				}
				echo "</td>\n</tr>\n";
			}
			echo "</table>\n";
		}
		echo "<p><a href='adminmenuitem.php?parent=", $this->parent, "'>create new item</a></p>\n";
	} // end of fn MenuListForAdmin
	
} // end of defn AdminMenu
?>