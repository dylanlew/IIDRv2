<?php
class AdminFrontEndMenu extends FrontEndMenu
{	$items = array();
	
	function __construct($id = 0)
	{	parent::__construct($id);
	} // fn __construct
	
	function AssignMenuItem($row = array())
	{	return new AdminFrontEndMenuItem($row);
	} // end of fn AssignMenuItem
	
} // end of defn AdminFrontEndMenu
?>