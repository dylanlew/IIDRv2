<?php
include_once('sitedef.php');

class AjaxProductReviews extends AdminPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		$comments = new AdminStudentComments($_GET['sctype'], $_GET['parentid']);
		echo $comments->CommentsTable();
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxProductReviews

$page = new AjaxProductReviews();
?>