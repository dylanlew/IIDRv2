<?php
include_once('sitedef.php');

class BookingsPerDayGraph extends Graph
{	var $course;
	var $maxlines = 5;
	protected $dataWidth = 600;
	protected $dataHeight = 300;
	protected $lineColourNums = array(0=>0xFF0000, 1=>0x00FF00, 2=>0x0000FF, 3=>0x990099, 4=>0x009999, 5=>0x666600, 6=>0x666666, 6=>0x006666, 8=>0x660000, 9=>0xFF6666, 10=>0x6666FF);
	//protected $perXTagfactor = 40; // number of values marked on x-axis
	
	function __construct()
	{	
		$this->course = new AdminCourse($_GET['id']);
		$this->titleString = 'Bookings per day ' . $this->course->InputSafeString($this->course->content['ctitle']) . ', ' . date('d/m/y', strtotime($this->course->details['starttime'])) . ' - ' . date('d/m/y', strtotime($this->course->details['endtime']));
		parent::__construct();
		
	} //end of fn __construct
	
	protected function GetData()
	{	$this->legend = array('bookings per day');
		
		foreach ($this->course->GetBookingsPerDay() as $date=>$count)
		{	$this->data[] = array('n'=>date('j/m', strtotime($date)), 'y'=>array($count));
		}
	} // end of GetData
	
} // end of fn BookingsPerDayGraph


$graph = new BookingsPerDayGraph();
$graph->OutPut();
?>