<?php
include_once("sitedef.php");

class PageQuotePage extends AdminPageQuotesPage
{	private $quote;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function PageQuotesConstruct()
	{	parent::PageQuotesConstruct();
		$this->quote = new AdminPageQuote($_GET['id']);
		
		if (isset($_POST['quotetext']))
		{	$saved = $this->quote->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->quote->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->quote->Delete())
			{	$this->RedirectBack('pagequotes.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('pagequote.php?id=' . $this->quote->id, $this->quote->id ? $this->quote->SampleText() : 'new quote');
		
	} // end of fn PageQuotesConstruct
	
	protected function PageQuotesMainContent()
	{	ob_start();
		echo $this->quote->InputForm();
		return ob_get_clean();
	} // end of fn PageQuotesMainContent
	
} // end of defn PageQuotePage

$page = new PageQuotePage();
$page->Page();
?>