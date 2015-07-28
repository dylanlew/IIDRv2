<?php

class AdminFAQPost extends AdminPost
{

	public function __construct($id = null)
	{	
		parent::__construct(new FAQPost($id));
	}
	
	public function Save($data = array())
	{
		$fail = array();
		$success = array();
		$fields = array();
			
		$saved = parent::Save($data);
		
		$fail = $saved['failmessage'];
		$success = $saved['successmessage'];
		
		// save categories
		if($fail == '')
		{	
			
			$existing = array();
			
			foreach($this->post->GetCategories() as $cat)
			{	$existing[] = $cat->id;
			}
			
			$new = array_diff((array)$data['cat'], $existing); 
			
			if ($this->post->id && $new)
			{	
				$admin_actions[] = array("action"=>"Changed categories", "actionfrom"=>"", "actionto"=>"", "actiontype"=>"boolean");
			}
			
			$this->db->Query("DELETE FROM faqtocategories WHERE pid = ". (int)$this->post->id);	
			
			foreach((array)$data['cat'] as $cat)
			{	
				$this->db->Query("INSERT INTO faqtocategories SET cid = ". (int)$cat .", pid = ". (int)$this->post->id);
			}
			
			$this->post->categories = array();
		}
	
		return array("failmessage"=>$fail, "successmessage"=>$success);
	}
	
	
}

?>