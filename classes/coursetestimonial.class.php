<?php
class CourseTestimonial extends Base
{	
	public $details = array();
	public $id = 0;
	
	function __construct($id = 0)
	{	parent::__construct();
		$this->Get($id);
	} // fn __construct
	
	function Reset()
	{	$this->details = array();
		$this->id = 0;
	} // end of fn Reset
	
	function Get($id = 0)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id["tid"];
		} else
		{	if ($result = $this->db->Query("SELECT * FROM coursetestimonials WHERE tid=" . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
	} // end of fn Get	
	
	function GetAuthor()
	{
		if($result = $this->db->Query("SELECT * FROM students WHERE userid = ".(int)$this->details["sid"]))
		{
			if($row = $this->db->FetchArray($result))
			{
				return new Student($row);
			}
		}	
	}
	
	
} // end of defn CourseTestimonial

?>