<?php
class SubscriptionProduct extends Product implements Searchable
{
	public $imagesizes = array('default'=>array(300, 300), 'thumbnail'=>array(120, 120), 'tiny'=>array(50, 50));
	public $imagelocation = '';
	public $imagedir = '';
	
	public function __construct($id = null)
	{	parent::__construct();
		
		$this->imagelocation = SITE_URL . 'img/subs/';
		$this->imagedir = CITDOC_ROOT . '/img/subs/';
		
		if(!is_null($id))
		{	$this->Get($id);
		}
		
	} // end of fn __construct
	
	public function Get($id = 0)
	{
		$this->Reset();
		
		if (is_array($id))
		{	$this->id = $id['id'];
			$this->details = $id;
		} else
		{	if ($result = $this->db->Query('SELECT * FROM subproducts WHERE id = '. (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
	} // end of fn Get
	
	public function GetBundles()
	{	return parent::GetBundles('sub');
	} // end of fn GetBundles
	
	public function GetDownloads($liveonly = true)
	{	$downloads = array();
		$where = array('prodid=' . (int)$this->id);
		if ($liveonly)
		{	$where[] = 'live=1';
		}
		$sql = 'SELECT * FROM storeproductfiles WHERE ' . implode(' AND ', $where) . ' ORDER BY pfid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$downloads[$row['pfid']] = $row;
			}
		}
		return $downloads;
	} // end of fn GetDownloads
	
	public function GetName()
	{	return $this->details['title'];
	} // end of fn GetName
	
	public function GetPrice()
	{	return $this->details['price'];
	} // end of fn GetPrice
	
	public function GetTax()
	{	if ($this->details['taxid'])
		{	if ($taxx = new Tax($this->details['taxid']))
			{	$tax = $taxx->Calculate($this->details['price']);
			}
		} else
		{	$tax = 0;
		}
		return $tax;
	} // end of fn GetTax
	
	public function GetPriceWithTax()
	{
		if ($this->details['taxid'])
		{	if ($tax = new Tax($this->details['taxid']))
			{	$price = $this->details['price'];
				$price += $tax->Calculate($this->details['price']);	
			}
		} else
		{	$price = $this->GetPrice();	
		}
		
		return $price;
	} // end of fn GetPriceWithTax
	
	public function InStock()
	{	return true;
	} // end of fn InStock
	
	public function HasDownload()
	{	return false;
	} // end of fn HasDownload
	
	public function Is($name = '')
	{	if ($name == 'in_stock')
		{	return true;
		}
		return parent::Is($name, $this->details['status']);	
	} // end of fn Is
	
	public function HasQty($qty = 0)
	{	return true;	
	} // end of fn HasQty
	
	public function HasShipping()
	{	return false;
	} // end of fn HasShipping
	
	public function HasTax()
	{	return $this->details['taxid'];	
	} // end of fn HasTax
	
	
	public function HasImage($size = 'default')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	} // end of fn HasImage
	
	public function GetImageFile($size = 'default')
	{	return $this->ImageFileDirectory($size) . '/' . (int)$this->id .'.png';
	} // end of fn GetImageFile
	
	public function ImageFileDirectory($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size);
	} // end of fn FunctionName
	
	public function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id .'.png';
	} // end of fn GetImageSRC

	public function DefaultImageSRC($size = 'default')
	{	$photo = new ProductPhoto();
		return parent::DefaultImageSRC($this->imagesizes[$size]);
	} // end of fn DefaultImageSRC
	
	public function IsLive()
	{	return (bool)$this->details['live'];	
	} // end of fn IsLive
	
	public function GetAuthor()
	{	return '';	
	} // end of fn GetAuthor
	
	public function GetReviews($exclude = 0, $liveonly = true)
	{	return array();
	} // end of fn GetReviews
	
	public function ReviewList($limit = 1, $exclude = 0)
	{	return '';
	} // end of fn ReviewList
	
	public function GetLink()
	{	return $this->link->GetSubProductLink($this);
	} // end of fn GetLink
	
	public function GetStatus()
	{	return new ProductStatus($this->details['status']);
	} // end of fn GetStatus
	
	public function UpdateQty($qty = 0){}

	public function BuyButton()
	{	ob_start();
		static $bbcount = 0;
		$bbcount++;
		echo '<form id="subBuyButton_', $bbcount, '" method="post" action="', SITE_URL, 'cart.php"><input type="hidden" name="type" value="sub" /><input type="hidden" name="add" value="', $this->id, '" /><input type="hidden" name="qty" value="1" /><a class="course_booknow" onclick="document.getElementById(\'subBuyButton_', $bbcount, '\').submit();">Subscribe now</a></form>';
		return ob_get_clean();
	} // end of fn BuyButton
	
	/** Search Functions ****************/
	public function Search($term)
	{
		
		$match = ' MATCH(title, description) AGAINST("' . $this->SQLSafe($term) . '") ';
		$sql = 'SELECT *, ' . $match . ' as matchscore FROM subproducts WHERE ' . $match . ' AND live = 1 ORDER BY matchscore DESC';
		
		$results = array();
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$results[] = new SubscriptionProduct($row);	
			}
		}
		
		return $results;
	} // end of fn Search
	
	public function SearchResultOutput()
	{
		echo '<h4><a href="', $this->Link(), '">', $this->InputSafeString($this->details['title']), '</a></h4>';
	} // end of fn SearchResultOutput
	
	public function AlsoBoughtProducts($limit = 4)
	{	$users = array();
		$products = array();
		
		// get users who bought this product
		
		// get other items bought by these users
		
		return $products;
	} // end of fn AlsoBoughtProducts
	
	public function ProductID()
	{	return 'SU' . $this->id;
	} // end of fn ProductID
	
} // end of class SubscriptionProduct
?>