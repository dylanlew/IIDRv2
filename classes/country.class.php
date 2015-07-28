<?php
class Country extends Base
{	var $details = array();
	var $code = "";
	
	function __construct($ctrycode = 0)
	{	parent::__construct();
		$this->Get($ctrycode);
	} // fn __construct
	
	function Reset()
	{	$this->details = array();
		$this->code = '';
	} // end of fn Reset
	
	function Get($ctrycode = 0)
	{	$this->Reset();
		if (is_array($ctrycode))
		{	$this->details = $ctrycode;
			$this->code = $ctrycode['ccode'];
		} else
		{	if ($result = $this->db->Query('SELECT * FROM countries WHERE ccode="' . $this->SQLSafe($ctrycode) . '"'))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->details = $row;
					$this->code = $row['ccode'];
				}
			}
		}
		
	} // end of fn Get
	
} // end of defn Country
?>