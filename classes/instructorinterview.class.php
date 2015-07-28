<?php
class InstructorInterview extends BlankItem
{	
	function __construct($id = 0)
	{	parent::__construct($id, 'instinterviews', 'ivid');
		$this->Get($id);
	} // fn __construct
	
	public function Link()
	{	$inst = new Instructor($this->details['inid']);
		return SITE_URL . 'people/interviews/' . $this->id . '/' . $inst->details['instslug'] . '/';
	} // end of fn Link
	
} // end of defn InstructorInterview
?>