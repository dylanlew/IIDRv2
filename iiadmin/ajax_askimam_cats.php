<?php
include_once("sitedef.php");

class AjaxCourse_Cats extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct();
		if ($this->topic->id)
		{	switch ($_GET['action'])
			{	case 'refresh':
					echo $this->topic->CategoriesTable();
					break;
				case 'add':
					$this->topic->AddCategory($_GET['catid']);
					echo $this->CategoriesList();
					break;
				case 'remove':
					$this->topic->RemoveCategory($_GET['catid']);
					echo $this->topic->CategoriesTable();
					break;
				case 'popup':
					echo $this->CategoriesList();
					break;
				default: echo 'action not found: ', $this->InputSafeString($_GET['action']);
			}
		} else echo 'no topic';
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	$this->topic = new AdminAskImamTopic($_GET['id']);
	} // end of fn AssignTopic
	
	private function CategoriesList()
	{	ob_start();
		if ($cats = $this->GetCategories())
		{	echo '<ul class="coursemmList">';
			foreach ($cats as $catid=>$subcats)
			{	echo $this->DisplayLines($catid, $subcats);
			}
			echo '<ul>';
			//$this->VarDump($cats);
		} else
		{	echo 'no categories available';
		}
		return ob_get_clean();
	} // end of fn CategoriesList
	
	public function DisplayLines($catid = 0, $subcats = array())
	{	ob_start();
		$cat = new CourseCategory($catid);
		echo '<li>', $cat->CascadedName();
		if (!$this->topic->cats[$catid])
		{	echo ' - <a onclick="CourseCatAdd(', $this->topic->id, ',', $catid, ')">add this</a>';
		}
		echo '</li>';
		if ($subcats)
		{	foreach ($subcats as $subcatid=>$subsubcats)
			{	echo $this->DisplayLines($subcatid, $subsubcats);
			}
		}
		return ob_get_clean();
	} // end of fn DisplayLines
	
	private function GetCategories($parent = 0)
	{	$cats = array();
		$sql = 'SELECT * FROM coursecategories WHERE parentcat=' . (int)$parent . ' ORDER BY ctitle';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[$row['cid']] = $this->GetCategories($row['cid']);
			}
		}
		
		return $cats;
	} // end of fn GetCategories
	
} // end of defn AjaxCourse_Cats

$page = new AjaxCourse_Cats();
?>