<?php
include_once('sitedef.php');

class FAQViewPage extends AdminFAQPage
{	private $faq;
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function FAQConstructor()
	{	parent::FAQConstructor();
		$this->faq = new AdminFAQ($_GET['id']);
		
		if (isset($_POST['answer']))
		{	$saved = $this->faq->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($_GET['delete'] && $_GET['confirm'])
		{	if ($this->faq->Delete())
			{	header('location: faqlist.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('faq.php?id=' . $this->faq->id, $this->faq->id ? ('question #' . $this->faq->id) : 'create new');
	} // end of fn FAQConstructor
	
	public function FAQMainContent()
	{	echo $this->faq->InputForm();
	} // end of fn FAQMainContent

} // end of defn FAQViewPage

$page = new FAQViewPage();
$page->Page();
?>