<?php 
require_once('init.php');

class GrantsPage extends BasePage
{	private $grant;

	function __construct()
	{	parent::__construct('grants');
		$this->AddBreadcrumb('Grants', $this->page->Link());
		$this->AddBreadcrumb('Application');
		$this->grant = new GrantApp();
		$this->css[] = 'page.css';
		$this->css[] = 'grantform.css';
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		$this->js[] = 'grantform.js';
		$this->js[] = 'webforms/webforms2-p.js';
		
		if (isset($_POST['ga_firstname']))
		{	$saved = $this->grant->SaveFromUser($_POST, $_FILES, $this->user);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		$this->bodyOnLoadJS[] = 'CalculateTotals()';
	} // end of fn __construct
	
	function MainBodyContent()
	{	if (!$this->grant->id)
		{	$grantpage = new PageContent('grant-apply');
			echo $grantpage->HTMLMainContent();
		}
		echo $this->grant->UserInputForm($this->user);
	} // end of fn MemberBody
	
} // end of defn GrantsPage

$page = new GrantsPage();
$page->Page();
?>