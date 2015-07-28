<?php
class FooterMenu extends PageContents
{
	function __construct()
	{	parent::__construct(true);
	} //  end of fn __construct
	
	function Get($lang = '')
	{	$this->Reset();
		
		$where = array('pages.parentid=0', 'pages.footermenu=1', 'pages.pagelive=1');
		$tables = array('pages');

		if (!$lang)
		{	$lang = $this->lang;
		}
		$tables[] = 'pages_speak';
		$where[] = 'pages.pageid=pages_speak.pageid';
		$where[] = 'pages_speak.lang="' . $lang . '"';

		$sql = 'SELECT pages.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY pages.pageorder';

		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->pages[] = new FooterPageContent($row, true);
			}
		}
	} // end of fn Get
	
} // end of defn FooterMenu
?>