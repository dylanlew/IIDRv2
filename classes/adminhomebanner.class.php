<?php
class AdminHomeBanner extends HomeBanner
{	var $items = array();

	function __construct()
	{	parent::__construct($liveonly = false);
	} // end of fn __construct
	
	function AssignItem($row = array())
	{	return new AdminHomeBannerItem($row);
	} // end of fn AssignItem
	
} // end of defn AdminHomeBanner
?>