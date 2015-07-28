<?php
class CourseDate extends BlankItem
{	public $photos = array();
	
	public function __construct($id = null)
	{	parent::__construct($id, 'coursedates', 'cdid');
	} // fn __construct
	
} // end of class defn CourseDate
?>