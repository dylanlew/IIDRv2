<?php

class StoreDownload extends Base
{
	public $id;
	public $details = array();
	public $downloaddir = "";
	
	public function __construct($id = null)
	{
		parent::__construct();
		
		$this->downloaddir = CITDOC_ROOT . "/storedownloads/";
		
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
			if($result = $this->db->Query("SELECT * FROM storedownloads WHERE id = ". (int)$id))
			{
				if($row = $this->db->FetchArray($result))
				{
					$this->Get($row);	
				}
			}
		}
	}
	
	public function GetExtension()
	{
		return pathinfo($this->details['filename'], PATHINFO_EXTENSION);
	}
	
	public function GetFilename()
	{
		//return substr(md5($this->id), 0, 15) . '.'. $this->GetExtension();
		$filename = preg_replace("/[^A-Za-z0-9]/", "-", strtolower($this->details['title']));
		return $this->id .'-'. $filename . '.' . $this->GetExtension();
	}
	
	private function GetFileSRC()
	{
		return $this->downloaddir . $this->details['filename'];
	}
	
	public function CanDownload($userid = 0)
	{
		$sql = "SELECT o.* FROM storeorderitems oi 
				INNER JOIN storeorders o ON oi.orderid = o.id 
				LEFT JOIN storedownloads d ON d.pid = oi.pid 
				WHERE o.sid = ". (int)$userid ." AND oi.pid = ". $this->id ." AND oi.ptype = 'store'";
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$o = new StoreOrder($row);
				
				if($o->IsPaid())
				{
					return $o->id;
				}
			}
		}
	}
	
	public function Download()
	{	
		header("Pragma: ");
		header("Cache-Control: ");
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='. $this->GetFilename());
		header("Content-Length: ". filesize($this->GetFileSRC()));
		
		set_time_limit(0);
		
		if($fp = fopen($this->GetFileSRC(), "rb"))
		{
			while(!feof($fp))
			{
				print(fread($fp, 1024*8));
				flush();	
			}
			
			fclose($fp);
		}
		
		exit;
	}
}

?>