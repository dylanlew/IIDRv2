<?php
include_once('sitedef.php');

class ProductEditPage extends AdminProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('edit');
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'pageedit_tiny_mce.js';
		$this->js[] = 'adminproduct.js';
		
	} // end of fn ProductsLoggedInConstruct
	
	public function ConstructFunctions()
	{	if (isset($_POST['title']))
		{	$saved = $this->product->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->product->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->product->Delete())
			{	$this->RedirectBack('products.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->InputForm();
	} // end of fn ProductsBody
	
} // end of defn ProductEditPage

$page = new ProductEditPage();
$page->Page();
?>