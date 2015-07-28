<?php
include_once('sitedef.php');

class ProductDownloadsPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('downloads');
		$this->breadcrumbs->AddCrumb('productdownloads.php?id=' . $this->product->id, 'Downloads');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->DownloadsList();
	} // end of fn ProductsBody
	
} // end of defn ProductDownloadsPage

$page = new ProductDownloadsPage();
$page->Page();
?>