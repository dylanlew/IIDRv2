<?php
include_once('sitedef.php');

class BundlesListPage extends AdminProductsPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		$this->breadcrumbs->AddCrumb('bundles.php', 'bundles');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	//$this->FilterForm();
		echo $this->BundlesList();
	} // end of fn ProductsBody
	
	function FilterForm()
	{	class_exists('Form');
		echo '<input type="submit" class="submit" value="Get" /><div class="clear"></div></form>';
	} // end of fn FilterForm
	
	function BundlesList()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="6"><a href="bundleedit.php">Create new bundle</a></th></tr><tr><th>Title</th><th>Description</th><th>Products</th><th>Discount</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->Bundles() as $bundle_row)
		{	$bundle = new AdminBundle($bundle_row);
			echo '<tr class="stripe', $i++ % 2, '"><td>', $this->InputSafeString($bundle->details['bname']), '</td><td>', nl2br($this->InputSafeString($bundle->details['bdesc'])), '</td><td>', $bundle->ProductTextList('<br />'), '</td><td>',number_format($bundle->details['discount'], 2), '</td><td>', $bundle->details['live'] ? 'Yes' : 'No', '</td><td><a href="bundleedit.php?id=', $bundle->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('bundles', $bundle->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($bundle->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="bundleedit.php?id=', $bundle->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo "</table>";
		return ob_get_clean();
	} // end of fn BundlesList
	
	function Bundles()
	{	$products = array();
		$sql = "SELECT * FROM bundles ORDER BY bid";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$products[] = $row;
			}
		}
		
		return $products;
	} // end of fn Bundles
	
} // end of defn BundlesListPage

$page = new BundlesListPage();
$page->Page();
?>