<?php

class AdminAskTheExpert extends AskTheExpert
{	
	public function __construct($id = null)
	{
		parent::__construct($id);
	}
	
	public function Get($id)
	{
		parent::Get($id);
		$this->answer = new AdminFAQPost($this->details['answered']);
	}
	
	public function GetAll()
	{
		$questions = array();
		
		if($result = $this->db->Query("SELECT * FROM asktheexpert ORDER BY dateadded DESC"))
		{
			while($row = $this->db->FetchArray($result))
			{
				$questions[] = new AdminAskTheExpert($row);
			}
		}
		
		return $questions;
	}
	
	public function Save($data = array())
	{
		$success = array();
		$fail = array();
		
		if(!$data['ptitle'])
		{
			$fail = "Must enter a question";
		}
		
		if(!$fail)
		{	
			$saved = $this->answer->Save($data);
			$fail = $saved['failmessage'];
			$success = $saved['successmessage'];
			
			if($success && $this->answer->post->id && !$this->details['answered'])
			{
				$this->db->Query("UPDATE asktheexpert SET answered = ". (int)$this->answer->post->id ." 
								  WHERE id = ". (int)$this->id);
			}
		}
		
		return array("failmessage"=>$fail, "successmessage"=>$success);
	}
	
	public function Delete()
	{
		$this->db->Query("DELETE FROM asktheexpert WHERE id = ". (int)$this->id ." ");
		return $this->db->AffectedRows();
	}
	
	public function InputForm()
	{
		$data = array(); 
		
		if($this->id)
		{
			$data = $this->answer->post->details;
			
			if($_POST)
			{
				foreach($_POST as $key => $value)
				{	
					$data[$key] = $value;
				}
			}
		}
		else
		{
			$data = $_POST;	
		}
		
		ob_start();
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'ate_edit');
		$this->AddBackLinkHiddenField($form);
		$form->AddTextInput('Question', 'ptitle', $this->InputSafeString($data['ptitle']), 'long', 255, 1);
		
		$form->AddRawText("<h4>Show in which categories?</h4>");
		$faqcat = new AdminFAQCategory;
		
		foreach($faqcat->GetAll() as $cat)
		{
			$form->AddCheckBox($cat->details['ctitle'], "cat[]", $cat->id, $this->InCategory($cat->id));
		}
		
		$form->AddTextArea('Answer', 'pcontent', $this->InputSafeString($data['pcontent']), '', 0, 0, 8, 60);
		$form->AddHiddenInput('ptype', $this->answer->post->type);
		$form->AddCheckBox('Show on website?', 'live', '1', $data['live']);
		$form->AddSubmitButton('', 'Save', 'submit');
		$form->Output();
		
		return ob_get_clean();
	}
	
	public function InCategory($cat)
	{	
		$categories = $this->answer->post->GetCategories();
		
		foreach($categories as $c)
		{
			if($c->id == $cat)
			{
				return true;
			}
		}
	}
	
}

?>