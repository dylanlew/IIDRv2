<?php
include_once('sitedef.php');

class ATICategoryListingPage extends AskTheImamPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function ATIConstructor()
	{	parent::ATIConstructor();
		$this->breadcrumbs->AddCrumb('aticatlist.php', 'Categories');
	} // end of fn ATIConstructor
	
	public function ATIMainContent()
	{	echo $this->ListCats();
	} // end of fn ATIMainContent
	
	public function ListCats()
	{	ob_start();
		
		echo '<table><tr class="newlink"><th colspan="4"><a href="aticat.php">New Category</a></th></tr><tr><th>Category name</th><th>FAQ\'s</th><th>List order</th><th>Actions</th></tr>';
		$cats = new ATICats(false, false);
		foreach ($cats->cats as $cat_row)
		{	$cat = new AdminATICat($cat_row);
			echo '<tr><td>', $this->InputSafeString($cat->details['catname']), '</td><td>', count($cat->GetQuestions()), '</td><td>', (int)$cat->details['listorder'], '</td><td><a href="aticat.php?id=' . $cat->id . '">edit</a>';
			if ($cat->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="aticat.php?id=' . $cat->id . '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn ListQuestions

} // end of defn ATICategoryListingPage

$page = new ATICategoryListingPage();
$page->Page();
?>