<?php
include_once('sitedef.php');

class ProductBundlesPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('bundles');
		$this->breadcrumbs->AddCrumb('productbundles.php?id=' . $this->product->id, 'Bundles');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->BundlesList();
	} // end of fn ProductsBody
	
} // end of defn ProductBundlesPage

$page = new ProductBundlesPage();
$page->Page();
?>