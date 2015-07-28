<?php 
require_once('init.php');

class AjaxStoreCat extends Base
{	
	function __construct()
	{	parent::__construct();	
		$category = new StoreCategory($_GET['catid']);
		$store = new Store();
		if ($products = $store->GetCategoryProducts($category->id, 0, $_GET['sortby']))
		{	$shelf = new StoreShelf($products);
			echo $shelf->DisplayShelfContents('cat', $this->GetParameter('pag_store_cat') * 3);
		}
	
	} // end of fn __construct
	
} // end of defn AjaxStoreCat

$page = new AjaxStoreCat();
?>