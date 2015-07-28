<?php
include_once("sitedef.php");

$emailfrom = "";
switch ($_GET["type"])
{	case "city": $city = new City((int)$_GET["id"]);
				$emailfrom = $city->EmailFrom();
				break;
	case "country": $country = new Country($_GET["id"]);
				$emailfrom = $country->EmailFrom();
				break;
	case "course": $course = new Course((int)$_GET["id"]);
				$emailfrom = $course->EmailFrom();
				break;
	default: $base = new Base();
				$emailfrom = $base->GetParameter("emailfrom");
				break;
}
echo "<!--emailfrom-->", $emailfrom;
?>