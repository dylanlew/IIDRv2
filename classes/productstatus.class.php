<?php

class ProductStatus extends Base
{
	public $id;
	public $details = array();
	
	public function __construct($id = null)
	{	
		parent::__construct();
		
		if(!is_null($id))
		{
			$this->Get($id);
		}
	}
	
	public function Reset()
	{
		$this->id = 0;
		$this->details = array();
	}
	
	public function Get($id)
	{
		$this->Reset();
		
		if(is_array($id))
		{
			$this->id = $id['id'];
			$this->details = $id;
		}
		else
		{
			if($result = $this->db->Query("SELECT * FROM productstatus WHERE id = ". (int)$id ." "))
			{
				if($row = $this->db->FetchArray($result))
				{
					$this->Get($row);
				}
			}
		}
	}
	
	public function GetByName($id = '')
	{
		if($result = $this->db->Query("SELECT * FROM productstatus WHERE name = '". $this->SQLSafe($id) ."' "))
		{
			if($row = $this->db->FetchArray($result))
			{
				$this->Get($row);
			}
		}	
	}
	
}

?>