<?php
include_once('sitedef.php');

class ProductImagePage extends AdminProductPage
{	private $image;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('images');
		$this->breadcrumbs->AddCrumb('productimages.php?id=' . $this->product->id, 'Images');
		if ($this->image->id)
		{	$this->breadcrumbs->AddCrumb('productimage.php?id=' . $this->image->id, $this->image->details['phototitle'] ? $this->InputSafeString($this->image->details['phototitle']) : ('#' . $this->image->id));
		} else
		{	$this->breadcrumbs->AddCrumb('productimage.php?prodid=' . $this->product->id, 'New image');
		}
	} // end of fn ProductsLoggedInConstruct
	
	public function AssignProduct()
	{	$this->image = new AdminProductPhoto($_GET['id']);
		if ($this->image->id)
		{	$this->product = new AdminStoreProduct($this->image->details['prodid']);
		} else
		{	$this->product = new AdminStoreProduct($_GET['prodid']);
		}
	} // end of fn AssignProduct
	
	public function ConstructFunctions()
	{	if (isset($_POST['phototitle']))
		{	$saved = $this->image->Save($_POST, $this->product->id, $_FILES['photofile']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->image->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->image->Delete())
			{	$this->RedirectBack('productimages.php?id=' . $this->product->id);
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn ConstructFunctions
	
	function ProductsBody()
	{	parent::ProductsBody();
		echo $this->image->InputForm($this->product->id);
	} // end of fn ProductsBody
	
} // end of defn ProductImagePage

$page = new ProductImagePage();
$page->Page();
?>