<?php
include_once('sitedef.php');

class BundleEditPage extends AdminProductsPage
{	private $bundle;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
	//	$this->js[] = 'tiny_mce/jquery.tinymce.js';
	//	$this->js[] = 'course_tiny_mce.js';
	//	$this->css[] = 'adminproduct.css';
		$this->js[] = 'adminbundle.js';
		
		$this->bundle = new AdminBundle($_GET['id']);
		
		if (isset($_POST['bname']))
		{	$saved = $this->bundle->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->bundle->id)
		{	
			if (isset($_POST['listorder']) && is_array($_POST['listorder']))
			{	$saved = $this->bundle->AmendProducts($_POST);
				$this->successmessage = $saved['successmessage'];
				$this->failmessage = $saved['failmessage'];
			}
			
			if ($_GET['delete'] && $_GET['confirm'])
			{	if ($this->bundle->Delete())
				{	$this->RedirectBack('products.php');
				} else
				{	$this->failmessage = 'Delete failed';
				}
			}
		}
		
		$this->breadcrumbs->AddCrumb('bundles.php', 'bundles');
		if ($this->bundle->id)
		{	$this->breadcrumbs->AddCrumb('bundleedit.php?id=' . $this->bundle->id, $this->InputSafeString($this->bundle->details['bname']));
		} else
		{	$this->breadcrumbs->AddCrumb('bundleedit.php', 'creating new bundle');
		}
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	//$this->FilterForm();
		echo $this->bundle->InputForm(), $this->bundle->ProductsDisplay();
	} // end of fn ProductsBody
	
} // end of defn BundleEditPage

$page = new BundleEditPage();
$page->Page();
?>