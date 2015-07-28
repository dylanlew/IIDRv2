<?php

class FAQCategory extends Base
{
	public $id = 0;
	public $details = array();
	public $questions = array();
	
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
		$this->questions = array();
	}
	
	public function Get($id)
	{
		$this->Reset();
		
		if (is_array($id))
		{	
			$this->details = $id;
			$this->id = $id["cid"];
		} 
		else
		{	
			if ($result = $this->db->Query("SELECT * FROM faqcategories WHERE cid=" . (int)$id))
			{	
				if ($row = $this->db->FetchArray($result))
				{	
					$this->Get($row);
				}
			}
		}
	}
	
	public function GetQuestions()
	{
		$this->questions = array();
		
		$sql = "SELECT p.* FROM posts p 
				LEFT JOIN faqtocategories fc ON fc.pid = p.pid 
				WHERE fc.cid = ". (int)$this->id ." AND p.live = 1";
		
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
		
		if($result = $this->db->Query("SELECT * FROM faqcategories WHERE live = 1"))
		{
			while($row = $this->db->FetchArray($result))
			{
				$categories[] = new FAQCategory($row);	
			}
		}
		
		return $categories;
	}
	
	public function DisplayCategoryList()
	{
		ob_start();
		
		if($categories = $this->GetAll())
		{
			echo "<ul>";
			
			foreach($categories as $c)
			{
				echo "<li><a href='". $this->link->GetLink('faq.php?id='. $c->details['cid']) ."'>". $this->InputSafeString($c->details['ctitle']) ."</a></li>";	
			}
			
			echo "</ul>";
		}
		return ob_get_clean();
	}
}

?>