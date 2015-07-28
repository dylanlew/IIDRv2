<?php
include_once('sitedef.php');

class AjaxCitySelector extends AdminAKMembersPage
{	var $userto;
	var $userfrom;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$ctry = new AdminCountry($_GET['ctry']);
		echo $ctry->CitySelector();
	} // end of fn AKMembersLoggedInConstruct
	
} // end of defn AjaxCitySelector

$page = new AjaxCitySelector();
?>