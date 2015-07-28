<?php
class Instructors extends Base
{
	public $instructors = array();
	
	public function __construct()
	{	parent::__construct();
		$this->Get();
	} // fn __construct
	
	public function Get()
	{	$this->Reset();
		$sql = 'SELECT * FROM instructors WHERE instructors.live=1 ORDER BY showfront DESC, instname ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->instructors[] = $this->AssignInstructor($row);
			}
		} 
	} // fn Get
	
	public function Reset()
	{	$this->instructors = array();
	} // fn Reset
	
	public function AssignInstructor($row = array())
	{	return new Instructor($row);
	} // fn AssignInstructor
	
} // end of class Instructors

?>