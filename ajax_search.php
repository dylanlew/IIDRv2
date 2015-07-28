<?php 
require_once('init.php');
class AjaxSearch extends SearchPage
{	
	function __construct()
	{	parent::__construct();		
		echo $this->SearchResults();
	} // end of fn __construct
	
} // end of defn AjaxSearch

$page = new AjaxSearch();
?>