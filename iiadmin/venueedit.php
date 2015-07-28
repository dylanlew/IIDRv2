<?php
include_once('sitedef.php');

class VenueEditPage extends AdminVenuePage
{	private $venue;
	
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	protected function VenuesLoggedInConstruct()
	{	parent::VenuesLoggedInConstruct();
		
		$this->venue = new AdminVenue($_GET['id']);
		
		if (isset($_POST['vname']))
		{	$saved = $this->venue->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->venue->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->venue->Delete())
			{	header('location: venues.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('venueedit.php?id=' . (int)$this->venue->id, $this->venue->id ? $this->InputSafeString($this->venue->details['vname']) : 'new venue');
	} // end of fn VenuesLoggedInConstruct
	
	protected function VenueBody()
	{	echo $this->venue->InputForm();
	} // end of fn VenueBody
	
} // end of defn VenueEditPage

$page = new VenueEditPage();
$page->Page();
?>