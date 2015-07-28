<?php
class AdminFrontEndMenuItem extends FrontEndMenuItem
{	
	function __construct($id = 0)
	{	parent::__construct($id);
	} // fn __construct
	
	function AssignMenuItem($row = array())
	{	return new AdminFrontEndMenuItem($row);
	} // end of fn AssignMenuItem
	
	function CanDelete()
	{	return count($this->items) == 0;
	} // end of fn CanDelete
	
} // end of defn AdminFrontEndMenuItem
?>