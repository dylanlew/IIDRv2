<?php
include_once("sitedef.php");

class AdminStaffCatsPage extends CMSPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("staffcats.php", "Staff categories");
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	$this->CatsList();
	} // end of fn CMSBodyMain
	
	function CatsList()
	{	echo "<table><tr class='newlink'><th colspan='3'><a href='staffcatedit.php'>new staff category</a></th></tr>\n</th><th>Name</th><th>Discount</th><th>Actions</th></tr>";
		$cats = new AdminStaffCategories();
		foreach ($cats->cats as $cat)
		{	
			echo "<tr class='stripe", $i++ % 2, "'>\n<td>", $this->InputSafeString($cat->details["scname"]), "</td>\n<td>";
			if ($cat->details["discpc"])
			{	echo $cat->details["discpc"], "%";
			} else
			{	if ($cat->details["discamount"])
				{	echo number_format($cat->details["discamount"], 2);
				}
			}
			echo "</td>\n<td><a href='staffcatedit.php?id=", $cat->id, "'>edit</a>";
			if ($histlink = $this->DisplayHistoryLink("staffcats", $cat->id))
			{	echo "&nbsp;|&nbsp;", $histlink;
			}
			if ($cat->CanDelete())
			{	echo "&nbsp;|&nbsp;<a href='staffcatedit.php?id=", $cat->id, "&delete=1'>delete</a>";
			}
			echo "</td>\n</tr>\n";
		}
		echo "</table>\n";
	} // end of fn CatsList
	
} // end of defn AdminStaffCatsPage

$page = new AdminStaffCatsPage();
$page->Page();
?>