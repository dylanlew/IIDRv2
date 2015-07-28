<?php
class PageContents extends Base
{	var $pages = array();
	var $liveonly = true;

	function __construct($liveonly = true)
	{	parent::__construct();
		$this->liveonly = ($liveonly ? true : false);
		$this->Get();
	} //  end of fn __construct
	
	function Reset()
	{	$this->pages = array();
	} // end of fn Reset
	
	function Get($lang = '')
	{	$this->Reset();
		
		$where = array();
		$tables = array('pages');
		
		if ($this->liveonly)
		{	$where[] = 'pages.pagelive=1';
		
		
		}
		$where[] = 'pages.parentid=0';
		$sql = 'SELECT pages.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY pages.pageorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->pages[] = new PageContent($row, $this->liveonly);
			}
		}
	} // end of fn Get

	function xxxGetHeaderMenu($exclude_sub = false, $lang = '')
	{	$menu = array();
		
		$where = array('pages.headermenu=1', 'pages.pagelive=1');
		$tables = array('pages');

		if (!$lang)
		{	$lang = $this->lang;
		}
		
		if ($exclude_sub)
		{	$where[] = 'pages.parentid=0';
		}
		$sql = 'SELECT pages.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY pages.pageorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$page = new PageContent($row, $this->liveonly);
				$menu[$page->details['pagename']] = array('link'=>$page->Link(), 'text'=>$this->InputSafeString($page->details['pagetitle']), 'class'=>$this->InputSafeString($page->details['menuclass']));
			}
		}
		
		if (!$menu && ($lang != $this->def_lang))
		{	return $this->GetHeaderMenu($exclude_sub, $this->def_lang);
		}
		
		return $menu;
	} // end of fn GetHeaderMenu

	function GetHeaderMenu()
	{	
		$menu = array();
		
		$sql = "SELECT * FROM pages WHERE parentid = 0 AND headermenu = 1 AND pagelive = 1 ORDER BY pageorder ASC";
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$p = new PageContent($row, 1);
				$menu[$p->details["pagename"]] = array('link' => $p->Link(), 'text' => $this->InputSafeString($p->details['pagetitle']), 'class' => $this->InputSafeString($p->details['menuclass']));
			}
		}
		
		return $menu;
		
	} // end of fn GetHeaderMenu
	function GetFooterMenu()
	{	
		$menu = array();
		
		$sql = "SELECT * FROM pages WHERE parentid = 0 AND footermenu = 1 AND pagelive = 1 ORDER BY pageorder ASC";
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$p = new PageContent($row, 1);
				$menu[$p->details["pagename"]] = array('link' => $p->Link(), 'text' => $this->InputSafeString($p->details['pagetitle']), 'class' => $this->InputSafeString($p->details['menuclass']));
			}
		}
		
		return $menu;
		
	} // end of fn GetHeaderMenu

} // end of defn AdminPageContent
?>