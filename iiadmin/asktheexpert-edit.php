<?php
include_once("sitedef.php");

class AskTheExpertEditPage extends AdminPage
{	
	private $question;
	private $post;
	
	function __construct()
	{	parent::__construct("CONTENT");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("web content"))
		{	
			$this->breadcrumbs->AddCrumb("asktheexpert.php", "Ask the Expert");
			
			$this->question = new AdminAskTheExpert($_GET['id']);
			
			if(isset($_POST['ptitle']))
			{
				$saved = $this->question->Save($_POST);
				$this->failmessage = $saved['failmessage'];
				$this->successmessage = $saved['successmessage'];
			}
			
			// Find answered question
			if($answerid = $this->question->details['answered'])
			{
				$this->post = new FAQPost($answerid);
			}
			
			if($this->question->id && $_GET['delete'])
			{
				if($this->question->Delete())
				{
					$this->RedirectBack("asktheexpert.php");
				}
				else
				{
					$this->failmessage = "Delete failed";
				}	
			}
				
			
		
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	
		if ($this->user->CanUserAccess("web content"))
		{	
			$this->QuestionOverview();	
			$this->AnswerForm();
		}
	} // end of fn AdminBodyMain
	
	function QuestionOverview()
	{
		echo "<div><label>Name:</label> ". $this->InputSafeString($this->question->details['name']) ."</div>";
		echo "<div><label>Email:</label> ". $this->InputSafeString($this->question->details['email']) ."</div>";
		echo "<div><label>Date asked:</label> ". $this->OutputDate($this->question->details['dateadded'], 'd F Y H:i:s') ."</div>";
		echo "<div><label>IP address:</label> ". $this->InputSafeString($this->question->details['ip']) ."</div>";
		echo "<div><label>Question:</label> ". $this->InputSafeString($this->question->details['message']) ."</div>";
	}
	
	function AnswerForm()
	{
		echo $this->question->InputForm();
	}
	

} // end of defn UserListPage

$page = new AskTheExpertEditPage();
$page->Page();
?>