<?php
include_once('sitedef.php');

class ProductCatsListPage extends AdminProductsPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		$this->breadcrumbs->AddCrumb('storecategories.php', 'Categories');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	$this->CatsList();
	} // end of fn ProductsBody
	
	function CatsList()
	{	
		echo '<table><tr class="newlink"><th colspan="4"><a href="storecatedit.php">Create new category</a></th></tr><tr><th>Title</th><th>Products</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetCats() as $cat_row)
		{	$cat = new AdminStoreCategory($cat_row);
			echo '<tr class="stripe', $i++ % 2, '"><td>', $this->InputSafeString($cat->details['ctitle']), '</td><td>', count($cat->GetProducts()), '</td><td>', $cat->details['live'] ? 'Yes' : 'No', '</td><td><a href="storecatedit.php?id=', $cat->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('storecategories', $cat->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($cat->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="storecatedit.php?id=', $cat->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo "</table>";
	} // end of fn CatsList
	
	function GetCats()
	{	$products = array();
		$sql = 'SELECT * FROM storecategories ORDER BY cid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$products[] = $row;
			}
		}
		
		return $products;
	} // end of fn GetCats
	
} // end of defn ProductCatsListPage

$page = new ProductCatsListPage();
$page->Page();
?>