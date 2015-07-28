<?php
class Instructor extends BlankItem implements Searchable
{	var $imagelocation = '';
	var $imagedir = '';
	var $imagesizes = array('default'=>array(300, 400), 'thumbnail'=>array(231, 247));
	
	function __construct($id = 0)
	{	parent::__construct($id, 'instructors', 'inid');
		$this->imagelocation = SITE_URL . 'img/instructors/';
		$this->imagedir = CITDOC_ROOT . '/img/instructors/';
	} // fn __construct
	
	function HasImage($size = '')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	}
	
	function GetImageFile($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}
	
	function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}

	public function DefaultImageSRC($size = 'default')
	{	return parent::DefaultImageSRC($this->imagesizes[$size]);
	} // end of fn DefaultImageSRC
	
	function GetCourses()
	{	$courses = array();
		if ((int)$this->id)
		{	$sql = 'SELECT courses.* FROM courseinstructors, courses WHERE courseinstructors.cid=courses.cid AND courseinstructors.inid=' . (int)$this->id . ' ORDER BY courses.starttime';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$courses[$row['cid']] = $row;
				}
			}
		}
		return $courses;
	} // end of fn GetCourses
	
	function GetPosts($live_only = true)
	{	$posts = array();
		if ((int)$this->id)
		{	$where = array('postinstructors.pid=posts.pid', 'postinstructors.inid=' . (int)$this->id);
			$sql = 'SELECT posts.* FROM postinstructors, posts WHERE ' . implode(' AND ', $where) . ' ORDER BY posts.pdate DESC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$posts[$row['pid']] = $row;
				}
			}
		}
		return $posts;
	} // end of fn GetPosts
	
	public function GetGalleries()
	{	$galleries = array();
		
		$where = array('galleries.gid=gallerytoinstructor.gid', 'gallerytoinstructor.inid=' . (int)$this->id);
		
		$sql = 'SELECT galleries.* FROM galleries, gallerytoinstructor WHERE ' . implode(' AND ', $where) . ' ORDER BY galleries.gid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$galleries[$row['gid']] = $row;
			}
		}
		return $galleries;
	} // end of fn GetGalleries
	
	public function GetReviews($exclude = 0)
	{	$reviews = array();
		$where = array('pid=' . (int)$this->id, 'ptype="instructor"');
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
		{	//return '<h3>Testimonials</h3><div class="reviews_none">There are no testimonials for ' . $this->InputSafeString($this->details['instname']) . ' yet</div>';
		}
	} // end of fn ReviewList
	
	function GetFullName()
	{	$name = array();
		if ($this->details['insttitle'])
		{	$name[] = $this->details['insttitle'];
		}
		if ($this->details['instname'])
		{	$name[] = $this->details['instname'];
		}
		return implode(' ', $name);
	} // end of fn GetFullName
	
	function GetInterviewsAndPosts($live_only = true)
	{	$ivposts = array();
		if ($interviews = $this->GetInterviews($live_only))
		{	foreach ($interviews as $iv)
			{	$ivposts[] = array('type'=>'interview', 'date'=>$iv['ivdate'], 'item'=>$iv);
			}
		}
		if ($posts = $this->GetPosts($live_only))
		{	foreach ($posts as $post)
			{	$ivposts[] = array('type'=>'post', 'date'=>$post['pdate'], 'item'=>$post);
			}
		}
		return $ivposts;	
	} // end of fn GetInterviewsAndPosts
	
	function GetInterviews($live_only = true)
	{	$interviews = array();
		$where = array('inid=' . (int)$this->id);
		if ($live_only)
		{	$where[] = 'live=1';
		}
		$sql = 'SELECT * FROM instinterviews WHERE ' . implode(' AND ', $where) . ' ORDER BY ivdate DESC, ivid DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$interviews[$row['ivid']] = $row;
			}
		}
		return $interviews;	
	} // end of fn GetInterview
	
	function GetTestimonials($limit = 0)
	{	$testimonials = array();
		
		$sql = 'SELECT * FROM instructortestimonials WHERE instid = ' . (int)$this->id . ' ORDER BY dateadded DESC';
		if($limit = (int)$limit)
		{	$sql .= ' LIMIT 0,' . $limit;
		}
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$testimonials[] = new InstructorTestimonial($row);	
			}
		}
		
		return $testimonials;
	} // end of fn GetTestimonials
	
	/** Search Functions ****************/
	public function Search($term)
	{
		$match = 'MATCH(instname, instbio) AGAINST("' . $this->SQLSafe($term) . '") ';
		$sql = 'SELECT *, ' . $match . ' as matchscore FROM instructors WHERE ' . $match . ' AND live = 1 ORDER BY matchscore DESC';
		
		$results = array();
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$results[] = new Instructor($row);	
			}
		}
		
		return $results;
	} // end of fn Search
	
	public function SearchResultOutput()
	{
		echo '<h4><span>People</span><a href="', $link = $this->Link(), '">', $this->InputSafeString($this->GetFullName()), '</a></h4><p><a href="', $link, '">read more ...</a></p>';
	} // end of fn SearchResultOutput
	
	function Link()
	{	return $this->link->GetInstructorLink($this);
	} // end of fn Link
	
	public function GetMultiMedia($liveonly = true)
	{	$multimedia = array();
		
		$sql = 'SELECT multimedia.* FROM multimedia, instructors_mm WHERE multimedia.mmid=instructors_mm.mmid AND instructors_mm.inid=' . $this->id;
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
	
	public function GetMultiMediaAll($liveonly = true)
	{	$multimedia = $this->GetMultiMedia($liveonly);
		
		$sql = 'SELECT multimedia.* FROM multimedia, multimediapeople WHERE multimedia.mmid=multimediapeople.mmid AND multimediapeople.inid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND multimedia.live=1';
		}
		$sql .= ' ORDER BY multimedia.mmorder, multimedia.posted';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$multimedia[$row['mmid']])
				{	$multimedia[$row['mmid']] = $row;
				}
			}
		}
		
		return $multimedia;
	} // end of fn GetMultiMediaAll
	
	public function GetActivities($liveonly = true)
	{	$activities = array();
		
		$sql = 'SELECT * FROM instactivities WHERE inid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND live=1';
		}
		$sql .= ' ORDER BY actdate DESC';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$activities[$row['iaid']] = $row;
			}
		}
		
		return $activities;
	} // end of fn GetActivities

	public function ShortText($length = 120)
	{	$text = strip_tags(stripslashes($this->details['instbio']));
		if (strlen($text) > $length)
		{	return $this->InputSafeString(substr($text, 0, $length)) . ' ...';
		} else
		{	return $this->InputSafeString($text);
		}
	} // end of fn ShortText
	
	public function GetAllEvents($limit = 0, $liveonly = true)
	{	$events = array();
		// first get all courses / events
		$tables = array('courses', 'courseinstructors');
		$where = array('courses.cid=courseinstructors.cid', 'courseinstructors.inid=' . (int)$this->id);
		
		if ($liveonly)
		{	$where[] = 'courses.live=1';
		}
		
		$sql = 'SELECT courses.* FROM ' . implode(',', $tables) . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY starttime DESC';
		if ($limit = (int)$limit)
		{	$sql .= ' LIMIT ' . $limit;
		}
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$course = new Course($row);
				$events[] = array('type'=>$course->content['ctype'], 'title'=>$this->InputSafeString($course->content['ctitle']), 'date'=>$course->details['starttime'], 'link'=>'<a href="' . $course->Link() . '">', 'subtitle'=>$course->GetDateVenue(', ', 'D jS M Y'), 'img'=>(($src = $course->HasImage('thumbnail')) || ($src = $course->GetDefaultImage('thumbnail-small'))) ? $src : '');
				$courses_found = true;
			}
		}
		
		// now get activities
		$sql = 'SELECT * FROM instactivities WHERE inid=' . (int)$this->id . ' AND live=1 ORDER BY actdate DESC';
		if ($limit)
		{	$sql .= ' LIMIT ' . $limit;
		}
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	
				$events[] = array('type'=>'activity', 'title'=>$this->InputSafeString($row['acttitle']), 'date'=>$row['actdate'], 'link'=>'', 'subtitle'=>date('D jS M Y', strtotime($row['actdate'])), 'img'=>'');
				$act_found = true;
			}
		}
		
		if ($courses_found && $act_found)
		{	usort($events, array($this, 'USortGetAllEvents'));
		}
		
		if ($limit)
		{	$events = array_slice($events, 0, $limit);
		}
		return $events;
	} // end of fn GetAllEvents
	
	public function USortGetAllEvents($a, $b)
	{	if ($a['date'] == $b['date'])
		{	return $a['title'] > $b['title'];
		} else
		{	return $a['date'] < $b['date'];
		}
	} // end of fn USortGetAllEvents
	
	public function CatsForBreadCrumbs()
	{	if ($this->details['catid'])
		{	$cat = new InstructorCategory($this->details['catid']);
			return $cat->BreadCrumbs();
		}
	} // end of fn CatsForBreadCrumbs
	
} // end of defn Instructor
?>