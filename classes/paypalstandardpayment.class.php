<?php

class PayPalStandardPayment extends Base
{
	public $id;
	public $details = array();
	public $fields = array();
	
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
		$this->fields = array();
	}
	
	public function Get($id)
	{
		$this->Reset();
		
		if(is_array($id))
		{
			$this->id = $id['id'];
			$this->details = $id;
			//$this->GetFields();
		}
		else
		{
			if($result = $this->db->Query("SELECT * FROM paypalpayments WHERE id = ". (int)$id))
			{
				if($row = $this->db->FetchArray($result))
				{
					$this->Get($row);	
				}
			}
		}
	}
	
	public function GetFields()
	{
		if(!$this->fields)
		{
			$this->fields = array();
			
			if($result = $this->db->Query("SELECT * FROM paypalpaymentfields WHERE paymentid = ". (int)$this->id))
			{
				while($row = $this->db->FetchArray($result))
				{
					$this->fields[$row['fieldname']] = $row['fieldvalue']; 	
				}
			}
		}
		
		return $this->fields;
	}
	
	public function GetField($name = '')
	{
		if($this->GetFields())
		{
			return $this->fields[$name];
		}
	}
	
	public function IsPaid()
	{
		if($this->GetField('payment_status') == 'Completed')
		{
			return true;
		}
	}
	
	public function GetDate()
	{
		if($date = $this->GetField('payment_date'))
		{
			return strtotime($date);
		}	
	}
	
	public function Save($data = array())
	{
		$flds = array();
		$data_flds = array();
		
		$flds[] = "orderid=". $this->SQLSafe($data['invoice']);
		$flds[] = "txn_id='". $this->SQLSafe($data['txn_id']) ."'";
		$flds[] = "payer_id='". $this->SQLSafe($data['payer_id']) ."'";
		$flds[] = "payment_date=NOW()";
		
		$sql = "INSERT INTO paypalpayments SET ". implode(',', $flds);
		
		if($this->db->Query($sql))
		{
			$id = $this->db->InsertID();
			
			foreach($data as $key => $value)
			{
				$this->db->Query("INSERT INTO paypalpaymentfields 
								  SET paymentid = ". (int)$id .", fieldname = '". $this->SQLSafe($key) ."', fieldvalue = '". $this->SQLSafe($value) ."' ");	
			}
			
			return true;
		}
	
	}
	
	public function GetOrderPayments($orderid = 0)
	{
		$payments = array();
		
		if($result = $this->db->Query("SELECT * FROM paypalpayments WHERE orderid = ". (int)$orderid))
		{
			while($row = $this->db->FetchArray($result))
			{
				$payments[] = new PayPalStandardPayment($row);	
			}
		}
		
		return $payments;
	}
}

?>