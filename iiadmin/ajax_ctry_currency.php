<?php
include_once("sitedef.php");

if ($city_ctry = explode("_", $_GET["city_ctry"]))
{	$country = new Country($city_ctry[count($city_ctry) - 1]);
	$currency = $country->GetCurrency();
	echo "<!--cursymbol-->", $currency->details["cursymbol"];
}
?>