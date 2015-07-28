<?php
include_once('sitedef.php');

class SubProductEditPage extends AdminSubProductPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('edit');
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'course_tiny_mce.js';
//		$this->js[] = 'adminproduct.js';
		
	} // end of fn ProductsLoggedInConstruct
	
	public function ConstructFunctions()
	{	if (isset($_POST['title']))
		{	$saved = $this->product->Save($_POST, $_FILES['product_image']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->product->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->product->Delete())
			{	$this->RedirectBack('subproducts.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->product->InputForm();
	} // end of fn ProductsBody
	
} // end of defn SubProductEditPage

$page = new SubProductEditPage();
$page->Page();
?>