<?php
class DeliveryOption extends BlankItem
{	
	public function __construct($id = null)
	{	parent::__construct($id, 'deliveryoptions', 'id');
	} //  end of fn __construct
	
	public function GetRegionName()
	{	$sql = 'SELECT drname FROM delregions WHERE drid=' . (int)$this->details['region'];
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['drname'];
			}
		}
		return 'fallback option';
	} // end of fn GetRegionName
	
	public function GetPrice()
	{
		return $this->details['price'];	
	} //  end of fn GetPrice
	
} // end of fn DeliveryOption
?>