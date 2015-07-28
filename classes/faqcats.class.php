<?php
class FAQCats extends Base
{	public $cats = array();

	public function __construct($usedonly = true, $liveonly = true)
	{	parent::__construct();
		$this->GetCats($usedonly, $liveonly);
	} // end of fn __construct
	
	public function GetCats($usedonly = true, $liveonly = true)
	{	$this->cats = array();
		
		$tables = array('faqcats');
		$where = array();
		$groupby = array();
		
		if ($usedonly || $liveonly)
		{	$tables[] = 'faqtocats';
			$where[] = 'faqcats.catid=faqtocats.catid';
			$groupby[] = 'faqcats.catid';
			
			if ($liveonly)
			{	$tables[] = 'faq';
				$where[] = 'faqtocats.faqid=faq.faqid';
				$where[] = 'faq.live=1';
			}
		}
		
		$sql = 'SELECT faqcats.* FROM ' . implode(', ', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		if ($gstr = implode(', ', $groupby))
		{	$sql .= ' GROUP BY ' . $gstr;
		}
		
		$sql .= ' ORDER BY faqcats.listorder, faqcats.catid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->cats[$row['catid']] = $row;
			}
		}
	} // end of fn GetCats
	
} // end of class FAQCats
?>