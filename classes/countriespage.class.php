<?php
class CountriesPage extends AdminPage
{	
	function __construct()
	{	parent::__construct("COUNTRIES");
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("countries"))
		{	$this->breadcrumbs->AddCrumb("countries.php", "Countries");
			$this->CountriesLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function CountriesLoggedInConstruct()
	{	
	} // end of fn CountriesLoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("countries"))
		{	$this->CountriesBodyMain();
		}
	} // end of fn AdminBodyMain
	
	function CountriesBodyMain()
	{	
	} // end of fn CountriesBodyMain
	
} // end of defn CountriesPage
?>