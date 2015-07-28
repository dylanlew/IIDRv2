<?php
class PostCategory extends BlankItem
{	var $subcats = array();

	function __construct($id = 0)
	{	parent::__construct($id, 'postcategories', 'cid');
		$this->Get($id); 
	} // fn __construct
	
	public function ResetExtra()
	{	$this->subcats = array();
	} // end of fn ResetExtra
	
	public function GetExtra()
	{	$this->subcats = array();
		if ($this->id)
		{	$sql = 'SELECT * FROM postcategories WHERE parentcat=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->subcats[$row['cid']] = $row;
				}
			}
		}
	} // end of fn GetExtra
	
	public function GetPosts($posttype = '', $liveonly = true)
	{	$posts = array();
		
		if ($id = (int)$this->id)
		{	$where = array('catid=' . $id);
		
			if ($liveonly)
			{	$where[] = 'live=1';
			}
			if ($posttype)
			{	$where[] = 'ptype="' . $this->SQLSafe($posttype) . '"';
			}
			
			$sql = 'SELECT * FROM posts WHERE ' . implode(' AND ', $where) . ' ORDER BY pdate DESC';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$posts[$row['pid']] = $row;
				}
			}
		}
		
		return $posts;
	} // end of fn GetPosts
	
	public function CascadedName($cat = false, $sep = ' &raquo; ')
	{	if (!$cat)
		{	$cat = $this;
		}
		$name = $this->InputSafeString($cat->details['ctitle']);
		if ($parent = $cat->GetParent())
		{	return $this->CascadedName($parent) . $sep . $name;
		} else
		{	return $name;
		}
	} // end of fn CascadedName
	
	public function GetParent()
	{	if ($this->details['parentcat'] && ($parent = new PostCategory($this->details['parentcat'])) && $parent->id)
		{	return $parent;
		}
	} // end of fn GetParent
	
	public function Link($posttype = 'post')
	{	return SITE_URL . $posttype . '-category/' . $this->id . '/' . $this->details['catslug'] . '/';
	} // end of fn Link
	
} // end of defn PostCategory
?>