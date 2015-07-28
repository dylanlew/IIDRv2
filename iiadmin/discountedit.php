<?php
include_once('sitedef.php');

class DiscountEditPage extends AdminDiscountPage
{	var $discount;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function DiscountLoggedInConstruct()
	{	parent::DiscountLoggedInConstruct();
		
		$this->css[] = 'course_edit.css';
	//	$this->js[] = 'course_edit.js';
		$this->css[] = 'admindiscount.css';
	//	$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		$this->js[] = 'admin_discount.js';

		$this->discount  = new AdminDiscountCode($_GET['id']);
		
		if (isset($_POST['discdesc']))
		{	$saved = $this->discount->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->discount->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->discount->Delete())
			{	$this->Redirect('discounts.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		if ($this->discount->id)
		{	$this->breadcrumbs->AddCrumb('discountedit.php?id=' . $this->discount->id, $this->InputSafeString($this->discount->details['disccode']));
		} else
		{	$this->breadcrumbs->AddCrumb('discountedit.php', 'Creating new discount');
		}
	} // end of fn DiscountLoggedInConstruct
	
	function DiscountBody()
	{	echo $this->discount->InputForm();
	} // end of fn DiscountBody
	
} // end of defn DiscountEditPage

$page = new DiscountEditPage();
$page->Page();
?>