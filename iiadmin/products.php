<?php
include_once('sitedef.php');

class ProductsListPage extends AdminProductsPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	//$this->FilterForm();
		$this->ProductsList();
	} // end of fn ProductsBody
	
	function FilterForm()
	{	class_exists('Form');
		echo '<input type="submit" class="submit" value="Get" /><div class="clear"></div></form>';
	} // end of fn FilterForm
	
	function ProductsList()
	{	
		echo '<table><tr class="newlink"><th colspan="10"><a href="productedit.php">Create new product</a></th></tr><tr><th>Product<br />ID</th><th>Product<br />Code</th><th>&nbsp;</th><th>Title</th><th>Author</th><th>Category</th><th>Live?</th><th>Extra</th><th>Price</th><th>Actions</th></tr>';
		foreach ($this->Products() as $product_row)
		{	$product = new AdminStoreProduct($product_row);
			$extras = array();
			if ($product->details['spoffer'])
			{	$extras[] = 'Special offer';
			}
			if ($product->details['frontpage'])
			{	$extras[] = 'Front page';
			}
			if (!is_array($cats))
			{	$cats = $product->GetAllCategories();
			}
			echo '<tr class="stripe', $i++ % 2, '"><td>', $product->id, '</td><td>', $product->ProductID(), '</td><td>';
			if ($img = $product->HasImage('tiny'))
			{	echo '<img src="', $img, '" />';
			}
			echo '</td><td>', $this->InputSafeString($product->details['title']), '</td><td>', $product->GetAuthorString(), '</td><td>', $cats[$product->details['category']], '</td><td>', $product->details['live'] ? 'Yes' : 'No', '</td><td>', implode('&nbsp;/<br />', $extras), '</td><td class="num">', number_format($product->details['price'], 2), '</td><td><a href="productedit.php?id=', $product->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('storeproducts', $product->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($product->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="productedit.php?id=', $product->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo "</table>";
	} // end of fn ProductsList
	
	function Products()
	{	$products = array();
		$sql = "SELECT * FROM storeproducts ORDER BY id";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$products[] = $row;
			}
		}
		
		return $products;
	} // end of fn Products
	
} // end of defn ProductsListPage

$page = new ProductsListPage();
$page->Page();
?>