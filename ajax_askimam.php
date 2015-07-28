<?php 
require_once('init.php');

class AskTheImamAjax extends AskTheImamPage
{	
	function __construct()
	{	parent::__construct();
		
		switch ($_GET['action'])
		{	case 'archive_list':
				echo $this->DisplayArchiveList($_GET['limit']);
				break;
			case 'show_question':
				$question = new AskImamQuestion($_GET['qid']);
				echo $question->OutputAnswer($this->user->id);
				break;
		}
		
	} // end of fn __construct
	
	protected function AssignLatestTopic(){}
	protected function AssignFilter(){}
	
} // end of defn AskTheImamAjax

$page = new AskTheImamAjax();
?>