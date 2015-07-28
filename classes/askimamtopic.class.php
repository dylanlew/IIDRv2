<?php
class AskImamTopic extends BlankItem
{	public $questions = array();
	public $instructors = array();
	public $cats = array();
	var $imagelocation = '';
	var $imagedir = '';
	var $imagesizes = array('default'=>array(510, 339), 'thumbnail'=>array(170, 113));
	
	public function __construct($id = null)
	{	parent::__construct($id, 'askimamtopics', 'askid');
		$this->imagelocation = SITE_URL . 'img/askexpert/';
		$this->imagedir = CITDOC_ROOT . '/img/askexpert/';
	} // fn __construct
	
	public function ResetExtra()
	{	$this->questions = array();
		$this->instructors = array();
		$this->cats = array();
	} // end of fn ResetExtra
	
	public function GetExtra()
	{	$this->GetQuestions();
		$this->GetInstructors();
		$this->GetCategories();
	} // end of fn GetExtra
	
	public function GetCategories()
	{	$this->cats = array();
		
		if ($id = (int)$this->id)
		{	$where = array('askimamtocats.askid=' . $id, 'askimamtocats.catid=coursecategories.cid');
			$sql = 'SELECT coursecategories.* FROM askimamtocats, coursecategories WHERE ' . implode(' AND ', $where) . ' ORDER BY coursecategories.ctitle DESC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->cats[$row['cid']] = $row;
				}
			}
		}
	} // end of fn GetCategories
	
	public function GetQuestions($liveonly = true)
	{	$this->questions = array();
		$sql = 'SELECT * FROM askimamquestions WHERE askid=' . (int)$this->id;
		if ($liveonly)
		{	$sql .= ' AND live=1';
		}
		$sql .= ' ORDER BY listorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->questions[$row['qid']] = $row;
			}
		}
	} // end of fn GetQuestions
	
	public function GetInstructors($liveonly = true)
	{	$this->instructors = array();
		$sql = 'SELECT  instructors.*, askimaminstructors.listorder AS cilistorder FROM askimaminstructors,instructors WHERE askimaminstructors.inid=instructors.inid AND askimaminstructors.askid=' . (int)$this->id;
		if ($liveonly)
		{	$sql .= ' AND live=1';
		}
		$sql .= ' ORDER BY askimaminstructors.listorder, instname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->instructors[$row['inid']] = $row;
			}
		}
	} // end of fn GetInstructors
	
	public function CategoryListDisplay($sep = ', ')
	{	$catnames = array();
		foreach ($this->cats as $cat)
		{	$catnames[] = $this->InputSafeString($cat['ctitle']);
		}
		return implode($sep, $catnames);
	} // end of fn CategoryListDisplay
	
	public function InstructorsListDisplay($sep = ', ')
	{	$instnames = array();
		foreach ($this->instructors as $inst_row)
		{	$inst = new Instructor($inst_row);
			$instnames[] = $this->InputSafeString($inst->GetFullName());
		}
		return implode($sep, $instnames);
	} // end of fn InstructorsListDisplay
	
	public function DisplayInstructorText($links = true)
	{	$instlist = array();
		if ($this->details['anstext'])
		{	$instlist[] = $this->InputSafeString($this->details['anstext']);
		}
		foreach ($this->instructors as $inst_row)
		{	$instrustor = new Instructor($inst_row);
			if ($links)
			{	$instlist[] = '<a href="' . $instrustor->Link() . '">' . $this->InputSafeString($instrustor->GetFullName()) . '</a>';
			} else
			{	$instlist[] = $this->InputSafeString($instrustor->GetFullName());
			}
		}
		return implode(', ', $instlist);
	} // end of fn DisplayInstructorText
	
	public function Link()
	{	return SITE_SUB . '/asktheexpert/topic/' . $this->id . '/' . $this->details['slug'] . '/';
	} // end of fn Link
	
	function HasImage($size = '')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	}
	
	function GetImageFile($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}
	
	function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}
	
} // end of class AskImamTopic
?>