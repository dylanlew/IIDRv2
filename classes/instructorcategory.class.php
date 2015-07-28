<?php
class InstructorCategory extends BlankItem
{	var $subcats = array();

	function __construct($id = 0)
	{	parent::__construct($id, 'instructorcats', 'icid');
	} // fn __construct
	
	public function ResetExtra()
	{	$this->subcats = array();
	} // end of fn ResetExtra
	
	public function GetExtra()
	{	$this->subcats = array();
		if ($this->id)
		{	$sql = 'SELECT * FROM instructorcats WHERE parentcat=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->subcats[$row['icid']] = $row;
				}
			}
		}
	} // end of fn GetExtra
	
	public function GetPeople($liveonly = true)
	{	$instuctors = array();
		
		if ($id = (int)$this->id)
		{	$where = array('catid=' . $id, 'coursetocats.courseid=courses.cid');
		
			if ($liveonly)
			{	$where[] = 'live=1';
			}
			
			$sql = 'SELECT * AS toplist FROM instuctors WHERE ' . implode(' AND ', $where) . ' ORDER BY showfront DESC, instname ASC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$instuctors[$row['icid']] = $row;
				}
			}
		}
		
		return $instuctors;
	} // end of fn GetPeople
	
	public function CascadedName($cat = false, $sep = ' &raquo; ')
	{	if (!$cat)
		{	$cat = $this;
		}
		$name = $this->InputSafeString($cat->details['catname']);
		if ($parent = $cat->GetParent())
		{	return $this->CascadedName($parent) . $sep . $name;
		} else
		{	return $name;
		}
	} // end of fn CascadedName
	
	public function BreadCrumbs($cat = false)
	{	if (!$cat)
		{	$cat = $this;
		}
		$names = array($this->id=>$this->InputSafeString($cat->details['catname']));
		if ($parent = $cat->GetParent())
		{	return array_merge($this->BreadCrumbs($parent), $names);
		} else
		{	return $names;
		}
	} // end of fn BreadCrumbs
	
	public function GetParent()
	{	if ($this->details['parentcat'] && ($parent = new InstructorCategory($this->details['parentcat'])) && $parent->id)
		{	return $parent;
		}
	} // end of fn GetParent
	
} // end of defn InstructorCategory
?>