<?php

class AdminFAQCategory extends FAQCategory
{
	public function __construct($id = null)
	{
		parent::__construct($id);
	}
	
	public function GetQuestions()
	{
		$this->questions = array();
		
		$sql = "SELECT p.* FROM posts p 
				LEFT JOIN faqtocategories fc ON fc.pid = p.pid 
				WHERE fc.cid = ". (int)$this->id;
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$this->questions[] = new FAQPost($row);
			}
		}
		
		return $this->questions;
	}
	
	public function GetAll()
	{
		$categories = array();
		
		if($result = $this->db->Query("SELECT * FROM faqcategories"))
		{
			while($row = $this->db->FetchArray($result))
			{
				$categories[] = new FAQCategory($row);	
			}
		}
		
		return $categories;
	}
	
	
}

?>