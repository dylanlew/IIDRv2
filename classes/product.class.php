<?php
abstract class Product extends Base
{
	public $id = 0;
	public $details = array();
	
	public function __construct()
	{	parent::__construct();
	} // end of fn LoggedInConstruct
	
	public function Reset()
	{
		$this->id = 0;
		$this->details = array();
	} // end of fn Reset
	
	public function Get($id)
	{
		$this->Reset();
	} // end of fn Get
	
	public function Is($name = '', $statusid = null)
	{	
		$status = new ProductStatus($statusid);
		
		if($status->details['name'] == $name)
		{
			return $status;
		}
	} // end of fn Is
	
	public function AllowPayOnDay()
	{
		return false;
	} // end of fn AllowPayOnDay
	
	public function GetBundles($ptype = '')
	{	$bundles = array();
		$sql = 'SELECT bundles.* FROM bundles, bundleproducts WHERE bundles.bid=bundleproducts.bid AND pid=' . (int)$this->id . ' AND bundleproducts.ptype="' . $this->SQLSafe($ptype) . '" AND bundles.live=1';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$bundles[$row['bid']] = $row;
			}
		}
		return $bundles;
	} // end of fn GetBundles
	
	public abstract function GetLink();
	public abstract function GetName();
	public abstract function GetPrice();
	public abstract function InStock();
	public abstract function IsLive();
	public abstract function HasQty($qty = 0);
	public abstract function UpdateQty($qty = 0);
	public abstract function HasShipping();
	public abstract function HasTax();
	public abstract function ProductID();
} // end of class Product
?>