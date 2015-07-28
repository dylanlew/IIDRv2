<?php
include_once('sitedef.php');

class CoursesContentListPage extends AdminCourseContentPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		$this->breadcrumbs->AddCrumb('coursescontent.php', 'Course content');
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	//$this->FilterForm();
		$this->CoursesList();
	} // end of fn CoursesBody
	
	function FilterForm()
	{	class_exists('Form');
		echo '<input type="submit" class="submit" value="Get" /><div class="clear"></div></form>';
	} // end of fn FilterForm
	
	function CoursesList()
	{	$can_sponsors = $this->user->CanUserAccess('sponsors');
		echo '<table><tr class="newlink"><th colspan="7"><a href="coursecontentedit.php">Create new course content</a></th></tr><tr><th>Content ID</th><th></th><th>Title</th><th>Type</th><th>Categories</th><th>Scheduled</th><th>Actions</th></tr>';
		foreach ($this->Courses() as $course)
		{	
			echo '<tr class="stripe', $i++ % 2, '"><td>', (int)$course->id, '</td><td>';
			if ($image = $course->HasImage('thumbnail-small'))
			{	echo '<img width="100px" src="', $image, '?', time(), '" />';
			} else
			{	echo 'no<br />thumbnail';
			}
			echo '</td><td>', $this->InputSafeString($course->details['ctitle']), '</td><td>', $this->InputSafeString($course->details['ctype']), '</td><td>', $course->CategoryDisplayList(), '</td><td>';
			if ($course->courses)
			{	echo '<a href="coursecontentschedule.php?id=', $course->id, '">', count($course->courses), '</a>';
			} else
			{	echo '--';
			}
			echo '</td><td><a href="coursecontentedit.php?id=', $course->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('coursscontent', $course->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($course->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="coursecontentedit.php?id=', $course->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	} // end of fn CoursesList
	
	function Courses()
	{	$courses = array();
		$sql = 'SELECT * FROM coursecontent ORDER BY ctitle';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$courses[] = new AdminCourseContent($row);
			}
		}
		
		return $courses;
	} // end of fn Courses
	
} // end of defn CoursesContentListPage

$page = new CoursesContentListPage();
$page->Page();
?>