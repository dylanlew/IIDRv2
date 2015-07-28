<?php
class InstructorAct extends BlankItem
{	public $photos = array();
	
	public function __construct($id = null)
	{	parent::__construct($id, 'instactivities', 'iaid');
	} // fn __construct
	
} // end of class defn InstructorAct
?>