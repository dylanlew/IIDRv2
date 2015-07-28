<?php
include_once("sitedef.php");

class AjaxStudentComment extends AdminPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		
		$comment = new AdminStudentComment($_GET['id']);
		switch ($_GET['action'])
		{	case 'save':
					$saved = $comment->AdminSave($_POST);
					if ($saved['failmessage'])
					{	echo '<div class="failmessage">', $saved['failmessage'], '</div>';
					}
					if ($saved['successmessage'])
					{	echo '<div class="successmessage">', $saved['successmessage'], '</div>';
					}
					break;
		}
		echo $comment->AjaxForm();
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxStudentComment

$page = new AjaxStudentComment();
?>