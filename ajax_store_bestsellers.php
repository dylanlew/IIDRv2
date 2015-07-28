<?php 
require_once('init.php');

class AjaxStoreBestSellers extends Base
{	
	function __construct()
	{	parent::__construct();		
		
		$store = new Store();
		if ($products = $store->GetBestSellingProducts())
		{	$shelf = new StoreShelf($products);
			echo $shelf->DisplayShelfContents('bestsellers', $this->GetParameter('pag_store_best') * 3);
		}
	
	} // end of fn __construct
	
} // end of defn AjaxStoreBestSellers

$page = new AjaxStoreBestSellers();
?>