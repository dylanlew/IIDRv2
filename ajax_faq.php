<?php 
require_once('init.php');

class FAQAjax extends FAQPage
{		
	function __construct()
	{	parent::__construct();
		echo $this->QuestionsList();
	} // end of fn __construct
	
} // end of defn FAQAjax

$page = new FAQAjax();
?>