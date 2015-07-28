<?php 
require_once('init.php');

class AskTheExpertAskQuestion extends BasePage
{	
	function __construct()
	{	parent::__construct();
		
		$submission = new AskImamSubmission();
		
		switch ($_GET['action'])
		{	case 'form':
				echo $submission->InputForm($this->user);
				break;
			case 'submit':
				$saved = $submission->CreateFromPost($_POST, $this->user);
				if ($saved['successmessage'])
				{	echo '<p id="asqSuccess">', $saved['successmessage'], '</p>';
				} else
				{	if ($saved['failmessage'])
					{	echo '<p id="asqFail">', $saved['failmessage'], '</p>', $submission->InputForm($this->user);
					}
				}
				break;
		}
		
 	} // end of fn __construct
	
} // end of defn AskTheExpertAskQuestion

$page = new AskTheExpertAskQuestion();
?>