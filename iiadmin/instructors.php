<?php
include_once('sitedef.php');

class InstructorsListPage extends AdminInstructorPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ConstructFunctions()	
	{	$this->css[] = 'adminctry.css';
	} // end of fn ConstructFunctions
	
	function InstructorBody()
	{	echo $this->FilterForm(), $this->InstructorsList();
	} // end of fn InstructorBody
	
	function FilterForm()
	{	ob_start();
		echo '<form class="akFilterForm"><span>Name</span><input type="text" name="instname" value="', $this->InputSafeString($_GET['instname']), '" /><input type="submit" class="submit" value="Get" /><div class="clear"></div></form>';
		return ob_get_clean();
	} // end of fn FilterForm
	
	function InstructorsList()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="7"><a href="instructoredit.php">Create new instructor</a></th></tr><tr><th></th><th>Name</th><th>Category</th><th>Link</th><th>Show on front</th><th>Visible</th><th>Actions</th></tr>';
		$cats = array();
		foreach ($this->Instructors() as $instructor_row)
		{	$instructor = new AdminInstructor($instructor_row);
			echo '<tr class="stripe', $i++ % 2, '"><td>';
			if (file_exists($instructor->GetImageFile('thumbnail')))
			{	echo '<img width="100px" src="', $instructor->GetImageSRC('thumbnail'), '" />';
			} else
			{	echo 'no photo';
			}
			echo '</td><td>', $this->InputSafeString($instructor->GetFullName()), '</td><td>';
			if ($instructor->details['catid'])
			{	if (!$cats[$instructor->details['catid']])
				{	$cats[$instructor->details['catid']] = new InstructorCategory($instructor->details['catid']);
				}
				echo $cats[$instructor->details['catid']]->CascadedName();
			}
			echo '</td><td>', $instructor->Link(), '</td><td>', $instructor->details['showfront'] ? 'Show front' : '', '</td><td>', $instructor->details['live'] ? 'Live' : '', '</td><td><a href="instructoredit.php?id=', $instructor->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('instructors', $instructor->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($instructor->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="instructoredit.php?id=', $instructor->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
		
	} // end of fn InstructorsList
	
	function Instructors()
	{	$instructors = array();
		$where = array();
		if ($_GET['instname'])
		{	$where[] = 'instname LIKE "%' . $this->SQLSafe($_GET['instname']) . '%"';
		}
		$sql = 'SELECT * FROM instructors';
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY instname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$instructors[] = $row;
			}
		}
		return $instructors;
	} // end of fn Instructors
	
} // end of defn InstructorsListPage

$page = new InstructorsListPage();
$page->Page();
?>