<?php
include_once('sitedef.php');

class InstructorCatsPage extends AdminInstructorPage
{	
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->css[] = 'adminpages.css';
		$this->breadcrumbs->AddCrumb('instructorcats.php', 'Categories');
	} // end of fn CoursesLoggedInConstruct
	
	public function CoursesBody()
	{	echo $this->CourseCatsList();
	} // end of fn CoursesBody
	
	private function CourseCatsList()
	{	ob_start();
		echo '<table id="pagelist"><tr class="newlink"><th colspan="6"><a href="instructorcatedit.php">Create new category</a></th></tr><tr><th>Name</th><th>No. of people</th><th>Actions</th></tr>';
		foreach ($this->Categories() as $cat_row)
		{	echo $this->CatListLine($cat_row);
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn CourseCatsList
	
	public function CatListLine($cat_row, $class = '', $level = 0)
	{	ob_start();
		$cat = new AdminInstructorCategory($cat_row);
		echo '<tr class="', $class, ' ', $level ? ('child' . $level) : '', '"><td class="pagetitle">', $this->InputSafeString($cat->details['catname']), '</td><td>', count($cat->GetPeople()), '</td><td><a href="instructorcatedit.php?id=', $cat->id, '">edit</a>';
		if ($histlink = $this->DisplayHistoryLink('coursecategories', $cat->id))
		{	echo '&nbsp;|&nbsp;', $histlink;
		}
		if ($cat->CanDelete())
		{	echo '&nbsp;|&nbsp;<a href="instructorcatedit.php?id=', $cat->id, '&delete=1">delete</a>';
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
		$sql = 'SELECT * FROM instructorcats WHERE parentcat=' . (int)$parentcat . ' ORDER BY catname ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[] = $row;
			}
		}
		
		return $cats;	
	} // end of fn Categories
	
} // end of defn InstructorCatsPage

$page = new InstructorCatsPage();
$page->Page();
?>