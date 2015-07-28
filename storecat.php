<?php 
require_once('init.php');

class StoreCategoryPage extends BasePage
{	
	public $store;
	public $category;
	public $products;
	
	function __construct($id = 0)
	{	
		parent::__construct('store');
		$this->css[] = 'storeshelf.css';
		
		$this->store = new Store();
		$this->category = $this->store->GetCategory($id);
		
		$this->AddBreadcrumb('Store', $this->link->GetLink('store.php'));
		
		if(!$this->category->id)
		{	$this->Redirect('store.php');
		} else
		{	$this->AddBreadcrumb($this->category->details['ctitle']);
		}
		
		if ($_GET['add'])
		{	print_r($_GET);
		}
	
	} // end of fn __construct
	
	function MainBodyContent()
	{	
		echo '<div id="sidebar" class="col">', $this->store->StoreMenu($this->category->details['cid']), '</div><div class="col3-wrapper-with-sidebar">', $this->CategoryListing(), '</div>';
	} // end of fn MainBodyContent
	
	function CategoryListing()
	{
		ob_start();
		$this->category->GetProducts();
		$shelf = new StoreShelf($this->category->products);
		echo '<div class="storeShelf"><h1>Welcome to the Store</h1><h2>', $this->InputSafeString($this->category->details['ctitle']), '</h2>', $shelf->DisplayShelf(), '</div>';
		return ob_get_clean();
	}
	
} // end of defn StoreCategoryPage

$page = new StoreCategoryPage($_GET['id']);
$page->Page();
?>