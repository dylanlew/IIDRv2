<?php
include_once('sitedef.php');

class ProductMMPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('multimedia');
		$this->js[] = 'admin_product_mm.js';
		$this->css[] = 'course_mm.css';
		$this->breadcrumbs->AddCrumb('productmm.php?id=' . $this->product->id, 'Reviews');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->MultiMediaDisplay();
	} // end of fn ProductsBody
	
} // end of defn ProductMMPage

$page = new ProductMMPage();
$page->Page();
?>