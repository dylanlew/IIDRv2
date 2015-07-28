<?php
class FAQCat extends BlankItem implements Searchable
{		
	public function __construct($id = 0)
	{	parent::__construct($id, 'faqcats', 'catid');
	} // end of fn __construct
	
	public function GetFAQ($liveonly = true)
	{	$faq = array();
		$sql = 'SELECT faq.* FROM faq, faqtocats WHERE faq.faqid=faqtocats.faqid AND faqtocats.catid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND faq.live=1';
		}
		$sql .= ' ORDER BY faq.listorder, faq.created';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$faq[$row['faqid']] = $row;
			}
		}
		return $faq;
	} // end of fn GetFAQ

	public function Link()
	{	return SITE_SUB . '/faq/category/' . $this->id . '/' . $this->details['catslug'] . '/';
	} // end of fn Link
	
	/** Search Functions ****************/
	public function Search($term)
	{
		$match = ' MATCH(faq.question, faq.answer) AGAINST("' . $this->SQLSafe($term) . '") ';
		$sql = 'SELECT faqtocats.catid, ' . $match . ' as matchscore FROM faq, faqtocats WHERE faq.faqid=faqtocats.faqid AND ' . $match . ' AND faq.live=1 ORDER BY matchscore DESC';
		
		$results = array();
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($results[$row['catid']])
				{	if ($row['matchscore'] > $results[$row['catid']]->details['matchscore'])
					{	$results[$row['catid']]->details['matchscore'] = $row['matchscore'];
					}
				} else
				{	$results[$row['catid']] = new FAQCat($row['catid']);
					$results[$row['catid']]->details['matchscore'] = $row['matchscore'];
				}
			}
		}
		
		return $results;
	} // end of fn Search
	
	public function SearchResultOutput()
	{
		echo '<h4><span>FAQ</span><a href="', $link = $this->Link(), '">', $this->InputSafeString($this->details['catname']), '</a></h4><p><a href="', $link, '">read more ...</a></p>';
	} // end of fn SearchResultOutput
	
} // end of class FAQCat
?>