<?php
include_once("sitedef.php");

class AjaxReview extends AdminPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		
		$review = new AdminProductReview($_GET['id']);
		switch ($_GET['action'])
		{	case 'save':
					$saved = $review->AdminSave($_POST);
					if ($saved['failmessage'])
					{	echo '<div class="failmessage">', $saved['failmessage'], '</div>';
					}
					if ($saved['successmessage'])
					{	echo '<div class="successmessage">', $saved['successmessage'], '</div>';
					}
					break;
		}
		echo $review->AjaxForm();
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxReview

$page = new AjaxReview();
?>