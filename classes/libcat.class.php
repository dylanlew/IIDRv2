<?php
class LibCat extends BlankItem
{	var $parent = array();
	var $subcats = array();
	
	function __construct($id = '')
	{	parent::__construct($id, 'libcats', 'lcid');
	} // fn __construct
	
	public function GetExtra()
	{	$this->GetParent();
		$this->GetSubCats();
	} // end of fn GetExtra
	
	public function GetParent()
	{	$this->parent = array();
		if ($parentid = (int)$this->details['parentid'])
		{	$sql = 'SELECT * FROM libcats WHERE lcid=' . $parentid;
			if ($result = $this->db->Query($sql))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->parent = $row;
				}
			}
		}
	} // end of fn GetParent
	
	public function GetSubCats()
	{	$this->subcats = array();
		if ($this->id)
		{	$sql = 'SELECT * FROM libcats WHERE parentid=' . $this->id . ' ORDER BY lcorder';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->subcats[$row['lcid']] = $row;
				}
			}
		}
	} // end of fn GetSubCats
	
	public function ResetExtra()
	{	$this->parent = array();
		$this->subcats = array();
	} // end of fn ResetExtra

	public function GetMultiMedia($getall = false, $liveonly = true, $libonly = true)
	{	$mm = array();
		$where = array('multimedia.mmid=multimediacats.mmid', 'multimediacats.lcid=' . $this->id);
		if ($liveonly)
		{	$where[] = 'multimedia.live=1';
		}
		if ($libonly)
		{	$where[] = 'multimedia.inlib=1';
		}
		$sql = 'SELECT multimedia.* FROM multimedia, multimediacats WHERE ' . implode(' AND ', $where) . ' ORDER BY multimedia.mmorder ASC, multimedia.posted DESC';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$mm[$row['mmid']])
				{	$mm[$row['mmid']] = $row;
				}
			}
		}
		
		if ($getall && $this->subcats)
		{	foreach ($this->subcats as $subcat_row)
			{	$subcat = new LibCat($subcat_row);
				if ($subcat_books = $subcat->GetMultiMedia($getall, $liveonly, $libonly))
				{	foreach ($subcat_books as $sb_book)
					{	if (!$mm[$sb_book['mmid']])
						{	$mm[$sb_book['mmid']] = $sb_book;
						}
					}
				}
			}
		}
		
		return $mm;
	} // end of fn GetMultiMedia
	
	public function Link()
	{	return SITE_URL . 'multimedia_cat/' . $this->id . '/' . $this->details['lcslug'] . '/';
	} // end of fn Link
	
} // end of defn LibCat
?>