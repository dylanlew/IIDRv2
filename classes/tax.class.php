<?php
class Tax extends BlankItem
{
	public $id;
	public $details = array();
	
	public function __construct($id = null)
	{	parent::__construct($id, 'taxrates', 'id');
	} // end of fn __construct
	
	public function GetRate()
	{	return $this->details['rate'];
	} // end of fn GetRate
	
	public function Calculate($value = 0)
	{	return $value * ($this->GetRate() / 100);
	} // end of fn Calculate
	
} // end of class Tax
?>