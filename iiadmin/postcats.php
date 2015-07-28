<?php
include_once('sitedef.php');

class CourseCatsPage extends AdminPostsPage
{	
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function PostLoggedInConstruct()
	{	parent::PostLoggedInConstruct();
		$this->breadcrumbs->AddCrumb('postcats.php', 'Categories');
	} // end of fn PostLoggedInConstruct
	
	public function PostBodyMain()
	{	echo $this->CourseCatsList();
	} // end of fn PostBodyMain
	
	private function CourseCatsList()
	{	ob_start();
		echo '<table id="pagelist"><tr class="newlink"><th colspan="4"><a href="postcatedit.php">Create new category</a></th></tr><tr><th>Name</th><th>Page link</th><th>No. of posts</th><th>Actions</th></tr>';
		foreach ($this->Categories() as $cat_row)
		{	echo $this->CatListLine($cat_row);
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn CourseCatsList
	
	public function CatListLine($cat_row, $class = '', $level = 0)
	{	ob_start();
		$cat = new AdminPostCategory($cat_row);
		echo '<tr class="', $class, ' ', $level ? ('child' . $level) : '', '"><td class="pagetitle">', $this->InputSafeString($cat->details['ctitle']), '</td><td><a href="', SITE_URL, $cat->Link(), '" target="_blank">', $cat->Link(), '</a></td><td>', count($cat->GetPosts()), '</td><td><a href="postcatedit.php?id=', $cat->id, '">edit</a>';
		if ($histlink = $this->DisplayHistoryLink('postcategories', $cat->id))
		{	echo '&nbsp;|&nbsp;', $histlink;
		}
		if ($cat->CanDelete())
		{	echo '&nbsp;|&nbsp;<a href="postcatedit.php?id=', $cat->id, '&delete=1">delete</a>';
		}
		echo '</td></tr>';
		if ($subcats = $this->Categories($cat->id))
		{	foreach ($subcats as $subcat_row)
			{	echo $this->CatListLine($subcat_row, 'child', $level + 1);
			}
		}
		return ob_get_clean();
	} // end of fn CatListLine
	
	private function Categories($parentcat = 0)
	{	$cats = array();
		$sql = 'SELECT * FROM postcategories WHERE parentcat=' . (int)$parentcat . ' ORDER BY ctitle ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[] = $row;
			}
		}
		
		return $cats;	
	} // end of fn Categories
	
} // end of defn CourseCatsPage

$page = new CourseCatsPage();
$page->Page();
?>