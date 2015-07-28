<?php
class Parameters extends Base
{	var $details = array();

	function __construct()
	{	parent:: __construct();
		$this->GetDetails();
	} // end of fn __construct
	
	function GetDetails()
	{	$this->details = array();
		$sql = 'SELECT * FROM parameters ORDER BY pardesc';
		if ($result = $this->db->Query($sql))
		{	while($row = $this->db->FetchArray($result))
			{	$this->details[$row['parname']] = new Parameter($row);
			}
		}
	} // end of fn GetDetails
	
	function ParameterGroups()
	{	$groups = array();
		foreach ($this->details as $detail)
		{	$groups[$detail->details['pargroup']]++;
		}
		ksort($groups);
		return $groups;
	} // end of fn ParameterGroups
	
	function InputForm($group = '')
	{	
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?group=' . $group, 'peform');
		if ($group)
		{	$form->AddFormHeader($group);
		}
		foreach ($this->details as $detail)
		{	if ($detail->details['pargroup'] == $group)
			{	$detail->AddFormLine($form);
				$details = true;
			}
		}
		$form->AddHiddenInput('dummy', '1');
		if ($details) $form->AddSubmitButton('', 'Save Changes', 'submit');
		$form->Output();
	} // end of fn InputForm
	
} // end of defn Parameters
?>