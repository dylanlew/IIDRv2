<?php
include_once('sitedef.php');

class ProductImagesPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('images');
		$this->breadcrumbs->AddCrumb('productimages.php?id=' . $this->product->id, 'Images');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->AdminImagesList();
	} // end of fn ProductsBody
	
} // end of defn ProductImagesPage

$page = new ProductImagesPage();
$page->Page();
?>