<?php
include_once('sitedef.php');

class ProductEditPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('people');
		$this->js[] = 'admin_productpeople.js';
		$this->breadcrumbs->AddCrumb('productpeople.php?id=' . $this->post->id, 'people');
	//	$this->js[] = 'adminproduct.js';
		
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->PeopleListContainer();
	} // end of fn ProductsBody
	
} // end of defn ProductEditPage

$page = new ProductEditPage();
$page->Page();
?>