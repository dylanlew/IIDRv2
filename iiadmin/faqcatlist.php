<?php
include_once('sitedef.php');

class FAQCategoryListingPage extends AdminFAQPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function FAQConstructor()
	{	parent::FAQConstructor();
		$this->breadcrumbs->AddCrumb('faqcatlist.php', 'Categories');
	} // end of fn FAQConstructor
	
	public function FAQMainContent()
	{	echo $this->ListCats();
	} // end of fn FAQMainContent
	
	public function ListCats()
	{	ob_start();
		
		echo '<table><tr class="newlink"><th colspan="5"><a href="faqcat.php">New Category</a></th></tr><tr><th>Category name</th><th>Link</th><th>FAQ\'s</th><th>List order</th><th>Actions</th></tr>';
		$cats = new FAQCats(false, false);
		foreach ($cats->cats as $cat_row)
		{	$cat = new AdminFAQCat($cat_row);
			echo '<tr><td>', $this->InputSafeString($cat->details['catname']), '</td><td><a href="', $link = $cat->Link(), '">', $link, '</a></td><td>', ($count = count($cat->GetFAQ())) ? ('<a href="faqlist.php?cat=' . $cat->id . '">' . $count . '</a>') : '', '</td><td>', (int)$cat->details['listorder'], '</td><td><a href="faqcat.php?id=' . $cat->id . '">edit</a>';
			if ($cat->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="faqcat.php?id=' . $cat->id . '&delete=1">delete</a>';
			}
			echo '&nbsp;|&nbsp;<a href="faq.php?catid=' . $cat->id . '">add FAQ</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn ListQuestions

} // end of defn FAQCategoryListingPage

$page = new FAQCategoryListingPage();
$page->Page();
?>