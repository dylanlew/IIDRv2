<?php
class CartRecord extends BlankItem
{	public $items = array();

	public function __construct($id = '')
	{	parent::__construct($id, 'carts', 'cartid');
	} // fn __construct
	
	public function GetExtra()
	{	$this->GetItems();
	} // end of fn GetExtra
	
	public function GetItems()
	{	$this->items = array();
		if ($this->id)
		{	$sql = 'SELECT cartitems.* FROM cartitems WHERE cartid=' . $this->id . ' ORDER BY addtime, ciid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->items[$row['ciid']] = $row;
				}
			}
		}
	} // end of fn GetItems
	
	public function ResetExtra()
	{	$this->items = array();
	} // end of fn ResetExtra
	
} // end of defn CartRecord
?>