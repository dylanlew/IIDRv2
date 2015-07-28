<?php

class FAQPost extends Post
{
	public $categories = array();
	
	public function __construct($id = null)
	{
		parent::__construct($id, 'faq', 'FAQPost');
	}
	
	public function GetQuestion()
	{
		return $this->details['ptitle'];
	}
	
	public function GetAnswer()
	{
		return $this->details['pcontent'];
	}
	
	public function GetNewestAskTheExpert()
	{
		$ate = new AskTheExpert;
		
		$sql = "SELECT p.* FROM posts p 
				LEFT JOIN faqtocategories fc ON fc.pid = p.pid
				WHERE fc.cid = ". (int)$ate->catid ." AND p.ptype = '". $this->SQLSafe($this->type) ."' AND p.live = 1 LIMIT 1";
				
		if($result = $this->db->Query($sql))
		{
			if($row = $this->db->FetchArray($result))
			{
				return new FAQPost($row);	
			}
		}
	}
	
	public function GetCategories()
	{
		if(!$this->categories)
		{
			$this->categories = array();
			
			$sql = "SELECT c.* FROM faqtocategories fc, faqcategories c WHERE fc.pid = ". $this->id ." AND fc.cid = c.cid";
			
			if($result = $this->db->Query($sql))
			{
				while($row = $this->db->FetchArray($result))
				{
					$this->categories[] = new FAQCategory($row);
				}
			}
		}
		
		return $this->categories;
	}
	
	public function AllowComments()
	{	return false;
	}
}

?>