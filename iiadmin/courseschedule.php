<?php
include_once("sitedef.php");

class ContentCoursesPage extends AdminCoursesPage
{	var $startdate = "";
	var $enddate = "";
	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$this->js[] = "adminbookings.js";
	
		if (($d = (int)$_GET["dstart"]) && ($m = (int)$_GET["mstart"]) && ($y = (int)$_GET["ystart"]))
		{	$this->startdate = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
		} else
		{	$this->startdate = $this->datefn->SQLDate(strtotime("-1 week"));
		}
	
		if (($d = (int)$_GET["dend"]) && ($m = (int)$_GET["mend"]) && ($y = (int)$_GET["yend"]))
		{	$this->enddate = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
		} else
		{	$this->enddate = $this->datefn->SQLDate(strtotime("+6 months"));
		}

		$this->coursecontent  = new AdminCourseContent($_GET["course"]);
		$this->breadcrumbs->AddCrumb("coursescontent.php", "Courses");
		$this->breadcrumbs->AddCrumb("coursecontentedit.php?id={$this->coursecontent->id}", $this->InputSafeString($this->coursecontent->details["ctitle"]));
		$this->breadcrumbs->AddCrumb("courseschedule.php?course={$this->coursecontent->id}", "Schedule");
		
	} // end of fn CoursesLoggedInConstruct
	
	function CoursesBody()
	{	echo $this->coursecontent->Header(), $this->FilterForm(), $this->coursecontent->ListCourses($this->startdate, $this->enddate, $_GET["ctry"], $this->user);
	} // end of fn CoursesBody
	
	function FilterForm()
	{	ob_start();
		class_exists("Form");
		$years = array();
		for ($y = 2006; $y <= date('Y') + 3; $y++)
		{	$years[$y] = $y;
		}
		$startfield = new FormLineDate("", "start", $this->startdate, $years, 0, 0, 1);
		$endfield = new FormLineDate("", "end", $this->enddate, $years, 0, 0, 1);
		echo "<form class='akFilterForm' action='", $_SERVER["SCRIPT_NAME"], 
					"' method='get'>\n<input type='hidden' name='course' value='", $this->coursecontent->id, "' /><span>From</span>";
		$startfield->OutputField();
		echo "<span>to</span>";
		$endfield->OutputField();
		if ($countries = $this->GetCourseCountries())
		{	$ctryselect = new FormLineSelect("", "ctry", $_GET["ctry"], "", $countries, true);
			echo "<span>country</span>";
			$ctryselect->OutputField();
		}
		echo "<input type='submit' class='submit' value='Apply filter' /><div class='clear'></div>\n</form>\n";
		return ob_get_clean();
	} // end of fn FilterForm
	
	function GetCourseCountries()
	{	$countries = array();
		$sql = "SELECT countries.ccode, countries.shortname, cities.cityid FROM countries, cities, courses WHERE countries.ccode=cities.country AND cities.cityid=courses.city AND courses.coursecontent={$this->coursecontent->id} ORDER BY countries.shortname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($this->user->CanAccessCity($row["cityid"]))
				{	$countries[$row["ccode"]] = $row["shortname"];
				}
			}
		}
		
		return $countries;
	} // end of fn GetCourseCountries
	
} // end of defn ContentCoursesPage

$page = new ContentCoursesPage();
$page->Page();
?>