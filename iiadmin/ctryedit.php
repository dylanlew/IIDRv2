<?php
include_once('sitedef.php');

class CountryEditPage extends CountriesPage
{	var $country;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CountriesLoggedInConstruct()
	{	$this->country  = new AdminCountry($_GET['ctry']);
		
		if (isset($_POST['shortname']))
		{	$saved = $this->country->Save();
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
			if ($this->successmessage && !$this->failmessage)
			{	if ($_POST['redirect'])
				{	$redirect = urldecode($_POST['redirect']);
				} else
				{	$redirect = 'countries.php';
				}
				header('location: ' . $redirect . '#tr' . $this->country->code);
				exit;
			}
		}
		
		if ($this->country->code && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->country->Delete())
			{	header('location: countries.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}

		if ($this->country->code)
		{	$this->breadcrumbs->AddCrumb('ctryedit.php?ctry=' . $this->country->code, $this->InputSafeString($this->country->details['shortname']));
		} else
		{	$this->breadcrumbs->AddCrumb('ctryedit.php', 'Creating new country');
		}
	} // end of fn CountriesLoggedInConstruct
	
	function CountriesBodyMain()
	{	$this->country->InputForm();
	} // end of fn CountriesBodyMain
	
} // end of defn CountryEditPage

$page = new CountryEditPage();
$page->Page();
?>