<?php
include_once("sitedef.php");

if ($courseid = (int)$_GET["course"])
{	$course = new Course($courseid);
	$country = new Country($course->details["country"]);
	$currency = $country->GetCurrency();
	echo "<!--cursymbol-->", $currency->details["cursymbol"];
}
?>