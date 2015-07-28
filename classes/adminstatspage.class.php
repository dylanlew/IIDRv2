<?php
class AdminStatsPage extends AdminPage
{
	function __construct()
	{	parent::__construct("STATS");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		$this->css[] = 'adminstats.css';
		$this->breadcrumbs->AddCrumb('', 'Stats');
		if ($this->user->CanUserAccess('statistics'))
		{	$this->StatsConstruct();
		}
	} // end of fn LoggedInConstruct

	function StatsConstruct()
	{	
	} // end of fn StatsConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('statistics'))
		{	$this->StatsContent();
		}
	} // end of fn AdminBodyMain
	
	function StatsContent()
	{	
	} // end of fn StatsContent
	
} // end of defn AdminStatsPage
?>