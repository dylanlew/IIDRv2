<?php
include_once("sitedef.php");
class_exists("Form");

$base = new Base();
$city = new AdminCity((int)$_GET["city"]);
echo "<!--locations-->";
if ($loc_list = $city->LocationDropdownList())
{	$select = new FormLineSelect("Location", "location", $data["location"], "", $loc_list, true);
	$select->Output();
}

?>