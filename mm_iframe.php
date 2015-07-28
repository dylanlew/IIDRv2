<?php 
require_once('init.php');

class MMIFramePage extends BasePage
{
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function MainBody()
	{	$mm = new Multimedia($_GET['id']);
		echo $mm->Output($_GET['w'], $_GET['h'], '', $_GET['auto']);
	} // end of fn MainBody
	
	function Footer(){}
	function Header(){}
	
} // end of defn MMIFramePage

$page = new MMIFramePage();
$page->Page();
?>