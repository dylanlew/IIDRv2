<?php
include_once("sitedef.php");

class AskTheExpertPage extends AdminPage
{	
	private $question;
	private $questions = array();
	
	function __construct()
	{	parent::__construct("CONTENT");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("web content"))
		{	
			$this->breadcrumbs->AddCrumb("asktheexpert.php", "Ask the Expert");
			
			$this->question = new AdminAskTheExpert;
			
			$this->questions = $this->question->GetAll();
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("web content"))
		{	$this->Listing();
		}
	} // end of fn AdminBodyMain
	
	function Listing()
	{
		if($this->questions)
		{
			echo "<table><tr><th>Question</th><th>Asked by</th><th>Date added</th><th>Answered</th><th>Actions</th></tr>";
			
			foreach($this->questions as $q)
			{
				echo "<tr>";
				echo "<td>". $this->InputSafeString($q->details['message']) ."</td>";
				echo "<td>". $this->InputSafeString($q->details['name']) ."</td>";
				echo "<td>". $this->OutputDate($q->details['dateadded'], 'd F Y H:i:s') ."</td>";
				
				if($q->details['answered'])
				{
					$faq = new FAQPost($q->details['answered']);
					echo "<td><a href='". $this->link->GetPostLink($faq) ."'>Yes</a></td>";
				}
				else
				{	echo "<td>No</td>";
				}
				
				echo "<td><a href='asktheexpert-edit.php?id=". $q->id ."'>". ($q->details['answered'] ? "Edit" : "Answer") ."</a> - <a href='asktheexpert-edit.php?id=". $q->id ."&delete=1'>Delete</a></td>";
				echo "</tr>";
			}
			
			echo "</table>";
		}
	}

} // end of defn UserListPage

$page = new AskTheExpertPage();
$page->Page();
?>