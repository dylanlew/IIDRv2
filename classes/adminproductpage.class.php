<?php
include_once('sitedef.php');

class AdminProductPage extends AdminProductsPage
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
		
		if ($this->product->id)
		{	$this->breadcrumbs->AddCrumb('productedit.php?id=' . $this->product->id, $this->InputSafeString($this->product->details['title']));
		} else
		{	$this->breadcrumbs->AddCrumb('productedit.php', 'creating new product');
		}
	} // end of fn ProductsLoggedInConstruct
	
	public function AssignProduct()
	{	$this->product = new AdminStoreProduct($_GET['id']);
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
		{	$options['edit'] = array('link'=>'productedit.php?id=' . $this->product->id, 'text'=>'Product');
			$options['people'] = array('link'=>'productpeople.php?id=' . $this->product->id, 'text'=>'People');
			$options['bundles'] = array('link'=>'productbundles.php?id=' . $this->product->id, 'text'=>'Bundles');
			$options['reviews'] = array('link'=>'productreviews.php?id=' . $this->product->id, 'text'=>'Reviews');
			$options['multimedia'] = array('link'=>'productmm.php?id=' . $this->product->id, 'text'=>'Multimedia');
			$options['images'] = array('link'=>'productimages.php?id=' . $this->product->id, 'text'=>'Images');
			$options['downloads'] = array('link'=>'productdownloads.php?id=' . $this->product->id, 'text'=>'Downloads');
			$options['mmpurchase'] = array('link'=>'productmmpurchase.php?id=' . $this->product->id, 'text'=>'Multimedia Purchased');
			$options['purchases'] = array('link'=>'productpurchases.php?id=' . $this->product->id, 'text'=>'Purchases');
		}
		return $options;
	} // end of fn BodyMenuOptions
	
} // end of defn AdminProductPage
?>