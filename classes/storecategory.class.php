<?php
class StoreCategory extends Base
{
	public $id = 0;
	public $details = array();
	public $products = array();
	
	public function __construct($id = null)
	{
		parent::__construct();
		
		if(!is_null($id))
		{	$this->Get($id);
		}
	} // end of fn __construct
	
	public function Reset()
	{
		$this->id = 0;
		$this->details = array();
		$this->products = array();
	} // end of fn Reset
	
	public function Get($id)
	{
		$this->Reset();
		
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id['cid'];
		}  else
		{	if ($result = $this->db->Query('SELECT * FROM storecategories WHERE cid=' . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
	} // end of fn Get
	
	public function GetProducts()
	{
		$this->products = array();
		
		$sql = 'SELECT * FROM storeproducts WHERE category = '. (int)$this->id .' AND live = 1';
		
		if ($result = $this->db->Query($sql))
		{
			while ($row = $this->db->FetchArray($result))
			{	$this->products[] = $row;
			//	$this->products[] = new StoreProduct($row);
			}
		}
		
		return $this->products;
	} // end of fn GetProducts
	
} // end of class StoreCategory
?>