<?php
class AdminGrantApp extends GrantApp
{	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function FullName()
	{	return trim($this->details['firstname'] . ' ' . $this->details['surname']);
	} // end of fn FullName
	
	public function AdminTitle()
	{	return $this->FullName() . ', ' . date('d/m/y', strtotime($this->details['appdate']));
	} // end of fn AdminTitle
	
	public function Display()
	{	ob_start();
		echo $this->ListPersonalDetails(), $this->ListCourseDetails(), $this->ListFiles(), $this->ListFundDetails(), $this->CostsTable(), $this->ListStatementDetails(), $this->AdminEditForm();
		//$this->VarDump($this->details);
		return ob_get_clean();
	} // end of fn Display
	
	public function AdminEditForm()
	{	ob_start();
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id);
		$form->AddTextArea('Admin notes', 'adminnotes', $this->InputSafeString($this->details['adminnotes']), '', 0, 0, 10, 40);
		$form->AddSelect('Status', 'adminstatus', $this->details['adminstatus'], '', $this->status_options, 0, 0);
		$form->AddSubmitButton('', 'Save Notes', 'submit');
		echo '<p>', $this->DisplayHistoryLink('grantapps', $this->id), '</p>';
		$form->Output();
		return ob_get_clean();
	} // end of fn AdminEditForm
	
	function AdminSave($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		$adminnotes = $this->SQLSafe($data['adminnotes']);
		$fields[] = 'adminnotes="' . $adminnotes . '"';
		if ($data['adminnotes'] != $this->details['adminnotes'])
		{	$admin_actions[] = array('action'=>'Admin notes', 'actionfrom'=>$this->details['adminnotes'], 'actionto'=>$data['adminnotes']);
		}
		
		if (isset($this->status_options[$adminstatus = (int)$data['adminstatus']]))
		{	$fields[] = 'adminstatus=' . $adminstatus;
			if ($adminstatus != $this->details['adminstatus'])
			{	$admin_actions[] = array('action'=>'Admin status', 'actionfrom'=>$this->status_options[$this->details['adminstatus']], 'actionto'=>$this->status_options[$adminstatus]);
			}
		} else
		{	$fail[] = 'Admin status not found';
		}

		$sql = 'UPDATE grantapps SET ' . implode(", ", $fields) . ' WHERE gaid=' . $this->id;
		if ($this->db->Query($sql))
		{	if ($this->db->AffectedRows())
			{	$base_parameters = array('tablename'=>'grantapps', 'tableid'=>$this->id, 'area'=>'bundles');
				if ($admin_actions)
				{	foreach ($admin_actions as $admin_action)
					{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
					}
				}
				$success[] = 'Notes saved';
				$this->Get($this->id);
			}
		}
	
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AdminSave
	
	public function ListPersonalDetails()
	{	ob_start();
		echo '<h3>Personal details</h3><table class="adminDetailsHeader"><tr><td class="label">Date/time of application</td><td>', date('d-M-Y @H:i', strtotime($this->details['appdate'])), '</td></tr><tr><td class="label">Name</td><td>', $this->InputSafeString($this->FullName()), '</td></tr>';
		if ($this->details['userid'])
		{	echo '<tr><td class="label">Registered Student</td><td>';
			if (($student = new Student($this->details['userid'])) && $student->id)
			{	echo '<a href="member.php?id=', $student->id, '">', $this->InputSafeString($student->GetName()), '</a>';
			} else
			{	echo 'student id ', $this->details['userid'], ' not found';
			}
			echo '</td></tr>';
		}
		echo '<tr><td class="label">Date of birth</td><td>', date('d-M-Y', strtotime($this->details['dob'])), '</td></tr><tr><td class="label">Male or female</td><td>', $this->sex_options[$this->details['sex']], '</td></tr><tr><td class="label">Address</td><td>', nl2br($this->InputSafeString($this->details['address'])), '</td></tr><tr><td class="label">Postcode</td><td>', $this->InputSafeString($this->details['postcode']), '</td></tr><tr><td class="label">Phone</td><td>', $this->InputSafeString($this->details['phone']), '</td></tr><tr><td class="label">Email</td><td>', $this->details['email'], '</td></tr><tr><td class="label">How long in UK</td><td>';
		if ($this->details['howlonguk_m'] || $this->details['howlonguk_y'])
		{	echo $years = $this->details['howlonguk_y'] + floor($this->details['howlonguk_m'] / 12), ' years, ', $months = $this->details['howlonguk_m'] % 12, ' months';
			if ($this->details['howlonguk_m'] != $months)
			{	echo ' [input was: ', $this->details['howlonguk_y'], ' years, ', $this->details['howlonguk_m'], ' months]';
			}
		} else
		{	echo '---';
		}
		echo '</td></tr><tr><td class="label">Country of birth</td><td>', $this->InputSafeString($this->details['birthctry']), '</td></tr><tr><td class="label">Nationality</td><td>', $this->InputSafeString($this->details['nationality']), '</td></tr><tr><td class="label">Languages</td><td>', nl2br($this->InputSafeString($this->details['languages'])), '</td></tr><tr><td class="label">Religion</td><td>', $this->InputSafeString($this->details['religion']), '</td></tr></table>';
		return ob_get_clean();
	} // end of fn ListPersonalDetails
	
	public function ListCourseDetails()
	{	ob_start();
		echo '<h3>Course details</h3><table class="adminDetailsHeader"><tr><td class="label">College, etc.</td><td>', nl2br($this->InputSafeString($this->details['collegename'])), '&nbsp;</td></tr><tr><td class="label">Course title</td><td>', $this->InputSafeString($this->details['coursetitle']), '</td></tr><tr><td class="label">Qualification acquired at end of studies</td><td>', $this->InputSafeString($this->details['qualification']), '</td></tr><tr><td class="label">Level of Study</td><td>', $this->InputSafeString($this->details['studylevel']), '</td></tr><tr><td class="label">Start date</td><td>', (int)$this->details['startdate'] ? date('d-M-Y', strtotime($this->details['startdate'])) : 'not given', '</td></tr><tr><td class="label">Finish date</td><td>', (int)$this->details['enddate'] ? date('d-M-Y', strtotime($this->details['enddate'])) : 'not given', '</td></tr><tr><td class="label">Number of weeks attendance</td><td>', (int)$this->details['weeks'] ? (int)$this->details['weeks'] : '', '</td></tr><tr><td class="label">Applicant\'s highest qualification</td><td>', $this->InputSafeString($this->details['highqual']), '</td></tr><tr><td class="label">Type of study</td><td>', $this->InputSafeString($this->studytypes[$this->details['studytype']]), '</td></tr><tr><td class="label">Part time, time per week</td><td>';
		if ($this->details['perweek_h'] || $this->details['perweek_d'])
		{	echo (int)$this->details['perweek_h'], ' hours, ', (int)$this->details['perweek_d'], ' days';
		} else
		{	echo '---';
		}
		echo '</td></tr></table>';
		return ob_get_clean();
	} // end of fn ListCourseDetails
	
	public function ListFundDetails()
	{	ob_start();
		echo '<h3>Fund details</h3><table class="adminDetailsHeader"><tr><td class="label">Fund type</td><td>', nl2br($this->InputSafeString($this->fund_options[$this->details['fundtype']])), '&nbsp;</td></tr></table>';
		return ob_get_clean();
	} // end of fn ListFundDetails
	
	public function ListStatementDetails()
	{	ob_start();
		echo '<h3>Course details</h3><table class="adminDetailsHeader"><tr><td class="label">Reference 1</td><td>', nl2br($this->InputSafeString($this->details['reference1'])), '</td></tr><tr><td class="label">Reference 2</td><td>', nl2br($this->InputSafeString($this->details['reference2'])), '</td></tr><tr><td class="label">Statement</td><td>', nl2br($this->InputSafeString($this->details['statement'])), '</td></tr></table>';
		return ob_get_clean();
	} // end of fn ListStatementDetails
	
	public function ListFiles()
	{	ob_start();
		if ($files = $this->GetFiles())
		{	echo '<h3>Files uploaded</h3><table class="adminDetailsHeader">';
			foreach ($files as $file)
			{	echo '<tr><td class="label">', $this->InputSafeString($file['filetype']), '</td><td><a href="grantapp_file.php?id=', $this->id, '&file=', $file['gafid'], '">download</a></td></tr>';
			}
			echo '</table>';
		}
		return ob_get_clean();
	} // end of fn ListFiles
	
	public function CostsTable()
	{	ob_start();
		echo '<h3>Amount of grant requested</h3><table class="grantsCosts"><tr><th>Item</th><th>Total cost (&pound;)</th><th>Grant requested (&pound;)</th></tr>',
			'<tr><td class="gctLabel">Tuition fees</td><td class="gctAmount">', number_format($this->details['tuition_c'], 2), '</td><td class="gctAmount">', number_format($this->details['tuition_g'], 2), '</td></tr>',
			'<tr><td class="gctLabel">Registration fees</td><td class="gctAmount">', number_format($this->details['reg_c'], 2), '</td><td class="gctAmount">', number_format($this->details['reg_g'], 2), '</td></tr>',
			'<tr><td class="gctLabel">Enrolment fees</td><td class="gctAmount">', number_format($this->details['enrol_c'], 2), '</td><td class="gctAmount">', number_format($this->details['enrol_g'], 2), '</td></tr>',
			'<tr><td class="gctLabel">Examination fees</td><td class="gctAmount">', number_format($this->details['exam_c'], 2), '</td><td class="gctAmount">', number_format($this->details['exam_g'], 2), '</td></tr>',
		'<tr><td class="gctLabel">Other fees</td><td class="gctAmount">', number_format($this->details['other_c'], 2), '</td><td class="gctAmount">', number_format($this->details['other_g'], 2), '</td></tr>';
		if ($this->details['other_list'])
		{	echo '<tr><td colspan="3" class="gctDetails"><h4>Details of other fees</h4>', nl2br($this->InputSafeString($this->details['other_list'])), '</td></tr>';
		}
		echo '<tr><td class="gctLabel">Required books</td><td class="gctAmount">', number_format($this->details['books_c'], 2), '</td><td class="gctAmount">', number_format($this->details['books_g'], 2), '</td></tr>';
		if ($this->details['other_list'])
		{	echo '<tr><td colspan="3" class="gctDetails"><h4>Details of required books</h4>', nl2br($this->InputSafeString($this->details['books_list'])), '</td></tr>';
		}
		echo '<tr><td class="gctLabel">Equipment</td><td class="gctAmount">', number_format($this->details['equip_c'], 2), '</td><td class="gctAmount">', number_format($this->details['equip_g'], 2), '</td></tr>';
		if ($this->details['equip_list'])
		{	echo '<tr><td colspan="3" class="gctDetails"><h4>Details of items of equipment</h4>', nl2br($this->InputSafeString($this->details['equip_list'])), '</td></tr>';
		}
		echo '<tr><td class="gctLabel">Other course costs</td><td class="gctAmount">', number_format($this->details['courseother_c'], 2), '</td><td class="gctAmount">', number_format($this->details['courseother_g'], 2), '</td></tr>';
		if ($this->details['courseother_list'])
		{	echo '<tr><td colspan="3" class="gctDetails"><h4>Details of other course costs</h4>', nl2br($this->InputSafeString($this->details['courseother_list'])), '</td></tr>';
		}
		echo '<tr><td class="gctLabel">Travel costs per week';
		if ($this->details['travel_weeks'])
		{	echo '<br />(for ', (int)$this->details['travel_weeks'], ' weeks)';
		}
		echo '</td><td class="gctAmount">', number_format($this->details['travel_c'], 2);
		if ($this->details['travel_weeks'])
		{	echo '<br />(', number_format($this->details['travel_c'] * $this->details['travel_weeks'], 2), ')';
		}
		echo '</td><td class="gctAmount">', number_format($this->details['travel_g'], 2);
		if ($this->details['travel_weeks'])
		{	echo '<br />(', number_format($this->details['travel_g'] * $this->details['travel_weeks'], 2), ')';
		}
		echo '</td></tr><tr><td colspan="3" class="gctDetails"><h4>Details of travel costs</h4>';
		if ($this->details['travel_from'] || $this->details['travel_to'])
		{	echo 'From ', $this->details['travel_from'] ? $this->InputSafeString($this->details['travel_from']) : '*****', ' to ', $this->details['travel_to'] ? $this->InputSafeString($this->details['travel_to']) : '*****', '. ';
		}
		if ($this->details['travel_distance'])
		{	echo 'Distance ', $this->InputSafeString($this->details['travel_distance']), '. ';
		}
		if ($this->details['travel_method'])
		{	echo 'Method of transport ', $this->InputSafeString($this->details['travel_method']), '. ';
		}
		echo '</td></tr><tr class="gctTotals"><td class="gctLabel">Totals</td><td class="gctAmount">', number_format($this->TotalCosts(), 2), '</td><td class="gctAmount">', number_format($this->TotalGrant(), 2), '</td></tr></table>';
		return ob_get_clean();
	} // end of fn CostsTable
	
} // end of class AdminGrantApp
?>