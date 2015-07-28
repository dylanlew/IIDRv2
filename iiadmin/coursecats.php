<?php
include_once('sitedef.php');

class CourseCatsPage extends AdminCoursesPage
{	
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->css[] = "adminpages.css";
		$this->breadcrumbs->AddCrumb('coursecats.php', 'Categories');
	} // end of fn CoursesLoggedInConstruct
	
	public function CoursesBody()
	{	echo $this->CourseCatsList();
	} // end of fn CoursesBody
	
	private function CourseCatsList()
	{	ob_start();
		echo '<table id="pagelist"><tr class="newlink"><th colspan="6"><a href="coursecatedit.php">Create new category</a></th></tr><tr><th>Name</th><th>Type</th><th>Page link</th><th>No. of courses</th><th>"Ask the Imam" subjects</th><th>Actions</th></tr>';
		foreach ($this->Categories() as $cat_row)
		{	echo $this->CatListLine($cat_row);
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn CourseCatsList
	
	public function CatListLine($cat_row, $class = '', $level = 0)
	{	ob_start();
		$cat = new AdminCourseCategory($cat_row);
		echo '<tr class="', $class, ' ', $level ? ('child' . $level) : '', '"><td class="pagetitle">', $this->InputSafeString($cat->details['ctitle']), '</td><td>', $cat->details['cattype'], '</td><td><a href="', SITE_URL, $cat->Link(), '" target="_blank">', $cat->Link(), '</a></td><td>', count($cat->GetCourses()), '</td><td>', count($cat->GetAskImam()), '</td><td><a href="coursecatedit.php?id=', $cat->id, '">edit</a>';
		if ($histlink = $this->DisplayHistoryLink('coursecategories', $cat->id))
		{	echo '&nbsp;|&nbsp;', $histlink;
		}
		if ($cat->CanDelete())
		{	echo '&nbsp;|&nbsp;<a href="coursecatedit.php?id=', $cat->id, '&delete=1">delete</a>';
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
		$sql = 'SELECT * FROM coursecategories WHERE parentcat=' . (int)$parentcat . ' ORDER BY ctitle ASC';
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