<?php
include_once('sitedef.php');

class CourseEnrolmentPatternPage extends AdminStatsPage
{	var $city;
	var $year = 0;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function StatsConstruct()
	{	parent::StatsConstruct('resources');
		
		$this->city  = new City($_GET['city']);
		$this->year = (int)$_GET['year'];

		$this->breadcrumbs->AddCrumb("enrolbefore_cy.php?city={$this->city->id}&year={$this->year}", "Course Enrolment Pattern " . ($this->city->id ? $this->InputSafeString($this->city->details['cityname']) . ', ' : '') . ($this->year ? $this->year : ''));
	} // end of fn StatsConstruct
	
	function StatsContent()
	{	echo $this->FilterForm(), $this->CityHeader(), $this->EnrolmentTable();
	} // end of fn StatsContent
	
	function FilterForm()
	{	ob_start();
		class_exists('Form');
		$years = array();
		for ($y = date('Y'); $y >= 2006; $y--)
		{	$years[$y] = $y;
		}
		$yearfield = new FormLineSelect('', "year", $this->year, '', $years);
		$cityfield = new FormLineSelect('', "city", $this->city, '', $this->CityList());
		echo '<form class="akFilterForm" action="', $_SERVER['SCRIPT_NAME'], 
					'" method="get"><span>Year</span>';
		$yearfield->OutputField();
		if ($cities = $this->CityList())
		{	$cityfield = new FormLineSelect('', "city", $this->city->id, '', $cities, 1);
			echo "<span>city</span>";
			$cityfield->OutputField();
		}
		echo '<input type="submit" class="submit" value="Apply filter" /><div class="clear"></div></form>';
		return ob_get_clean();
	} // end of fn FilterForm
	
	function CityList()
	{	$cities = array();
		$sql = "SELECT cities.*, countries.shortname FROM cities, countries WHERE cities.country=countries.ccode ORDER BY countries.shortname, cities.cityname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$adminuser->userid || $adminuser->CanAccessCity($row["cityid"]))
				{	$cities[$row["cityid"]] = $row["shortname"] . " - " . $row["cityname"];
				}
			}
		}
		return $cities;
	} // end of fn CityList
	
	function CityHeader()
	{	ob_start();
		if ($this->city->id)
		{	$country = new Country($this->city->details["country"]);
			echo '<div><h2>', $this->InputSafeString($this->city->details['cityname']), ' - ', $this->InputSafeString($country->details['shortname']), '</h2></div>';
		}
		return ob_get_clean();
	} // end of fn CityHeader
	
	function EnrolmentTable()
	{	ob_start();
		if ($this->city->id && $this->year)
		{	$stats = new BookingsAnalysis();
			if ($courses = $stats->CourseEnrolmentDaysBeforeCity($this->city->id, $this->year))
			{	echo $this->EnrolmentTableV($courses, $stats->CourseEnrolmentDaysBeforeCityWeeksBack($courses)), '<p><img src="enrolbefore_cy_graph.php?city=', $this->city->id, '&year=', $this->year, '" /></p>';
			} else
			{	echo'<p>No courses found</p>';
			}
		}
		return ob_get_clean();
	} // end of fn EnrolmentTable
	
	function EnrolmentTableH($courses = array(), $weeks_back = 0)
	{	ob_start();
		echo '<table class="ebcTable"><tr><th>Course</th>';
		for ($w = $weeks_back; $w >= 2; $w--)
		{	echo '<th>+', $w, 'W</th>';
		}
		for ($d = 13; $d >= 0; $d--)
		{	echo '<th>', $d > 0 ? '+' : '', $d, 'D</th>';
		}
		echo '</tr>';
		foreach ($courses as $courseid=>$weeks)
		{	$course = new Course($courseid);
			echo '<tr><td>', $this->InputSafeString($course->content->details['ctitle']), '</td>';
			for ($w = $weeks_back; $w >= 2; $w--)
			{	echo '<td>', isset($weeks[$key = '+' . $w . 'W']) ? (int)$weeks[$key] : '', '</td>';
			}
			for ($d = 13; $d >= 0; $d--)
			{	echo '<td>', isset($weeks[$key = ($d > 0 ? '+' : '') . $d . 'D']) ? (int)$weeks[$key] : '', '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn EnrolmentTableH
	
	function EnrolmentTableV($courses = array(), $weeks_back = 0)
	{	ob_start();
		echo '<table class="ebcTable"><tr><th>weeks</th>';
		foreach ($courses as $courseid=>$weeks)
		{	$course = new Course($courseid);
			echo '<th>', $this->InputSafeString($course->content->details['ctitle']), '</th>';
		}
		echo '</tr>';
		for ($w = $weeks_back; $w >= 2; $w--)
		{	$key = '+' . $w . 'W';
			echo '<tr><th>W +', $w, '</th>';
			foreach ($courses as $courseid=>$weeks)
			{	echo '<td>';
				if (isset($weeks[$key]))
				{	echo (int)$weeks[$key];
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		for ($d = 13; $d >= 0; $d--)
		{	$key = ($d > 0 ? '+' : '') . $d . 'D';
			echo '<tr><th>D ', $d > 0 ? '+' : '', $d, '</th>';
			foreach ($courses as $courseid=>$weeks)
			{	echo '<td>';
				if (isset($weeks[$key]))
				{	echo (int)$weeks[$key];
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn EnrolmentTableV
	
} // end of defn CourseEnrolmentPatternPage

$page = new CourseEnrolmentPatternPage();
$page->Page();
?>