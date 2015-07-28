<?php
class VideoCategory extends Base
{	
	var $details = array();
	var $videos = array();
	var $id = 0;
	
	function __construct($id = 0)
	{	parent::__construct();
		$this->Get($id); 
	} // fn __construct
	
	function Reset()
	{	$this->details = array();
		$this->videos = array();
		$this->id = 0;
	} // end of fn Reset
	
	function Get($id = 0)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id["cid"];
		} else
		{	if ($result = $this->db->Query("SELECT * FROM videocategories WHERE cid=" . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
	} // end of fn Get	
	
	function GetAll()
	{
		$categories = array();
		
		if($result = $this->db->Query("SELECT * FROM videocategories WHERE live = 1"))
		{
			while($row = $this->db->fetchArray($result))
			{
				$categories[] = new VideoCategory($row);
			}
		}
		
		return $categories;
	}
	
	function GetVideos($limit = 0, $order = 'dateadded', $sort = 'DESC')
	{
		$videos = array();
		
		$sql = "SELECT * FROM videos WHERE catid = ". (int)$this->id ." AND live = 1";
		
		if($order != "")
		{
			$sql .= " ORDER BY ". $this->SQLSafe($order) . " ". $this->SQLSafe($sort);
		}
		
		if($limit > 0)
		{
			$sql .= " LIMIT ". (int)$limit;	
		}
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->fetchArray($result))
			{
				$videos[] = new Video($row);
			}
		}
		
		return $videos;
	}
	
	public function CanView()
	{
		return $this->details['live'];	
	}
	
} // end of defn Video
?>