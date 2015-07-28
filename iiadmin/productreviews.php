<?php
include_once('sitedef.php');

class ProductEditPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('reviews');
		$this->js[] = 'admin_productreviews.js';
		$this->css[] = 'adminreviews.css';
		$this->breadcrumbs->AddCrumb('productreviews.php?id=' . $this->product->id, 'Reviews');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->ReviewsDisplay();
	} // end of fn ProductsBody
	
} // end of defn ProductEditPage

$page = new ProductEditPage();
$page->Page();
?>