<?php
class OpinionPost extends Post
{
	public function __construct($id = null)
	{	parent::__construct($id, 'opinions');
	} // fn __construct
	
	public function GetMostPopular($limit = 5)
	{
		$sql = "SELECT COUNT(c.cid) as total, p.* FROM comments c LEFT JOIN posts p ON p.pid = c.pid WHERE p.live = 1 AND p.ptype = 'opinion' ORDER BY total ASC LIMIT " . (int)$limit;
		
		$posts = array();

		if ($result = $this->db->Query($sql))
		{	if ($this->db->NumRows($result))
			{	while ($row = $this->db->FetchArray($result))
				{	$obj = $this->object_type;
					$posts[] = new $obj($row);
				}
			}
		}
		
		return $posts;			
	} // fn GetMostPopular
	
} // end of class OpinionPost
?>