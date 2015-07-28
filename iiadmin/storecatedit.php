<?php
include_once('sitedef.php');

class StoreCatEditPage extends AdminProductsPage
{	var $cat;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		
		$this->cat = new AdminStoreCategory($_GET['id']);
		
		if (isset($_POST['ctitle']))
		{	$saved = $this->cat->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->cat->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->cat->Delete())
			{	$this->RedirectBack('storecategories.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('storecategories.php', 'Categories');
		if ($this->cat->id)
		{	$this->breadcrumbs->AddCrumb('storecatedit.php?id=' . $this->cat->id, $this->InputSafeString($this->cat->details['ctitle']));
		} else
		{	$this->breadcrumbs->AddCrumb('storecatedit.php', 'creating new category');
		}
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	//$this->FilterForm();
		echo $this->cat->InputForm();
	} // end of fn ProductsBody
	
} // end of defn StoreCatEditPage

$page = new StoreCatEditPage();
$page->Page();
?>