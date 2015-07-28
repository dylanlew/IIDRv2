<?php
include_once('sitedef.php');

class SubProductsListPage extends AdminProductsPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		$this->breadcrumbs->AddCrumb('subproducts.php', 'subscriptions');
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
		echo '<table><tr class="newlink"><th colspan="8"><a href="subproductedit.php">Create new product</a></th></tr><tr><th>Product ID</th><th>&nbsp;</th><th>Title</th><th>Months duration</th><th>Price</th><th>List order</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->Products() as $product_row)
		{	$product = new AdminSubscriptionProduct($product_row);
			echo '<tr class="stripe', $i++ % 2, '"><td>', $product->id, '</td><td>';
			if ($img = $product->HasImage('tiny'))
			{	echo '<img src="', $img, '" />';
			}
			echo '</td><td>', $this->InputSafeString($product->details['title']), '</td><td class="num">', (int)$product->details['months'], '</td><td class="num">', number_format($product->details['price'], 2), '</td><td>', (int)$product->details['listorder'], '</td><td>', $product->details['live'] ? 'Yes' : 'No', '</td><td><a href="subproductedit.php?id=', $product->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('subproducts', $product->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($product->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="subproductedit.php?id=', $product->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo "</table>";
	} // end of fn ProductsList
	
	function Products()
	{	$products = array();
		$sql = "SELECT * FROM subproducts ORDER BY listorder, id";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$products[] = $row;
			}
		} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		
		return $products;
	} // end of fn Products
	
} // end of defn SubProductsListPage

$page = new SubProductsListPage();
$page->Page();
?>