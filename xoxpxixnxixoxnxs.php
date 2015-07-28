<?php 
require_once('init.php');

class OpinionsListingPage extends PostListingPage
{	
	function __construct()
	{	parent::__construct('opinions');
		
		$this->AddBreadcrumb('Opinions', $this->link->GetLink('opinions.php'));
		if ($year = (int)$_GET['year'])
		{	$this->AddBreadcrumb('Archive ' . $year, SITE_URL . 'opinions-archive/' . $year . '/');
		} else
		{	if ($_GET['cat'] && ($cat = new PostCategory($_GET['cat'])) && $cat->id)
			{	$this->AddBreadcrumb($this->InputSafeString($cat->details['ctitle']), $cat->Link('opinions'));
			}
		}
	} // end of fn __construct
	
	public function PostsSideBar()
	{	ob_start();
		echo '<div id="sidebar" class="col">', 
			$this->GetCategorySubmenu(), 
			$this->GetArchiveSubmenu(), 
			$this->GetSidebarCourses(), 
			$this->GetSidebarQuote(), '</div>';
		return ob_get_clean();
	} // end of fn PostsSideBar
	
} // end of defn OpinionsListingPage

$page = new OpinionsListingPage();
$page->Page();
?>