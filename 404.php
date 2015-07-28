<?php 
require_once('init.php');

class NotFoundPage extends BasePage
{	//var $page;
	
	function __construct()
	{	parent::__construct("");
	} // end of fn __construct

	function MainBodyContent()
	{	
		echo "<h2 style='margin-bottom: 70px;'>&nbsp;</h2><div class='course-content-wrapper' style='margin: 0px auto; width: 500px;'><h3>Oops! The page you were looking for, no longer exists.</h3></div>\n";
	} // end of fn MemberBody
	
} // end of defn NotFoundPage

$page = new NotFoundPage();
$page->Page();	
?>