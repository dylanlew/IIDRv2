<?php 
require_once('init.php');

class AjaxStoreAlsoBought extends Base
{	
	function __construct()
	{	parent::__construct();		
		//print_r($_GET);
		//$store = new Store();
		$product = $this->GetProduct($_GET['id'], 'store');
		if ($products = $product->AlsoBoughtProducts())
		{	$shelf = new StoreShelf($products);
			echo $shelf->DisplayShelfContents('alsobought', 3);
		}
	
	} // end of fn __construct
	
} // end of defn AjaxStoreAlsoBought

$page = new AjaxStoreAlsoBought();
?>