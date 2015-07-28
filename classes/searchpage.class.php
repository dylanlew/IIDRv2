<?php 
class SearchPage extends BasePage
{	protected $perpage = 10;
	protected $term;
	
	function __construct()
	{	parent::__construct('search');		
		
		$this->term = $_GET['term'];
	
	} // end of fn __construct
	
	function MainBodyContent()
	{	if ($this->term)
		{	echo '<div id="sidebar" class="col">', $this->GetSidebarCourses(), $this->GetSidebarQuote(), '</div><div class="col3-wrapper-with-sidebar"><h1>Search Results for <em>"', $this->InputSafeString($this->term), '"</em></h1><div id="srContainer">', $this->SearchResults(), '</div></div><div class="clear"></div>';
		} else
		{	//echo "{search form}";	
		}
	} // end of fn MainBodyContent
	
	protected function SearchResults()
	{	ob_start();
		$search = new Search;
		$search->In('Courses', new Course);
		$search->In('News', new NewsPost);
		$search->In('Multimedia', new Multimedia);
		$search->In('Opinions', new OpinionPost);
		$search->In('Instructors', new Instructor);
		$search->In('Store', new StoreProduct);
		$search->In('Pages', new PageContent);
		$search->In('FAQ', new FAQCat);
		
		if ($results = $search->Process($this->term))
		{	
			if ($_GET['page'] > 1)
			{	$start = ($_GET['page'] - 1) * $this->perpage;
			} else
			{	$start = 0;
			}
			$end = $start + $this->perpage;
			
			
			echo '<ul class="searchresults">';
			foreach ($results as $result)
			{	if (++$count > $start)
				{	if ($count > $end)
					{	break;
					}
				
					echo '<li>', $result->SearchResultOutput(), '</li>';	
				}
			}
			echo '</ul>';

			if (count($results) > $this->perpage)
			{	$pag = new AjaxPagination($_GET['page'], count($results), $this->perpage, 'srContainer', 'ajax_search.php', $_GET);
				echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
			}
		} else
		{
			// no results	
			echo '<h3>No results found.</h3>';
		}	
		return ob_get_clean();
	} // end of fn SearchResults
	
} // end of defn SearchPage
?>