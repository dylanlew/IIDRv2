<?php
class Pagination
{
	protected $stripVars = array('page'); // variables to strip from link
	protected $perPage = 0; // number of listings per page
	protected $total = 0; // number of listings
	protected $numOfPages; // number of pages
	protected $page = 0; // number of current page
	protected $pages = array(); //list of pages
	protected $baseLink = ''; // link to add pagination link to
	protected $pagename = 'page'; // name of get variable carrying page number
	protected $hashlink = ''; // place in page link, to go after page number
	
	function __construct($page = 0, $total = 0, $perPage = 20, $baseLink = '', $stripVars = array(), $pagename = 'page', $hashlink = '')
	{
		$this->total = intval($total);
		if ($pagename)
		{	$this->pagename = $pagename;
		}
		if ($hashlink)
		{	$this->hashlink = $hashlink;
		}
		$this->perPage = intval($perPage);
		if ($this->page = intval($page))
		{	$this->page--;
		}
		
		$this->numOfPages = ceil($this->total/$this->perPage);
		if($page > $this->numOfPages)
		{	$this->page = $this->numOfPages;	
		}

		$this->AddToStripVars($stripVars);
		$this->SetBaseLink($baseLink);
		
		$this->setPages();
		
	//	var_dump($this);
	} // end of constructor
	
	function AddToStripVars($stripVars = array())
	{	if (is_array($stripVars))
		{	foreach ($stripVars as $var)
			{	$this->stripVars[] = $var;
			}
		}
	} // end of fn AddToStripVars
	
	function SetBaseLink($baseLink = '')
	{	
		if (!$baseLink)
		{	$baseLink = $_SERVER['REQUEST_URI'];
		}
		
		// now strip out any existing page
		if ($querypos = strpos($baseLink, '?'))
		{	$this->baseLink = substr($baseLink, 0, $querypos);
			$query = array();
			foreach (explode('&', html_entity_decode(substr($baseLink, $querypos + 1), ENT_NOQUOTES)) as $q)
			{	$qbits = explode('=', $q);
				if (!in_array($qbits[0], $this->stripVars))
				{	$query[] = $q;
				}
			}
			if ($qstring = implode('&', $query))
			{	$this->baseLink .= '?' . $qstring;
			}
		} else
		{	$this->baseLink = $baseLink;
		}
		
	} // end of fn SetBaseLink
	
	function setPages()
	{	
		$this->pages = array();
		$pstart = 0;
		do
		{	$this->pages[] = new PaginationPage($pstart, $this->baseLink, $pstart == $this->page, $this->pagename, 
											$this->hashlink);
		} while ((++$pstart * $this->perPage) < $this->total);
		
	} // end of fn setPages
	
	function GetLimits()
	{
		$lower = $this->page * $this->perPage;
		$upper = $lower + $this->perPage;
		
		return array($lower, $upper);
	} // end of fn  GetLimits
	
	function Display($separator = '|')
	{	$pagination = array();
		//print_r($this->pages);
		foreach ($this->pages as $page)
		{	$pagination[] = $page->Display();
		}
		ob_start();
		if ($this->page)
		{	$link = $this->baseLink;
			if (strstr($link, '?'))
			{	$link .= '&';
			} else
			{	$link .= '?';
			}
			$link .= $this->pagename . '=';
			$link .= $this->page;
			if ($this->hashlink)
			{	$link .= '#' . $this->hashlink;
			}
			echo '<a href="', $link, '"><img src="', SITE_URL, 'img/template/pag_prev.png" alt="Previous" title="Previous" /></a>';
		}
		echo implode($separator, $pagination);
		if ($this->page < $this->numOfPages - 1)
		{	$link = $this->baseLink;
			if (strstr($link, '?'))
			{	$link .= '&';
			} else
			{	$link .= '?';
			}
			$link .= $this->pagename . '=';
			$link .= $this->page + 2;
			if ($this->hashlink)
			{	$link .= '#' . $this->hashlink;
			}
			echo '<a href="', $link, '"><img src="', SITE_URL, 'img/template/pag_next.png" alt="Next" title="Next" /></a>';
		}
		return ob_get_clean();
	} // end of fn Display
	
	function PageList()
	{	$pages = array();
		foreach ($this->pages as $page)
		{	$pages[] = $page->Display();
		}
		return $pages;
	} // end of fn PageList
	
	function PageLinks()
	{	$pages = array();
		foreach ($this->pages as $page)
		{	$pages[$page->PageNum()] = $page->Link();
		}
		return $pages;
	} // end of fn PageLinks
	
	function DisplayList($list = array())
	{	$newlist = array();
		
		if (is_array($list))
		{	$newlist = array_slice($list, $this->page * $this->perPage, $this->perPage);
		}
		
		return $newlist;
	} // end of fn DisplayList
	
} // end of class Pagination

class PaginationPage
{	private $pagenum = 0;
	private $baseLink = '';
	private $current = false;
	private $pagename = 'page';
	private $hashlink = '';

	function __construct($pagenum = 0, $baseLink = '', $current = false, $pagename = '', $hashlink = '')
	{	$this->pagenum = (int)$pagenum;
		if ($pagename)
		{	$this->pagename = $pagename;
		}
		if ($hashlink)
		{	$this->hashlink = $hashlink;
		}
		$this->current = $current;
		$this->SetBaseLink($baseLink);
	} // end of constructor
	
	function SetBaseLink($baseLink = '')
	{	if (!$this->current)
		{	$this->baseLink = $baseLink;
			if (strstr($this->baseLink, '?'))
			{	$this->baseLink .= '&';
			} else
			{	$this->baseLink .= '?';
			}
			$this->baseLink .= $this->pagename . '=';
			$this->baseLink .= $this->pagenum + 1;
			if ($this->hashlink)
			{	$this->baseLink .= '#' . $this->hashlink;
			}
		}
	} // end of fn SetBaseLink
	
	function Display()
	{	ob_start();
		//echo '<span', $this->baseLink ? ' class="paginlink"' : '', '>';
		if ($this->baseLink)
		{	echo '<a href="', $this->baseLink, '">';
		} else
		{	echo '<span>';
		}
		echo $this->PageNum();
		if ($this->baseLink)
		{	echo '</a>';
		} else
		{	echo '</span>';
		}
		return ob_get_clean();
	} // end of fn Display
	
	function PageNum()
	{	return $this->pagenum + 1;
	} // end of fn PageNum
	
	function Link()
	{	return $this->baseLink;
	} // end of fn GetLink
	
} // end of class  PaginationPage

?>