<?php
include_once("sitedef.php");

class LocationEditPage extends CountriesPage
{	var $city;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CountriesLoggedInConstruct()
	{	$this->location  = new AdminLocation($_GET["id"]);
		$this->js[] = "http://maps.google.com/maps/api/js?sensor=false";
		$this->js[] = "akgooglemap.js";

		if ($this->location->id)
		{	$city = new City($this->location->details["city"]);
		} else
		{	$city = new City($_GET["city"]);
		}
		$ctry  = new Country($city->details["country"]);
		
		if (!$this->user->CanAccessCity($city->id))
		{	header("location: countries.php");
			exit;
		}
		
		if (isset($_POST["loctitle"]))
		{	$saved = $this->location->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: cityedit.php?id=" . $this->location->details["city"]);
				exit;
			}
		}
		
		if ($this->location->id && $_GET["delete"] && $_GET["confirm"])
		{	$city = $this->location->details["city"];
			if ($this->location->Delete())
			{	header("location: cityedit.php?id=" . $city);
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}

		if ($ctry && $ctry->code)
		{	$this->breadcrumbs->AddCrumb("ctryedit.php?ctry={$ctry->code}", $this->InputSafeString($ctry->details["shortname"]));
		}
		if ($city && $city->id)
		{	$this->breadcrumbs->AddCrumb("cityedit.php?id={$city->id}", $this->InputSafeString($city->details["cityname"]));
		}
		
		if ($this->location->id)
		{	$this->breadcrumbs->AddCrumb("locationedit.php?id={$this->location->id}", 
						$this->InputSafeString($this->location->details["loctitle"]));
		} else
		{	$this->breadcrumbs->AddCrumb("cityedit.php?city={$_GET["city"]}", "Creating new location");
		}
	} // end of fn CountriesLoggedInConstruct
	
	function CountriesBodyMain()
	{	$this->location->InputForm();
	} // end of fn CountriesBodyMain
	
} // end of defn LocationEditPage

$page = new LocationEditPage();
$page->Page();
?>