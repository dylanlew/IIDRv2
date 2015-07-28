<?php
include_once("sitedef.php");

class CountryPaymentOptionPage extends CMSPage
{	var $country;
	var $pmtoption;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	$this->country  = new AdminCountry($_GET["ctry"]);

		$this->pmtoption = new AdminCountryPaymentOption($_GET["pmt"], $_GET["ctry"]);
		$this->js[] = "tiny_mce/jquery.tinymce.js";
		$this->js[] = "course_tiny_mce.js";
		$this->css[] = "ctrypmts.css";
		
		if (isset($_POST["optname"]))
		{	$saved = $this->pmtoption->Save($_POST);
			$this->successmessage = $saved["successmessage"];
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage && !$this->failmessage)
			{	header("location: ctryedit.php?ctry=" . $this->country->code);
				exit;
			}
		}

		$this->breadcrumbs->AddCrumb("countries.php", "Countries");
		$this->breadcrumbs->AddCrumb("ctryedit.php?ctry={$this->country->code}", 
						$this->InputSafeString($this->country->details["shortname"]));
		$this->breadcrumbs->AddCrumb("ctrypmtoption.php?ctry={$this->country->code}&pmt={$this->pmtoption->id}", 
						"Payment Option: " . $this->InputSafeString($this->pmtoption->details["optname"]));
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	echo $this->pmtoption->InputForm();
		$this->DefaultPaymentOption();
	} // end of fn CMSBodyMain
	
	function DefaultPaymentOption()
	{	$defoption = new AdminPaymentOption($this->pmtoption->id);
		//$this->VarDump($defoption->details);
		echo "<div id='defpmtoption'><p>Default text: <strong>", $this->InputSafeString($defoption->details["optname"]), "</strong></p>\n<p>Instructions to user:</p>\n<div id='pmt_inst'>", stripslashes($defoption->details["opttext"]), "</div>";
		if ($defoption->details["defoption"])
		{	echo "<p><strong>This is the default default option</strong></p>";
		}
		echo "</div>\n";
	} // end of fn DefaultPaymentOption
	
} // end of defn CountryPaymentOptionPage

$page = new CountryPaymentOptionPage();
$page->Page();
?>