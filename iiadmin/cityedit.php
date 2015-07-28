<?php
include_once("sitedef.php");

class CityEditPage extends CountriesPage
{	var $city;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CountriesLoggedInConstruct()
	{	$this->city  = new AdminCity($_GET["id"]);
		
		if ($this->city->id)
		{	if (!$this->user->CanAccessCity($this->city->id))
			{	header("location: countries.php");
				exit;
			}
		}
		
		$this->js[] = "ajax_fromemail.js";
		
		if (isset($_POST["cityname"]))
		{	$saved = $this->city->Save();
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: ctryedit.php?ctry=" . $this->city->details["country"]);
				exit;
			}
		}
		
		if ($this->city->id && $_GET["delete"] && $_GET["confirm"])
		{	$ctry = $this->city->details["country"];
			if ($this->city->Delete())
			{	header("location: ctryedit.php?ctry=" . $ctry);
				exit;
			} else
			{	$this->failmessage = "Delete failed";
			}
		}

		if ($this->city->id)
		{	$ctry  = new Country($this->city->details["country"]);
		} else
		{	$ctry  = new Country($_GET["ctry"]);
		}
		if ($ctry && $ctry->code)
		{	$this->breadcrumbs->AddCrumb("ctryedit.php?ctry={$ctry->code}", $this->InputSafeString($ctry->details["shortname"]));
		}
		
		if ($this->city->id)
		{	$this->breadcrumbs->AddCrumb("cityedit.php?id={$this->city->id}", 
						$this->InputSafeString($this->city->details["cityname"]));
		} else
		{	$this->breadcrumbs->AddCrumb("cityedit.php?ctry={$_GET["ctry"]}", "Creating new city");
		}
	} // end of fn CountriesLoggedInConstruct
	
	function CountriesBodyMain()
	{	$this->city->InputForm();
		$this->city->LocationList();
	} // end of fn CountriesBodyMain
	
} // end of defn CityEditPage

$page = new CityEditPage();
$page->Page();
?>