<?php
class CourseContent extends Base implements Searchable
{	var $details = array();
	var $cats = array();
	var $id = 0;
	var $imagelocation = '';
	var $imagedir = '';
	var $imagesizes = array();
	var $liveonly = true;
	var $courses = array();
	var $types = array('course'=>'course', 'event'=>'event');
	
	function __construct($id = 0, $liveonly = true)
	{	parent::__construct();
		$this->liveonly = (bool)$liveonly;
		// Images
		$this->imagelocation = SITE_URL . 'img/courses/';
		$this->imagedir = CITDOC_ROOT . '/img/courses/';
		$this->imagesizes['default'] = array(470, 290); 
		$this->imagesizes['banner'] = array(960, 290);
		$this->imagesizes['thumbnail'] = array(245, 160);
		$this->imagesizes['thumbnail-small'] = array(130, 145);
		
		$this->Get($id);
	} // fn __construct
	
	function Reset()
	{	$this->id = 0;
		$this->courses = array();
		$this->details = array();
		$this->cats = array();
	} // end of fn Reset
	
	function Get($id = 0)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id['ccid'];
			$this->GetCategories();
			$this->GetCourses();
		} else
		{	if ($result = $this->db->Query('SELECT * FROM coursecontent WHERE ccid=' . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
	} // end of fn Get
	
	public function GetCourses()
	{	$this->courses = array();
		
		if ($id = (int)$this->id)
		{	$where = array('courses.ccid=' . $id);
			if ($this->liveonly)
			{	$where[] = 'live=1';
			}
			$sql = 'SELECT * FROM courses WHERE ' . implode(' AND ', $where) . ' ORDER BY starttime DESC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->courses[$row['cid']] = $row;
				}
			}
		}
	} // end of fn GetCourses
	
	public function GetMultiMedia($liveonly = true)
	{	$multimedia = array();
		
		$sql = 'SELECT multimedia.* FROM multimedia, courses_mm WHERE multimedia.mmid=courses_mm.mmid AND courses_mm.cid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND multimedia.live=1';
		}
		$sql .= ' ORDER BY multimedia.mmorder, multimedia.posted';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$multimedia[$row['mmid']] = $row;
			}
		}
		
		return $multimedia;
	} // end of fn GetMultiMedia
	
	public function GetCategories()
	{	$this->cats = array();
		
		if ($id = (int)$this->id)
		{	$where = array('coursetocats.courseid=' . $id, 'coursetocats.catid=coursecategories.cid');
			$sql = 'SELECT coursecategories.* FROM coursetocats, coursecategories WHERE ' . implode(' AND ', $where) . ' ORDER BY coursecategories.ctitle DESC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->cats[$row['cid']] = $row;
				}
			}
		}
	} // end of fn GetCategories
	
	function GetAllCategories()
	{
		$categories = array();
		
		if ($result = $this->db->Query('SELECT * FROM coursecategories ORDER BY ctitle ASC'))
		{	if ($this->db->NumRows($result))
			{	while ($row = $this->db->FetchArray($result))
				{	$categories[$row['cid']] = $row['ctitle'];
				}
			}
		}
		
		return $categories;	
	} // end of fn GetAllCategories
	
	function GetAllCategoriesWithCourses()
	{
		$categories = array();
		
		$sql = 'SELECT coursecategories.* FROM coursecategories, coursetocats, courses WHERE coursetocats.catid=coursecategories.cid AND coursetocats.courseid=courses.cid AND courses.live=1 GROUP BY coursecategories.cid ORDER BY coursecategories.ctitle ASC';
		
		if ($result = $this->db->Query($sql))
		{	if ($this->db->NumRows($result))
			{	while ($row = $this->db->FetchArray($result))
				{	$categories[$row['cid']] = $row['ctitle'];
				}
			}
		}
		
		return $categories;	
	} // end of fn GetAllCategoriesWithCourses
	
	function GetVideo()
	{	return ($this->details['cvideo'] && ($v = new MultiMedia($this->details['cvideo'])) && $v->id && $v->details['live']) ? $v : false;
	} // end of fn GetVideo
	
	public function HasImage($size = '')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	} // end of fn HasImage
	
	public function GetImageFile($size = 'default')
	{	return $this->ImageFileDirectory($size) . '/' . (int)$this->id .'.png';
	} // end of fn GetImageFile
	
	public function ImageFileDirectory($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size);
	} // end of fn FunctionName
	
	public function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id .'.png';
	} // end of fn GetImageSRC
	
	public function GetDefaultImage($size = 'default')
	{	return $this->DefaultImageSRC($this->imagesizes[$size]);
	} // end of fn GetDefaultImage
	
	public function GetReviews($exclude = 0)
	{	$reviews = array();
		$where = array('pid=' . (int)$this->id, 'ptype="course"');
		if ($exclude = (int)$exclude)
		{	//$where[] = 'NOT sid=' . $exclude;
		}
		if ($this->liveonly)
		{	$where[] = 'suppressed=0';
		}
		$sql = 'SELECT * FROM productreviews WHERE ' . implode(' AND ', $where) . ' ORDER BY revdate DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$reviews[$row['prid']] = $row;
			}
		}
		return $reviews;
	} // end of fn GetReviews
	
	public function ReviewList($limit = 0, $exclude = 0)
	{	if ($reviewlist = $this->ListProductReviews($this->GetReviews($exclude), 'course', $limit))
		{	return '<h3>Testimonials</h3>' . $reviewlist;
		} else
		{	//return '<h3>Testimonials</h3><div class="reviews_none">There are no testimonials of this course yet</div>';
		}
	} // end of fn ReviewList
	
	public function CategoryDisplayList($sep = ', ')
	{	$catnames = array();
		foreach ($this->cats as $cat)
		{	$catnames[] = $cat['ctitle'];
		}
		return implode($sep, $catnames);
	} // end of fn FunctionName
	
	/** Search Functions ****************/
	public function Search($term)
	{
		$match = ' MATCH(ctitle, cshortoverview, coverview) AGAINST("' . $this->SQLSafe($term) . '") ';
		$sql = 'SELECT *, ' . $match . ' as matchscore FROM courses WHERE ' . $match . ' AND live = 1 ORDER BY matchscore DESC';
		
		$results = array();
		
		if($result = $this->db->Query($sql))
		{	while($row = $this->db->FetchArray($result))
			{	$results[] = new Course($row);	
			}
		}
		
		return $results;
	} // end of fn Search
	
	public function SearchResultOutput()
	{
		echo '<h4><a href="', $this->link->GetCourseLink($this), '">', $this->InputSafeString($this->details['ctitle']), '</a></h4>';
	} // end of fn SearchResultOutput
	
} // end of defn CourseContent
?>