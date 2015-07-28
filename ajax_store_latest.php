<?php 
require_once('init.php');

class AjaxStoreLatest extends Base
{	
	function __construct()
	{	parent::__construct();		
		
		$store = new Store();
		if ($products = $store->GetLatestProducts())
		{	$shelf = new StoreShelf($products);
			echo $shelf->DisplayShelfContents('latest', $this->GetParameter('pag_store_latest') * 3);
		}
	
	} // end of fn __construct
	
} // end of defn AjaxStoreLatest

$page = new AjaxStoreLatest();
?>