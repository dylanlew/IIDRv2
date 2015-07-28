<?php
include_once("sitedef.php");

class LibCatsPage extends CMSPage
{	var $pages = "";

	function __construct()
	{	parent::__construct("CMS");
	} //  end of fn __construct

	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->breadcrumbs->AddCrumb("libcats.php", "Library Categories");
	} // end of fn CMSLoggedInConstruct

	function CMSBodyMain()
	{	echo $this->CatsList();
	} // end of fn CMSBodyMain
	
	function GetCats()
	{	$cats = array();
		$sql = 'SELECT * FROM libcats WHERE parentid=0 ORDER BY lcorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[] = $row;
			}
		}
		return $cats;
	} // end of fn Get
	
	function CatsList()
	{	ob_start();
		echo '<table id="pagelist"><tr class="newlink"><th colspan="4"><a href="libcatedit.php">new category</a></th></tr><tr><th>Category name</th><th>Slug</th><th>List Order</th><th>Actions</th></tr>';
		foreach ($this->GetCats() as $maincat)
		{	echo $this->CatsListLine($maincat);
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn CatsList
	
	function CatsListLine($cat_row, $level = 0)
	{	ob_start();
		$cat = new AdminLibCat($cat_row);
		echo '<tr><td class="pagetitle">', str_repeat('&nbsp;---&nbsp;', $level), $this->InputSafeString($cat->details['lcname']), '</td><td>', $this->InputSafeString($cat->details['lcslug']), '</td><td>', (int)$cat->details['lcorder'], '</td><td><a href="libcatedit.php?id=', $cat->id, '">edit</a>';
		if ($cat->CanDelete())
		{	echo '&nbsp;|&nbsp;<a href="libcatedit.php?id=', $cat->id, '&delete=1">delete</a>';
		}
		if ($histlink = $this->DisplayHistoryLink('libcats', $cat->id))
		{	echo '&nbsp;|&nbsp;', $histlink;
		}
		echo '</td></tr>';
		if ($cat->subcats)
		{	foreach ($cat->subcats as $subcat)
			{	echo $this->CatsListLine($subcat, $level + 1);
			}
		}
		return ob_get_clean();
	} // end of fn CatsListLine
	
} // end of defn LibCatsPage

$page = new LibCatsPage();
$page->Page();
?>