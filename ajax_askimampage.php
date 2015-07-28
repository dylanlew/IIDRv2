<?php 
require_once('init.php');

class AskTheImamAjax extends AskTheImamPage
{	
	function __construct()
	{	parent::__construct();
		
		if ($questions = $this->GetFilteredQuestions())
		{	echo $this->ListQuestions($questions, $_GET['page'], $this->questions_perpage);
		}
		
	} // end of fn __construct
	
} // end of defn AskTheImamAjax

$page = new AskTheImamAjax();
?>