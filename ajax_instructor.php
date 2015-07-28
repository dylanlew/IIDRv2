<?php 
require_once('init.php');

class InstructorAjax extends InstructorPage
{	
	public function __construct()
	{	parent::__construct();
		if ($this->instructor->id)
		{	switch ($_GET['action'])
			{	case 'events':
					echo $this->DisplayEventsListingList($this->instructor->GetAllEvents(), $_GET['page']);
					break;
				case 'interviews':
					echo $this->DisplayInterviewListingList($this->instructor->GetInterviews(), $_GET['page']);
					break;
				case 'interviewposts':
					echo $this->DisplayInterviewAndPostListingList($this->instructor->GetInterviewsAndPosts(), $_GET['page']);
					break;
				case 'multimedia':
					echo $this->DisplayMultimediaListingList($this->instructor->GetMultiMedia(), $_GET['page']);
					break;
			}
		}
		
	} // end of fn __construct
	
} // end of defn InstructorAjax

$page = new InstructorAjax();
?>