<?php
include_once('sitedef.php');

class EnrolBeforeGraph extends Graph
{	var $city;
	var $year = "";
	var $maxlines = 5;
	protected $dataWidth = 800;
	protected $dataHeight = 400;
	protected $lineColourNums = array(0=>0xFF0000, 1=>0x00FF00, 2=>0x0000FF, 3=>0x990099, 4=>0x009999, 5=>0x666600, 6=>0x666666, 6=>0x006666, 8=>0x660000, 9=>0xFF6666, 10=>0x6666FF);
	protected $perXTagfactor = 40; // number of values marked on x-axis
	
	function __construct($cityid = 0, $year = 0)
	{	
		$this->city = new City($cityid);
		$this->year = (int)$year;
		$country = new Country($this->city->details["country"]);
		$this->titleString = 'Course Enrolment Pattern ' . $this->city->details['cityname'] . ' - ' . $country->details['shortname'] . " in " . $this->year;
		parent::__construct();
		
	} //end of fn __construct
	
	protected function GetData()
	{	$stats = new BookingsAnalysis();
		if ($courses = $stats->CourseEnrolmentDaysBeforeCity($this->city->id, $this->year))
		{	$this->legend = array();
			foreach ($courses as $courseid=>$weeks)
			{	$course = new Course($courseid);
				$this->legend[] = $course->content->details['ctitle'];
			}
			$weeks_back = $stats->CourseEnrolmentDaysBeforeCityWeeksBack($courses);
			for ($w = $weeks_back; $w >= 2; $w--)
			{	$key = '+' . $w . 'W';
				$y = array();
				foreach ($courses as $courseid=>$weeks)
				{	$y[] = (int)$weeks[$key];
				}
				$this->data[] = array("n"=>strtolower($key), "y"=>$y);
			}
			for ($d = 13; $d >= 0; $d--)
			{	$key = ($d > 0 ? '+' : '') . $d . 'D';
				$y = array();
				foreach ($courses as $courseid=>$weeks)
				{	$y[] = (int)$weeks[$key];
				}
				$this->data[] = array("n"=>strtolower($key), "y"=>$y);
			}
			
		}
	} // end of GetData
	
} // end of fn EnrolBeforeGraph


$graph = new EnrolBeforeGraph($_GET["city"], $_GET["year"]);
$graph->OutPut();
?>