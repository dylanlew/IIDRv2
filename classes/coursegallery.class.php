<?php

class CourseGallery extends Gallery
{
	private $cid;
	
	public function __construct($cid = null)
	{
		parent::__construct();
		
		if(!is_null($cid))
		{
			$this->Get($cid);	
		}
		
	}
	
	public function Get($cid)
	{
		if($result = $this->db->Query("SELECT * FROM gallerytocourse WHERE cid = ". (int)$cid))
		{
			if($row = $this->db->FetchArray($result))
			{	
				$this->cid = (int)$cid;
				return parent::Get($row['gid']);	
			}
		}	
	}
}

?>