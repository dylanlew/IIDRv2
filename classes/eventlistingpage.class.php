<?php
class EventListingPage extends CourseContentListingPage
{

	function __construct()
	{	parent::__construct('event');
		
	} // end of fn __construct
	
	public function SubmenuByDate()
	{	ob_start();
		echo '<div class="sidebar-menu"><h2>Filter by date</h2><ul>';
		$livecount = $this->GetLivePrevCount();
		if ($livecount['upcoming'])
		{	echo '<li', !$_GET ? ' class="current-subpage"' : '', '><a href="', SITE_SUB, '/events/"><span class="courseSideMenuLabel">Upcoming Events</span><span class="courseSideMenuCount">', $livecount['upcoming'], '</span><div class="clear"></div></a></li>';
		}
		if ($livecount['previous'])
		{	echo '<li', $_GET['prev'] ? ' class="current-subpage"' : '', '><a href="', SITE_SUB, '/previous-events/"><span class="courseSideMenuLabel">Previous Events</span><span class="courseSideMenuCount">', $livecount['previous'], '</span><div class="clear"></div></a></li>';
		}
		echo '</ul></div>';
		return ob_get_clean();
	} // end of fn SubmenuByDate
	
	public function SubmenuSubs(){}
	
} // end of defn EventListingPage
?>