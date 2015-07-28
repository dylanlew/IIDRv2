<?php
class ProductBundles extends Base
{	private $product;
	private $ptype = '';
	public $bundles = array();
	
	public function __construct($product, $ptype = '', $exclude_bundles = array())
	{	parent::__construct();
		$this->product = $product;
		$this->ptype = $ptype;
		$this->bundles = $this->product->GetBundles();
		if (is_array($exclude_bundles) && $exclude_bundles)
		{	foreach ($exclude_bundles as $exc_id=>$exc_bundle)
			{	if ($this->bundles[$exc_id])
				{	unset($this->bundles[$exc_id]);
				}
			}
		}
	} // end of fn LoggedInConstruct
	
	public function BundlesDisplay()
	{	ob_start();
		if ($this->bundles)
		{	echo '<div class="storeBundles"><ul>';
			foreach ($this->bundles as $bundle_row)
			{	if (($bundle = new Bundle($bundle_row)) && $bundle->products)
				{	$bp_display = array($this->BundleProductDisplay($this->product, $this->ptype));
					$bp_linkvars = array('badd[]=' . $this->product->id, 'btype[]=' . $this->ptype);
					foreach ($bundle->products as $bproduct_row)
					{	if (($bproduct_row['ptype'] != $this->ptype) || ($bproduct_row['pid'] != $this->product->id))
						{	$bp_display[] = $this->BundleProductDisplay($this->GetProduct($bproduct_row['pid'], $bproduct_row['ptype']), $bproduct_row['ptype']);
							$bp_linkvars[] = 'badd[]=' . $bproduct_row['pid'];
							$bp_linkvars[] = 'btype[]=' . $bproduct_row['ptype'];
						}
					}
					echo '<li><h3>', $this->InputSafeString($bundle->details['bname']), '</h3><p>Buy the following items together and save <span>&pound;', number_format($bundle->details['discount'], 2), '</span><a class="addtobasket" href="', SITE_URL, 'cart.php?', implode('&', $bp_linkvars), '">Add these now</a></p><div class="bundleProducts">', implode('<div class="sbSeparator">&plus;</div>', $bp_display), '</div><div class="clear"></div></li>';
				}
			}
			echo '</ul></div>';
		}
		return ob_get_clean();
	} // end of fn BundlesDisplay
	
	private function BundleProductDisplay($bproduct, $ptype = '')
	{	ob_start();
		echo '<div class="sb_', $ptype, '">';
		switch ($ptype)
		{	case 'store': 
					echo '<a href="', $link = $this->link->GetStoreProductLink($bproduct), '"><img src="', ($img = $bproduct->HasImage('thumbnail')) ? $img : (SITE_URL . 'img/products/thumbnail.png'), '" alt="', $title = $this->InputSafeString($bproduct->details['title']), ' - Image" /></a><h4><a href="', $link, '">', $title, '</a></h4>';
					break;
			case 'course': 
					echo '<a href="', $link = $this->link->GetCourseLink($bproduct->course), '"><img src="', ($img = $bproduct->HasImage('thumbnail-small')) ? $img : (SITE_URL . 'img/products/thumbnail.png'), '" alt="', $title = $this->InputSafeString($bproduct->course->content['ctitle']), ' - Image" /></a><h4><a href="', $link, '">', $title, ' - ', $this->InputSafeString($bproduct->ticket->details['tname']), '</a></h4>';
					break;
						
		}
		echo '<div class="bundleProdPrice">Price: &pound;', number_format($bproduct->GetPriceWithTax(), 2), '</div></div>';
		return ob_get_clean();
	} // end of fn BundleProductDisplay
	
} // end of class ProductBundles
?>