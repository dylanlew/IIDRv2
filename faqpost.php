<?php 
require_once('init.php');

class FAQPage extends BasePage
{	
	private $category;
	private $post;
	
	function __construct($id = null)
	{	parent::__construct('faqs');
	
		$this->css[] = 'page.css';
		$this->js[] = 'faqs.js';
		$this->css[] = 'faqs.css';
		
		$this->category = new FAQCategory;
		$this->post = new FAQPost($id);
		
		if(!$this->post->id || $this->post->details['ptype'] != 'faq' || $this->post->CanView() == false)
		{	$this->Redirect('faq.php');
			exit;
		}
		
		$this->AddBreadcrumb("FAQ", $this->link->GetLink("faq.php"));
		
		$this->AddBreadcrumb($this->InputSafeString($this->post->GetQuestion()));
		
		$this->title .= ' - ' . $this->InputSafeString($this->post->GetQuestion());
	} // end of fn __construct
	
	function MainBodyContent()
	{	
	
		echo "<div id='sidebar' class='col'>";
		
		
		echo "<div class='sidebar-menu clearfix'>";
		echo "<h2>Categories</h2>";
		echo $this->category->DisplayCategoryList();
		
		echo "</div>";
		
		echo '<div class="ask-the-expertsidebar clearfix">';
		$ate = new AskTheExpert;
		echo $ate->DisplayAskQuestion();
		echo '</div>';
		
		echo $this->GetSidebarCourses();
		
		echo "</div>";

		echo "<div class='col3-wrapper-with-sidebar'>";
		
		echo "<h1>". $this->InputSafeString($this->post->GetQuestion()) ."</h1>";
		echo $this->post->GetAnswer();
		

		
		echo "</div>";
		
	} // end of fn MainBodyContent
	
	
	
} // end of defn FAQPage

$page = new FAQPage($_GET['id']);
$page->Page();
?>