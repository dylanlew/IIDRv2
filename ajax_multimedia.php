<?php 
require_once('init.php');
class AjaxMultimedia extends MultimediaPage
{	
	function __construct()
	{	parent::__construct();
		echo $this->CategoryTopListing();
	} // end of fn __construct
	
} // end of defn AjaxMultimedia

$page = new AjaxMultimedia();
?>