<?php
include_once('sitedef.php');

class ProductMMPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('mmpurchase');
		$this->js[] = 'admin_product_mmpurchase.js';
		$this->css[] = 'course_mm.css';
		$this->breadcrumbs->AddCrumb('productmmpurchase.php?id=' . $this->product->id, 'Reviews');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->MultiMediaPurchaseDisplay();
	} // end of fn ProductsBody
	
} // end of defn ProductMMPage

$page = new ProductMMPage();
$page->Page();
?>