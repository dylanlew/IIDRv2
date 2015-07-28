<?php
class Parameter extends Base
{	var $details = array();
	var $name = '';
	
	function __construct($parname = array())
	{	parent:: __construct();
		$this->Get($parname);
	} // end of fn CompanyDetail
	
	function Get($parname = '')
	{	$this->details = array();
		$this->name = '';
		if (is_array($parname))
		{	$this->details = $parname;
			$this->name = $parname['parname'];
		} else
		{	if ($result = $this->db->Query('SELECT * FROM parameters WHERE parname="' . $this->SQLSafe($parname) . '"'))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
	} // end of fn Get
	
	function AddFormLine(&$form)
	{	$elements = array();
		switch ($this->details['fieldtype'])
		{	case 'VARCHAR': $elements[] = array('type'=>'TEXT', 'name'=>$this->name, 
								'maxlength'=>(int)$this->details['fieldlength'], 
								'value'=>$this->InputSafeString($this->details['fieldvalue']),
								'css'=>'long');
							break;
			case 'INT': $elements[] = array('type'=>'TEXT', 'name'=>$this->name, 'value'=>(int)$this->details['fieldvalue'], 
								'maxlength'=>(int)$this->details['fieldlength'], 'css'=>'num short');
							break;
			case 'PRICE': $elements[] = array('type'=>'TEXT', 'name'=>$this->name, 
								'value'=>round($this->details['fieldvalue'], 2), 'maxlength'=>(int)$this->details['fieldlength'], 'css'=>'num short');
							break;
			case 'FLOAT': $elements[] = array('type'=>'TEXT', 'name'=>$this->name, 
								'value'=>round($this->details['fieldvalue'], 6), 'maxlength'=>10, 'css'=>'num');
							break;
			case 'BOOLEAN': $elements[] = array('type'=>'CHECKBOX', 'name'=>$this->name, 'value'=>1, 
								'checked'=>$this->details['fieldvalue'] ? true : false);
							break;
			case 'TEXT': $elements[] = array('type'=>'TEXTAREA', 'name'=>$this->name, 
								'maxlength'=>(int)$this->details['fieldlength'], 
								'value'=>$this->InputSafeString($this->details['fieldvalue']));
							break;
		}
		$form->AddMultiInput($this->details['pardesc'], $elements);
	} // end of fn AddFormLine
	
	function Save($value = '')
	{	switch ($this->fieldtype)
		{	case 'INT': $value = (int)$value;
							break;
			case 'PRICE': $value = round($value, 2);
							break;
			case 'FLOAT': $value = (float)$value;
							break;
			case 'BOOLEAN': $value = ($value ? 1 : 0);
							break;
			default: $value = $this->SQLSafe($value);
		}
		$sql = 'UPDATE parameters SET fieldvalue="' . $value . '" WHERE parname="' . $this->name . '"';
		if ($result = $this->db->Query($sql))
		{	if ($this->db->AffectedRows())
			{	return true;
			}
		}
		return false;
	} // end of fn Save
	
} // end of defn Parameter
?>