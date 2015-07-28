<?php
include_once('sitedef.php');

class ProductDownloadsPage extends AdminProductPage
{	private $download;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('downloads');
		$this->breadcrumbs->AddCrumb('productdownloads.php?id=' . $this->product->id, 'Downloads');
		if ($this->download->id)
		{	$this->breadcrumbs->AddCrumb('productdownload.php?id=' . $this->download->id, $this->InputSafeString($this->download->details['filetitle']));
		} else
		{	$this->breadcrumbs->AddCrumb('productdownload.php?prodid=' . $this->product->id, 'New download');
		}
	} // end of fn ProductsLoggedInConstruct
	
	public function AssignProduct()
	{	$this->download = new AdminStoreProductDownload($_GET['id']);
		if ($this->download->id)
		{	$this->product = new AdminStoreProduct($this->download->details['prodid']);
		} else
		{	$this->product = new AdminStoreProduct($_GET['prodid']);
		}
	} // end of fn AssignProduct
	
	public function ConstructFunctions()
	{	if (isset($_POST['filetitle']))
		{	$saved = $this->download->Save($_POST, $this->product->id, $_FILES['filedownload']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->download->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->download->Delete())
			{	$this->RedirectBack('productdownloads.php?id=' . $this->product->id);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->download->InputForm($this->product->id);
	} // end of fn ProductsBody
	
} // end of defn ProductDownloadsPage

$page = new ProductDownloadsPage();
$page->Page();
?>