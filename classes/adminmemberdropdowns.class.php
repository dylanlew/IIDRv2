<?php
class AdminMemberDropdowns extends MemberDropdowns
{	var $dropdowns = array();

	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function AssignDropdown($row = array())
	{	return new AdminMemberDropdown($row);
	} // end of fn AssignDropdown
	
} // end of defn AdminMemberDropdowns
?>