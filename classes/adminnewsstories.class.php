<?php
class AdminNewsStories extends NewsStories
{	var $stories = array();

	function __construct($liveonly = 0, $startdate = "", $enddate = "", $limit = 0)
	{	parent::__construct();
	} // fn __construct
	
	function AssignStory($newsid)
	{	return new AdminNewsStory($newsid);
	} // end of fn AssignStory
	
} // end if class defn AdminNewsStories
?>