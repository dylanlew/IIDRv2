<?php
class AdminPageContents extends Base
{	var $pages = array();
	var $adminuser = false;

	function __construct($adminuser = false)
	{	parent::__construct();
		$this->adminuser = ($adminuser ? true : false);
		$this->Get();
	} //  end of fn __construct
	
	function Reset()
	{	$this->pages = array();
	} // end of fn Reset
	
	function Get()
	{	$this->Reset();
		$sql = 'SELECT * FROM pages WHERE parentid=0 ORDER BY pageorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->pages[] = new AdminPageContent($row, $this->adminuser);
			}
		}
	} // end of fn Get
	
	function PageList()
	{	
		echo '<table id="pagelist"><tr class="newlink"><th colspan="9"><a href="pageedit.php">New page</a></th></tr><tr><th>Title</th><th>Slug</th><th>Links to</th><th>Menu Order</th><th>Live</th><th>Header<br />menu?</th><th>Footer<br />menu?</th><th>Actions</th></tr>';
		foreach ($this->pages as $mainpage)
		{	echo $this->PageListLine($mainpage);
		}
		echo '</table>';
	} // end of fn PageList
	
	function PageListLine($page, $class = '', $level = 0)
	{	ob_start();
		echo '<tr class="', $class, ' ', $level ? ('child' . $level) : '', '"><td class="pagetitle"><a href="pageedit.php?id=', $page->id, '">', $this->InputSafeString($page->details['pagetitle']), '</a></td><td>', $page->details['pagename'], '</td><td>';
		if ($link = $page->Link())
		{	echo $link, ' (<a href="', $link, '" target="_blank">Preview</a>)';
		}
		echo '</td><td>', (int)$page->details['pageorder'], '</td><td>', $page->details['pagelive'] ? 'Yes' : '', '</td><td>', $page->details['headermenu'] ? 'Yes' : '', '</td><td>', $page->details['footermenu'] ? 'Yes' : '', '</td><td><a href="pageedit.php?id=', $page->id, '">edit</a>';
		if ($page->CanDelete())
		{	echo '&nbsp;|&nbsp;<a href="pagelist.php?delpage=', $page->id, '">Delete</a>';
		}
		if ($histlink = $this->DisplayHistoryLink('pages', $page->id))
		{	echo '&nbsp;|&nbsp;', $histlink;
		}
		echo '</td></tr>';
		if ($page->subpages)
		{	foreach ($page->subpages as $subpage)
			{	echo $this->PageListLine($subpage, 'child', $level + 1);
			}
		}
		return ob_get_clean();
	} // end of fn PageListLine
	
} // end of defn AdminPageContent
?>