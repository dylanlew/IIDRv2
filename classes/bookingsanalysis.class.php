<?php
class BookingsAnalysis extends Base
{	
	public function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	public function CourseEnrolmentPerDay($courseid = 0)
	{	$rawdays = array();
		$days = array();
		$where = array('course=' . (int)$courseid);
		$sql = 'SELECT LEFT(enroldate, 10) AS enrolday, COUNT(bookid) AS bcount FROM bookings WHERE ' . implode(' AND ', $where) . ' GROUP BY enrolday ORDER BY enrolday';
		$totalbookings = 0;
		$lastday = '';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$rawdays[$lastday = $row['enrolday']] = array('bookings'=>$row['bcount'], 'sofar'=>$totalbookings += $row['bcount']);
			}
		}
		
		// now fill in blanks
		$start = '';
		foreach ($rawdays as $day=>$stats)
		{	$days[$day] = $stats;
			if ($day < $lastday)
			{	$next = $day;
				while (!$rawdays[$next = $this->datefn->SQLDate(strtotime($next . ' +1 day'))])
				{	$days[$next] = array('bookings'=>0, 'sofar'=>$stats['sofar']);
				}
			}
		}
		
		return $days;
	} // end of fn CourseEnrolmentPerDay
	
	public function CourseEnrolmentDaysBefore($courseid = 0)
	{	$daysbefore = array();
		
		if ($days = $this->CourseEnrolmentPerDay($courseid))
		{	if ($startdate = substr($this->CourseDetail($courseid, 'starttime'), 0, 10))
			{	$startstamp = strtotime($startdate);
				foreach (array_reverse($days) as $date=>$stats)
				{	$day = round(($startstamp - strtotime($date)) / $this->datefn->secInDay);
					if (!$firstdone++)
					{	while ($startdate > $date)
						{	$catchupday = round(($startstamp - strtotime($startdate)) / $this->datefn->secInDay);
							$this->CEDBAddToArray($daysbefore, round(($startstamp - strtotime($startdate)) / $this->datefn->secInDay), $stats['sofar']);
							$startdate = $this->datefn->SQLDate(strtotime($startdate . ' -1 day'));
						}
					}
					$this->CEDBAddToArray($daysbefore, round(($startstamp - strtotime($date)) / $this->datefn->secInDay), $stats['sofar']);
				}
			}
		}
		return $daysbefore;
	} // end of fn CourseEnrolmentDaysBefore
	
	function CEDBAddToArray(&$list, $day, $sofar)
	{	if ($day >= 0)
		{	if ($day < 14)
			{	$key = ($day > 0 ? '+' : '') . $day . 'D';
				$list[$key] = $sofar;
			} else
			{	// only do full weeks back
				if (!($day % 7))
				{	$key = '+' . ($day / 7) . 'W';
					$list[$key] = $sofar;
				}
			}
		}
	} // end of fn CEDBAddToArray
	
	protected function CourseDetail($courseid = 0, $field = '')
	{	$sql = "SELECT $field FROM courses WHERE cid=$courseid";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row[$field];
			}
		}else echo "<p>", $this->db->Error(), "</p>\n";
		
	} // end of fn CourseDetail
	
	protected function CityYearCourses($cityid = 0, $year = 0)
	{	$courses = array();
		if (($cityid = (int)$cityid) && ($year = (int)$year))
		{	$sql = "SELECT cid FROM courses WHERE city=$cityid AND endtime<='$year-12-31 23:59:59' AND starttime>='$year-01-01 00:00:00' ORDER BY starttime";
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$courses[] = $row['cid'];
				}
			}else echo "<p>$sql:", $this->db->Error(), "</p>\n";
			
		}
		return $courses;
	} // end of fn CityYearCourses
	
	function CourseEnrolmentDaysBeforeCity($city = 0, $year = 0)
	{	$results = array();
		if ($courses = $this->CityYearCourses($city, $year))
		{	foreach ($courses as $courseid)
			{	$results[$courseid] = array_reverse($this->CourseEnrolmentDaysBefore($courseid), true);
			}
		}
		return $results;
	} // end of fn CourseEnrolmentDaysBeforeCity
	
	function CourseEnrolmentDaysBeforeCityWeeksBack($courses = array())
	{	$weeks_back = 3;
		foreach ($courses as $weeks)
		{	foreach ($weeks as $key=>$value)
			{	if (substr($key, -1) == 'W')
				{	if ((int)$key > $weeks_back)
					{	$weeks_back = (int)$key;
					}
				}
				break;
			}
		}
		if ($weeks_back > 20)
		{	$weeks_back = 20;
		}
		return $weeks_back;
	} // end of fn CourseEnrolmentDaysBeforeCityWeeksBack
	
} // end of defn BookingsAnalysis
?>