<?php

class Search extends Base
{	public $term;
	private $searchable = array();
	private $results = array();
	
	public function __construct()
	{	parent::__construct();
	} // fn __construct
	
	public function In($title, $object)
	{
		if ($object instanceof Searchable)
		{
			$this->searchable[$title] = $object;	
		}
	} // fn In
	
	public function Process($term = '')
	{
		if ($term != '')
		{
			$this->term = trim($term);
			
			foreach ($this->searchable as $title => $search)
			{
				if ($results = $search->Search($this->term))
				{	$this->results = array_merge($this->results, $results);
				}
			}
			usort($this->results, array($this, 'Sort'));
			
			return $this->results;
		}
	} // fn Process
	
	private function Sort($a, $b)
	{	return $a->details['matchscore'] > $b->details['matchscore'];
	} // fn Sort
	
	
} // end of defn Search

?>