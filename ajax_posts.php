<?php 
require_once('init.php');

class AjaxPostListing extends PostListingPage
{	
	function __construct()
	{	parent::__construct($_GET['ptype']);
		
		echo $this->PostListing();
		
	} // end of fn __construct
	
} // end of defn AjaxPostListing

$page = new AjaxPostListing();
?>