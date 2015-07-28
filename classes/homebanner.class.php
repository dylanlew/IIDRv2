<?php
class HomeBanner extends Base
{	var $items = array();

	function __construct($liveonly = true)
	{	parent::__construct();
		$this->Get($liveonly);
	} // end of fn __construct
	
	function Reset()
	{	$this->items = array();
	} // end of fn Reset
	
	function Get($liveonly = true, $lang = '')
	{	$this->Reset();
		
		$where = array();
		$tables = array('homebanner');
		
		if ($liveonly)
		{	$where[] = 'live=1';
			if (!$lang)
			{	$lang = $this->lang;
			}
		}
	
		$sql = 'SELECT homebanner.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY hborder';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->items[] = $this->AssignItem($row);
			}
		}
		
		if ($liveonly && !$this->items && ($lang != $this->def_lang))
		{	$this->Get(true, $this->def_lang);
		}
		
	} // end of fn Get
	
	function AssignItem($row = array())
	{	return new HomeBannerItem($row);
	} // end of fn AssignItem
	
} // end of defn HomeBanner
?>