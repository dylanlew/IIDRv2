<?php
class Bundle extends BlankItem
{	public $products = array();

	public function __construct($id = '')
	{	parent::__construct($id, 'bundles', 'bid');
	} // fn __construct
	
	public function GetExtra()
	{	$this->GetProducts();
	} // end of fn GetExtra
	
	public function GetProducts()
	{	$this->products = array();
		if ($this->id)
		{	$sql = 'SELECT bundleproducts.* FROM bundleproducts WHERE bid=' . $this->id . ' ORDER BY listorder, bpid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->products[$row['bpid']] = $row;
				}
			}
		}
	} // end of fn GetProducts
	
	public function ResetExtra()
	{	$this->products = array();
	} // end of fn ResetExtra
	
	public function IsProductBundled($id = 0, $ptype = '')
	{	foreach ($this->products as $product)
		{	if (($product['pid'] == $id) && ($product['ptype'] == $ptype))
			{	return $product;
			}
		}
		return false;
	} // end of fn IsProductBundled
	
} // end of defn Bundle
?>