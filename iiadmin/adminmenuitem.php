<?php
include_once("sitedef.php");

class AdminMenuItemPage extends AdminMenuPage
{	var $item = false;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AdminMenuLoggedInConstruct()
	{	parent::AdminMenuLoggedInConstruct();
		
		$this->item = new AdminMenuItem($this->user, $_GET["id"], true);
	
		if (isset($_POST["menutext"]))
		{	$saved = $this->item->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
		}
		
		if ($this->item->id && $_GET["delete"] && $_GET["confirm"])
		{	$parentid = $this->item->details["parentid"];
			if ($this->item->Delete())
			{	if ($parentid)
				{	header("location: adminmenuitem.php?id=$parentid");
				} else
				{	header("location: adminmenu.php");
				}
				exit;
			}
		}
		
		$this->breadcrumbs->AddCrumb("", "Admin Menu");
	} // end of fn AdminMenuLoggedInConstruct
	
	function AdminMenuBody()
	{	echo "<p><a href='";
		if (($parent = $this->item->details["parentid"]) || ($parent = (int)$_GET["parent"]))
		{	echo "adminmenuitem.php?id=", $parent;
		} else
		{	echo "adminmenu.php";
		}
		echo "'>back to parent</a></p>\n";
		if ($this->item->CanDelete())
		{	
			echo "<p><a href='adminmenuitem.php?id=", $this->item->id, "&delete=1", $_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you want to " : "", "delete this menu item</a></p>";
		}
		$this->item->InputForm($_GET["parent"]);
		if ($this->item->id)
		{	$this->item->submenu->MenuListForAdmin();
		}
	} // end of fn AdminMenuBody
	
} // end of defn AdminMenuItemPage

$page = new AdminMenuItemPage();
$page->Page();
?>