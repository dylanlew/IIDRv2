<?php
class AjaxPagination
{
	protected $perPage = 0; // number of listings per page
	protected $total = 0; // number of listings
	protected $numOfPages = 0; // number of pages
	protected $page = 0; // number of current page
	protected $ajaxPage = ''; // link to add pagination link to
	protected $pagename = 'page'; // name of get variable carrying page number
	protected $containerid = ''; // target for results
	protected $parameters = ''; //list of parameters
	
	function __construct($page = 0, $total = 0, $perPage = 20, $containerid = '', $ajaxPage = '', $parameters = array(), $pagename = 'page')
	{	$this->ajaxPage = $ajaxPage;
		$this->containerid = $containerid;
		$this->SetParameters($parameters);
		$this->total = intval($total);
		if ($pagename)
		{	$this->pagename = $pagename;
		}
		$this->perPage = intval($perPage);
		if ($this->page = intval($page))
		{	$this->page--;
		}
		
		if ($this->perPage)
		{	$this->numOfPages = ceil($this->total/$this->perPage);
		}
		if($page > $this->numOfPages)
		{	$this->page = $this->numOfPages;	
		}
	} // end of constructor
	
	public function SetParameters($parameters = array())
	{	$params = array();
		foreach ($parameters as $key=>$value)
		{	if ($key != $this->pagename)
			{	$params[] = $key . '=' . $value;
			}
		}
		$this->parameters = implode('&', $params);
	} // end of fn SetParameters
	
	function Display($separator = '')
	{	if ($this->perPage)
		{	$pagination = array();
			$text = '';
			//print_r($this->pages);
			$pstart = 0;
			do
			{	ob_start();
				if ($this->page != $pstart)
				{	echo '<a onclick="AjaxPagination(\'', $this->containerid, '\',\'', $this->ajaxPage, '\',\'', $this->parameters, '\',', $pstart + 1, ');">';
				} else
				{	echo '<span>';
				}
				echo $pstart + 1, ($this->page != $pstart) ? '</a>' : '</span>';
				$pagination[] = ob_get_clean();
				
			} while ((++$pstart * $this->perPage) < $this->total);
			
			ob_start();
			if ($this->page)
			{	echo '<a onclick="AjaxPagination(\'', $this->containerid, '\',\'', $this->ajaxPage, '\',\'', $this->parameters, '\',', $this->page, ');"><img src="', SITE_URL, 'img/template/pag_prev.png" alt="Previous" title="Previous" /></a>';
			}
			echo implode($separator, $pagination);
			if ($this->page < $this->numOfPages - 1)
			{	echo '<a onclick="AjaxPagination(\'', $this->containerid, '\',\'', $this->ajaxPage, '\',\'', $this->parameters, '\',', $this->page + 2, ');"><img src="', SITE_URL, 'img/template/pag_next.png" alt="Next" title="Next" /></a>';
			}
			return ob_get_clean();
		}
	} // end of fn Display
	
} // end of class AjaxPagination
?>