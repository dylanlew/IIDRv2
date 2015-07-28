<?php 
require_once('init.php');

class SubProductPage extends BasePage
{	public $product;
	
	function __construct()
	{	parent::__construct('courses');		
		$this->css[] = 'page.css';	
		$this->css[] = 'course.css';
		
		$this->product = new SubscriptionProduct($_GET['id']);
		$this->AddBreadcrumb('Subscription offers', $this->link->GetLink('subscriptions.php'));
		$this->AddBreadcrumb($this->InputSafeString($this->product->details['title']), $this->product->GetLink());
	
	} // end of fn __construct
	
	function xxMainBodyContent()
	{	$this->VarDump($this->product->details);
		echo '<div class="col3-wrapper"><h2>', $this->InputSafeString($this->product->details['title']), '</h2>', $this->product->BuyButton(), '</div>';
	} // end of fn MainBodyContent
	
	function MainBodyContent()
	{	
		echo '<h1>', $title = $this->InputSafeString($this->product->details['title']), $this->GetSocialLinks(3), '</h1><div id="course_detail_left"><div class="the-content" id="course_detail_overview">', stripslashes($this->product->details['description']), '</div></div><div id="course_detail_right">';
		if ($img = $this->product->HasImage('default'))
		{	echo '<img class="sub_sidebar_image" src="', $img, '" alt="', $title, '" title="', $title, '" />';
		}
		echo '<div class="course_details_sidelist"><h4>Price: </h4><div class="course_details_sidelist_content" style="font-size: 26px;">&pound;', number_format($this->product->details['price'], 2), '</div></div><div class="clear"></div>', $this->product->BuyButton(), '</div><div class="clear"></div>';
	} // end of fn MainBodyContent
	
	function BookButton()
	{
		ob_start();
		
			echo '<a class="course_booknow">Buy now</a>';
			
		
		return ob_get_clean();
	} // end of fn BookButton
	
} // end of defn SubProductPage

$page = new SubProductPage();
$page->Page();
?>