<?php
class AdminVenuePage extends AdminPage
{	
	public function __construct()
	{	parent::__construct('COURSES');
	} //  end of fn __construct

	public function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('courses'))
		{	$this->VenuesLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	protected function VenuesLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('venues.php', 'Venues');
	} // end of fn VenuesLoggedInConstruct
	
	protected function VenueBody()
	{	
	} // end of fn VenueBody
	
	public function AdminBodyMain()
	{	if ($this->user->CanUserAccess('courses'))
		{	$this->VenueBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminCoursesPage
?>