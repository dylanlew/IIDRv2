<?php
include_once("sitedef.php");

class AdminMemberDropdownsPage extends CMSPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("memberdropdowns.php", "Registration dropdown lists");
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	$this->AreasList();
	} // end of fn CMSBodyMain
	
	function AreasList()
	{	echo "<table><th></th><th>Text description</th><th>List order</th><th>Languages</th><th>Actions</th></tr>";
		$dropdowns = new AdminMemberDropdowns();
		foreach ($dropdowns->dropdowns as $dropdown)
		{	
			echo "<tr class='stripe", $i++ % 2, "'>\n<td>{", $dropdown->id, "}</td>\n<td>", $this->InputSafeString($dropdown->details["textdesc"]), "</td>\n<td>", (int)$dropdown->details["listorder"], "</td>\n<td>", $dropdown->LangUsedString(), "</td>\n<td><a href='memberdropdown.php?id=", $dropdown->id, "'>edit</a></td>\n</tr>\n";
		}
		echo "</table>\n";
	} // end of fn AreasList
	
} // end of defn AdminMemberDropdownsPage

$page = new AdminMemberDropdownsPage();
$page->Page();
?>