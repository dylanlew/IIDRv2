<?php
include_once('sitedef.php');

class AjaxProductReviews extends AdminProductPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('reviews');
		echo $this->product->ReviewsTable();
	} // end of fn ProductsLoggedInConstruct
	
} // end of defn AjaxProductReviews

$page = new AjaxProductReviews();
?>