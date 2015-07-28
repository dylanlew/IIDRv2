<?php
include_once('sitedef.php');

class AdminSubProductPage extends AdminProductsPage
{	public $product;
	public $product_option = '';
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct($product_option = 'edit')
	{	parent::ProductsLoggedInConstruct();
		$this->product_option = $product_option;
		$this->css[] = 'admincoursepage.css';
		$this->css[] = 'adminproduct.css';
		
		$this->AssignProduct();
		
		$this->ConstructFunctions();
		
		$this->breadcrumbs->AddCrumb('subproducts.php', 'subscriptions');
		if ($this->product->id)
		{	$this->breadcrumbs->AddCrumb('subproductedit.php?id=' . $this->product->id, $this->InputSafeString($this->product->details['title']));
		} else
		{	$this->breadcrumbs->AddCrumb('subproductedit.php', 'creating new subscription product');
		}
	} // end of fn ProductsLoggedInConstruct
	
	public function AssignProduct()
	{	$this->product = new AdminSubscriptionProduct($_GET['id']);
	} // end of fn AssignProduct
	
	public function ConstructFunctions()
	{	
	} // end of fn ConstructFunctions
	
	function ProductsBody()
	{	parent::ProductsBody();
		$this->ProductsBodyMenu();
	//	echo $this->product->InputForm(), $this->product->BundlesList();
	} // end of fn ProductsBody
	
	function ProductsBodyMenu()
	{	if ($this->product->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->product_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div>';
		}
	} // end of fn ProductsBodyMenu
	
	public function BodyMenuOptions()
	{	$options = array();
		if ($this->product->id)
		{	$options['edit'] = array('link'=>'subproductedit.php?id=' . $this->product->id, 'text'=>'Subscription Product');
		//	$options['bundles'] = array('link'=>'subproductbundles.php?id=' . $this->product->id, 'text'=>'Bundles');
		}
		return $options;
	} // end of fn BodyMenuOptions
	
} // end of defn AdminSubProductPage
?>