<?php
include_once('sitedef.php');

class FAQViewPage extends AdminFAQPage
{	private $faqcat;
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function FAQConstructor()
	{	parent::FAQConstructor();
		$this->faqcat = new AdminFAQCat($_GET['id']);
		if (isset($_POST['catname']))
		{	$saved = $this->faqcat->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($_GET['delete'] && $_GET['confirm'])
		{	if ($this->faqcat->Delete())
			{	header('location: faqcatlist.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('faqcatlist.php', 'Categories');
		$this->breadcrumbs->AddCrumb('faqcat.php?id=' . $this->faqcat->id, $this->faqcat->id ? $this->InputSafeString($this->faqcat->details['catname']) : 'create new');
	} // end of fn FAQConstructor
	
	public function FAQMainContent()
	{	echo $this->faqcat->InputForm();
	} // end of fn FAQMainContent
	
} // end of defn FAQViewPage

$page = new FAQViewPage();
$page->Page();
?>