<?php 
require_once('init.php');

class FAQListingPage extends FAQPage
{		
	function __construct()
	{	parent::__construct('faqs');
		
		$this->css[] = 'page.css';
		$this->js[] = 'faqs.js';
		$this->css[] = 'faqs.css';
		
		$this->AddBreadcrumb('FAQ', $this->link->GetLink('faq.php'));
		if($this->category->id)
		{	$this->AddBreadcrumb($this->category->details['ctitle']);
		}
	} // end of fn __construct
	
	function MainBodyContent()
	{	
		echo '<div id="sidebar" class="col"><div class="sidebar-menu clearfix">', $this->DisplayCategoryList(), '<div class="ask-the-expertsidebar clearfix"></div>', $this->GetSidebarCourses(), '</div><div class="col3-wrapper-with-sidebar"><h2>Frequently Asked Questions ';
		if ($this->category->id)
		{	echo '- ', $this->InputSafeString($this->category->details['catname']);	
		}
		echo '</h2><div id="faqContainer">', $this->QuestionsList(), '</div></div>';
	
	} // end of fn MainBodyContent
	
} // end of defn FAQListingPage

$page = new FAQListingPage();
$page->Page();
?>