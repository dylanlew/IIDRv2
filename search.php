<?php 
require_once('init.php');
class SearchHomePage extends SearchPage
{	
	function __construct()
	{	parent::__construct();		
		$this->css[] = 'search.css';
		$this->AddBreadcrumb('Search', 'search.php');
		if ($this->term)
		{	$this->AddBreadcrumb($this->InputSafeString($this->term));
		}
	
	} // end of fn __construct
	
} // end of defn SearchHomePage

$page = new SearchHomePage();
$page->Page();
?>