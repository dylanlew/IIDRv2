<?php 
class StorePage extends BasePage
{	protected $store;
	protected $category;
	
	public function __construct()
	{	parent::__construct('store');		
		$this->css[] = 'page.css';
		
		$this->store = new Store();
		$this->AssignProductCatgeory();
		$this->AssignBreadcrumbs();
	
	} // end of fn __construct
	
	protected function AssignProductCatgeory()
	{	$this->category = new StoreCategory($_GET['catid']);
	} // end of fn AssignProductCatgeory

	protected function AssignBreadcrumbs()
	{	$this->AddBreadcrumb('Store', $this->link->GetLink('store.php'));
		if ($this->category->id)
		{	$this->AddBreadcrumb($this->category->details['ctitle'], $this->link->GetStoreCategoryLink($this->category));
		}
	} // end of fn AssignBreadcrumbs
	
	function MainBodyContent()
	{	
	} // end of fn MainBodyContent
	
	public function SideMenuSpecialOffers($prodcount = 3)
	{	ob_start();
		if ($products = $this->store->GetSpecialOfferProducts($prodcount))
		{	$shelf = new StoreShelf($products);
			echo '<div id="storeSideSpecialOffers"><h2>Special offers</h2>', $shelf->DisplaySliderShelf(), '<div class="clear"></div></div>';
		}
		return ob_get_clean();
	} // end of fn SideMenuSpecialOffers
	
	public function SideMenuBestSellers($prodcount = 2)
	{	ob_start();
		if ($products = $this->store->GetBestSellingProducts($prodcount))
		{	$shelf = new StoreShelf($products);
			echo '<div id="storeSideBestSellers"><h2>Best Sellers</h2>', $shelf->DisplayShelf('dummy', $prodcount), '<div class="clear"></div></div>';
		}
		return ob_get_clean();
	} // end of fn SideMenuBestSellers
	
} // end of defn StorePage
?>