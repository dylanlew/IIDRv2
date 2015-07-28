<?php
class ATICats extends Base
{	public $cats = array();

	public function __construct($usedonly = true, $liveonly = true)
	{	parent::__construct();
		$this->GetCats($usedonly, $liveonly);
	} // end of fn __construct
	
	public function GetCats($usedonly = true, $liveonly = true)
	{	$this->cats = array();
		
		$tables = array('atiqcats');
		$where = array();
		$groupby = array();
		
		if ($usedonly || $liveonly)
		{	$tables[] = 'atiqcats';
			$where[] = 'atiqcats.catid=atitocats.catid';
			$groupby[] = 'atiqcats.catid';
			
			if ($liveonly)
			{	$tables[] = 'asktheimam';
				$where[] = 'atitocats.askid=asktheimam.askid';
				$where[] = 'asktheimam.published=1';
			}
		}
		
		$sql = 'SELECT atiqcats.* FROM ' . implode(', ', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		if ($gstr = implode(', ', $groupby))
		{	$sql .= ' GROUP BY ' . $gstr;
		}
		
		$sql .= ' ORDER BY atiqcats.listorder, atiqcats.catid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->cats[$row['catid']] = $row;
			}
		}
	} // end of fn GetCats
	
} // end of class ATICats
?>