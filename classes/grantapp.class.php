<?php
class GrantApp extends BlankItem
{	protected $studytypes = array('fulltime'=>'Full-time', 'parttime'=>'Part-time', 'distance'=>'Distance learning');
	protected $persontitles = array('Mr'=>'Mr', 'Mrs'=>'Mrs', 'Miss'=>'Miss', 'Ms'=>'Ms', 'Dr'=>'Dr');
	protected $filelocation = '';
	protected $sex_options = array('M'=>'Male', 'F'=>'Female');
	protected $fund_options = array('Azhar'=>'Azhar Fund', 'Ibda'=>'Ibda Fund', 'Ibtikar'=>'Ibtikar Fund', 'Tanmiya'=>'Tanmiya Fund');
	private $captcha = false;
	protected $status_options = array(0=>'Pending', 1=>'Accepted', 2=>'Declined', 3=>'Download');

	public function __construct($id = null)
	{	parent::__construct($id, 'grantapps', 'gaid');
		$this->filelocation = CITDOC_ROOT . '/grantapps/';
	} // end of fn __construct
	
	public function FileDir()
	{	return $this->filelocation . $this->id . '/';
	} // end of fn FileDir
	
	public function GetFiles()
	{	$files = array();
		$sql = 'SELECT * FROM grantappsfiles WHERE gaid=' . $this->id . ' ORDER BY gafid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$files[$row['gafid']] = $row;
			}
		}
		return $files;
	} // end of fn GetFiles
	
	public function FileLocation($file = array())
	{	return $this->FileDir() . $file['filename'];
	} // end of fn FileLocation
	
	public function DefaultsFromStudent(Student $user)
	{	$defaults = array();
		if ($user->id)
		{	$defaults['firstname'] = $user->details['firstname'];
			$defaults['surname'] = $user->details['surname'];
			$defaults['title'] = $user->details['title'];
			$defaults['dob'] = $user->details['dob'];
			$defaults['sex'] = $user->details['morf'];
			$address = array();
			if ($user->details['address1'])
			{	$address[] = $user->details['address1'];
			}
			if ($user->details['address2'])
			{	$address[] = $user->details['address2'];
			}
			if ($user->details['address3'])
			{	$address[] = $user->details['address3'];
			}
			if ($user->details['city'])
			{	$address[] = $user->details['city'];
			}
			$defaults['address'] = implode("\n", $address);
			$defaults['postcode'] = $user->details['postcode'];
			if (!$defaults['phone'] = $user->details['phone'])
			{	$defaults['phone2'] = $user->details['phone2'];
			}
			$defaults['emailadd'] = $user->details['username'];
			if ($user->details['country'] && ($birthctry = $this->GetCountry($user->details['country'])))
			{	$defaults['birthctry'] = $birthctry;
			}
		}
		return $defaults;
	} // end of fn DefaultsFromStudent
	
	public function SaveDataFromPost($post = array())
	{	$data = array();
		foreach ($post as $key=>$value)
		{	$data[str_replace('ga_', '', $key)] = $value;
		}
		return $data;
	} // end of fn SaveDataFromPost
	
	public function SaveFromUser($rawdata = array(), $files = array(), $user = false)
	{	$fields = array();
		$fail = array();
		$success = array();
	//	$this->VarDump($files);
	//	return;
		$data = $this->SaveDataFromPost($rawdata);
		
		if (!$data['submit_authorise'])
		{	$fail[] = 'You must check the authorisation to submit a grant application';
		}
		
		if ($rawdata['email'] || $rawdata['name'])
		{	$fail[] = 'Spam detected';
		}
		
		$fields[] = 'appdate="' . $this->datefn->SQLDateTime() . '"';
		if (is_a($user, 'Student') && $user->id)
		{	$fields[] = 'userid=' . $user->id;
		}
		
		$firstname = $this->SQLSafe($data['firstname']);
		$surname = $this->SQLSafe($data['surname']);
		if ($firstname && $surname)
		{	$fields[] = 'firstname="' . $firstname . '"';
			$fields[] = 'surname="' . $surname . '"';
		} else
		{	$fail[] = 'You must give your full name';
		}
		
		if ($postcode = $this->SQLSafe($data['postcode']))
		{	$fields[] = 'postcode="' . $postcode . '"';
		} else
		{	$fail[] = 'You must give your postcode';
		}
		
		if ($data['emailadd'])
		{	if ($this->ValidEMail($data['emailadd']))
			{	$fields[] = 'email="' . $this->SQLSafe($data['emailadd']) . '"';
			} else
			{	$fail[] = 'You must give a valid email address';
			}
		} else
		{	$fail[] = 'You must give your email address';
		}
		
		if ($address = $this->SQLSafe($data['address']))
		{	$fields[] = 'address="' . $address . '"';
		} else
		{	$fail[] = 'You must give your address';
		}
		
		$fields[] = 'phone="' . $this->SQLSafe($data['phone']) . '"';
		$fields[] = 'title="' . $this->SQLSafe($data['title']) . '"';
		$fields[] = 'nationality="' . $this->SQLSafe($data['nationality']) . '"';
		$fields[] = 'birthctry="' . $this->SQLSafe($data['birthctry']) . '"';
		$fields[] = 'languages="' . $this->SQLSafe($data['languages']) . '"';
		$fields[] = 'religion="' . $this->SQLSafe($data['religion']) . '"';

		$fields[] = 'collegename="' . $this->SQLSafe($data['collegename']) . '"';
		$fields[] = 'coursetitle="' . $this->SQLSafe($data['coursetitle']) . '"';
		$fields[] = 'qualification="' . $this->SQLSafe($data['qualification']) . '"';
		$fields[] = 'studylevel="' . $this->SQLSafe($data['studylevel']) . '"';
		
		$fields[] = 'howlonguk_m=' . (int)$data['howlonguk_m'];
		$fields[] = 'howlonguk_y=' . (int)$data['howlonguk_y'];
		$fields[] = 'weeks=' . (int)$data['weeks'];
		$fields[] = 'highqual="' . $this->SQLSafe($data['highqual']) . '"';
		$fields[] = 'studytype="' . $this->SQLSafe($data['studytype']) . '"';
		$fields[] = 'perweek_h=' . (int)$data['perweek_h'];
		$fields[] = 'perweek_d=' . (int)$data['perweek_d'];
		
		// costs
		$fields[] = 'tuition_c=' . round($data['tuition_c'], 2);
		$fields[] = 'tuition_g=' . round($data['tuition_g'], 2);
		$fields[] = 'reg_c=' . round($data['reg_c'], 2);
		$fields[] = 'reg_g=' . round($data['reg_g'], 2);
		$fields[] = 'enrol_c=' . round($data['enrol_c'], 2);
		$fields[] = 'enrol_g=' . round($data['enrol_g'], 2);
		$fields[] = 'exam_c=' . round($data['exam_c'], 2);
		$fields[] = 'exam_g=' . round($data['exam_g'], 2);
		$fields[] = 'other_c=' . round($data['other_c'], 2);
		$fields[] = 'other_g=' . round($data['other_g'], 2);
		$fields[] = 'other_list="' . $this->SQLSafe($data['other_list']) . '"';
		$fields[] = 'books_c=' . round($data['books_c'], 2);
		$fields[] = 'books_g=' . round($data['books_g'], 2);
		$fields[] = 'books_list="' . $this->SQLSafe($data['books_list']) . '"';
		$fields[] = 'equip_c=' . round($data['equip_c'], 2);
		$fields[] = 'equip_g=' . round($data['equip_g'], 2);
		$fields[] = 'equip_list="' . $this->SQLSafe($data['equip_list']) . '"';
		$fields[] = 'courseother_c=' . round($data['courseother_c'], 2);
		$fields[] = 'courseother_g=' . round($data['courseother_g'], 2);
		$fields[] = 'courseother_list="' . $this->SQLSafe($data['courseother_list']) . '"';

		$fields[] = 'reference1="' . $this->SQLSafe($data['reference1']) . '"';
		$fields[] = 'reference2="' . $this->SQLSafe($data['reference2']) . '"';
		$fields[] = 'statement="' . $this->SQLSafe($data['statement']) . '"';

		$fields[] = 'travel_from="' . $this->SQLSafe($data['travel_from']) . '"';
		$fields[] = 'travel_to="' . $this->SQLSafe($data['travel_to']) . '"';
		$fields[] = 'travel_distance="' . $this->SQLSafe($data['travel_distance']) . '"';
		$fields[] = 'travel_method="' . $this->SQLSafe($data['travel_method']) . '"';
		
		$fields[] = 'travel_c=' . ($travel_c = round($data['travel_c'], 2));
		$fields[] = 'travel_g=' . ($travel_g = round($data['travel_g'], 2));
		
		$fields[] = 'travel_weeks=' . ($travel_weeks = (int)$data['travel_weeks']);
		
		if (($travel_c || $travel_g) && !$travel_weeks)
		{	$fail[] = 'You must give the number of weeks left if you are claiming travel costs per week';
		}
		
		if (($d = (int)$data['ddob']) && ($m = (int)$data['mdob']) && ($y = (int)$data['ydob']))
		{	$fields[] = 'dob="' . $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y)) . '"';
		} else
		{	$fail[] = 'Please enter your date of birth';
		}
		
		if ($this->sex_options[$data['sex']])
		{	$fields[] = 'sex="' . $data['sex'] . '"';
		} else
		{	$fail[] = 'Please say if you are male of female';
		}
		
		if ($this->fund_options[$data['fundtype']])
		{	$fields[] = 'fundtype="' . $data['fundtype'] . '"';
		} else
		{	$fail[] = 'Please select a fund type';
		}
		
		if (($d = (int)$data['dstartdate']) && ($m = (int)$data['mstartdate']) && ($y = (int)$data['ystartdate']))
		{	$fields[] = 'startdate="' . $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y)) . '"';
		}
		if (($d = (int)$data['denddate']) && ($m = (int)$data['menddate']) && ($y = (int)$data['yenddate']))
		{	$fields[] = 'enddate="' . $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y)) . '"';
		}
		
		// now look for files
		$fileuploads = array();
		if ($files['cv_file'])
		{	if ($files['cv_file']['size'] && !$files['cv_file']['error'])
			{	if (in_array($this->FiletypeFromFilename($files['cv_file']['name']), array('doc', 'rtf', 'pdf', 'zip', 'txt')))
				{	$fileuploads['CV'] = $files['cv_file'];
				} else
				{	$fail[] = 'Your CV is not one of the accepted formats';
					@unlink($files['cv_file']['tmp_name']);
				}
			}
		} else
		{	$fail[] = 'Your CV upload has failed';
			@unlink($files['cv_file']['tmp_name']);
		}
		if ($files['photo_file'])
		{	if ($files['photo_file']['size'] && !$files['photo_file']['error'])
			{	if (in_array($this->FiletypeFromFilename($files['photo_file']['name']), array('png', 'jpg', 'jpeg')))
				{	$fileuploads['Photo'] = $files['photo_file'];
				} else
				{	$fail[] = 'Your photo upload has failed';
					@unlink($files['photo_file']['tmp_name']);
				}
			}
		} else
		{	$fail[] = 'Your photo is not one of the accepted formats';
			@unlink($files['photo_file']['tmp_name']);
		}
		
		// check captcha
		if (!$fail)
		{	$captcha = $this->Captcha();
			if (!$captcha->VerifyInput())
			{	$fail[] = 'captcha code has not been entered correctly';
			}
		}

		if (!$fail)
		{	$sql = 'INSERT INTO grantapps SET ' . implode(', ', $fields);
		//	echo $sql;
		//	return;
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->id = $this->db->InsertID();
					$success[] = 'Your application has been submitted';
					$this->Get($this->id);
					
					// now look for files
					if ($fileuploads)
					{	foreach ($fileuploads as $filetype=>$file)
						{	$sql = 'INSERT INTO grantappsfiles SET gaid=' . $this->id . ', filetype="' . $this->SQLSafe($filetype) . '", filename="' . $this->SQLSafe($file['name']) . '"';
							$this->db->Query($sql);
							if (!file_exists($this->FileDir()))
							{	mkdir($this->FileDir());
							}
							move_uploaded_file($file['tmp_name'], $this->FileDir() . $file['name']);
						}
					}
					$this->AdminEmail();
					$this->UserConfirmEmail();
				}
			} //else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		}
	
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn SaveFromUser
	
	private function Captcha()
	{	if ($this->captcha === false)
		{	$this->captcha = new ReCaptcha();
		}
		return $this->captcha;
	} // end of fn Captcha
	
	function FullName()
	{	return trim($this->details['firstname'] . ' '. $this->details['surname']);
	} // end of fn FullName
	
	public function AdminEmail()
	{	
		ob_start();
		$sep = "\n";
		
		echo 'A new grant application has been received ...', $sep, 'Name: ', stripslashes($this->FullName()), $sep;
		if ($this->details['userid'])
		{	echo 'Registered Student: ';
			if (($student = new Student($this->details['userid'])) && $student->id)
			{	echo $this->InputSafeString($student->GetName()), ' - ', SITE_URL, 'iiadmin/member.php?id=', $student->id;
			} else
			{	echo 'student id ', $this->details['userid'], ' not found';
			}
			echo $sep;
		}
		echo 'Phone: ', stripslashes($this->details['phone']), $sep, 'Email: ', stripslashes($this->details['email']), $sep, 'Address: ', $sep, stripslashes($this->details['address']), $sep, 'Postcode: ', stripslashes($this->details['postcode']), $sep, $sep, 'Grant requested £', number_format($this->TotalGrant(), 2), $sep, $sep, 'View in admin panel: ', SITE_URL, 'iiadmin/grantapp.php?id=', $this->id;
		
		//$this->VarDump($mailbody);
		$mail = new HTMLMail();
		$mail->SetSubject('IIDR Grant application');
		$mail->SendEMailForArea('GRANTAPPS', '', ob_get_clean());
		
	} // end of fn AdminEmail
	
	public function UserConfirmEmail()
	{	$mailfields = array();
		$mailfields['firstname'] = $this->details['firstname'];
		$mailfields['surname'] = $this->details['surname'];
		$mailfields['site_url'] = $this->link->GetLink();
		$mailfields['grantapp_ref'] = $this->link->GetLink();
		$mailfields['site_link_html'] = '<a href="' . $mailfields['site_url'] . '">visit IIDR</a>';
		$mailtemplate = new MailTemplate('grant_app_confirm');
		$mail = new HTMLMail();
		$mail->SetSubject($mailtemplate->details['subject']);
	
		if ($mail->Send($this->details['email'], $mailtemplate->BuildHTMLEmailText($mailfields), $mailtemplate->BuildHTMLPlainText($mailfields)))
		{	return true;
		}
	} // end of fn UserConfirmEmail
	
	public function TotalCosts()
	{	$cost = $this->details['tuition_c'] + $this->details['reg_c'] + $this->details['enrol_c'] + $this->details['exam_c'] + $this->details['other_c'] + $this->details['books_c'] + $this->details['equip_c'] + $this->details['courseother_c'] + ($this->details['travel_weeks'] * $this->details['travel_c']);
		
		return $cost;
	} // end of fn TotalCosts
	
	public function TotalGrant()
	{	$grant = $this->details['tuition_g'] + $this->details['reg_g'] + $this->details['enrol_g'] + $this->details['exam_g'] + $this->details['other_g'] + $this->details['books_g'] + $this->details['equip_g'] + $this->details['courseother_g'] + ($this->details['travel_weeks'] * $this->details['travel_g']);
		return $grant;
	} // end of fn TotalGrant
	
	public function UserInputForm($user = false)
	{	ob_start();
		if ($this->id)
		{	$sentpage = new PageContent('grant-sent');
			if ($text = $sentpage->HTMLMainContent())
			{	echo '<div class="the-content">', $text, '</div>';
			}
		} else
		{	class_exists('Form');
			if ($data = $this->SaveDataFromPost($_POST))
			{	if (($d = (int)$_POST['ddob']) && ($m = (int)$_POST['mdob']) && ($y = (int)$_POST['ydob']))
				{	$data['dob'] = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
				}
				if (($d = (int)$_POST['dstartdate']) && ($m = (int)$_POST['mstartdate']) && ($y = (int)$_POST['ystartdate']))
				{	$data['startdate'] = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
				}
				if (($d = (int)$_POST['denddate']) && ($m = (int)$_POST['menddate']) && ($y = (int)$_POST['yenddate']))
				{	$data['enddate'] = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
				}
			} else
			{	if (is_a($user, 'Student') && $user->id)
				{	$data = $this->DefaultsFromStudent($user);
				}
			}
			echo '<h1>Apply for a Grant</h1><div class="contactpage-wrapper"><div class="col4-wrapper "><div id="grantform_container" class="inner clearfix"><form class="contactform" action="" method="post" enctype="multipart/form-data">
				<div class="grantTopHalfLeft">
					<div class="clearfix">
						<label>Title:</label><select name="ga_title"><option value="">please choose from ...</option>';
			foreach ($this->persontitles as $key=>$value)
			{	echo '<option value="', $key, '"', $key == $data['ga_title'] ? ' selected="selected"' : '', '>', $this->InputSafeString($value), '</option>';
			}
			echo '</select>
					</div>
					<div class="clearfix">
						<label>First name:*</label>
						<input name="ga_firstname" type="text" class="text" value="', $this->InputSafeString($data['firstname']), '" required="required" />
						<input name="name" type="text" style="display:none;" >
					</div>
					<div class="clearfix">
						<label>Surname:*</label>
						<input name="ga_surname" type="text" class="text" value="', $this->InputSafeString($data['surname']), '" required="required" />
					</div>
					<div class="clearfix">    
						<label>Email:*</label>
						<input name="ga_emailadd" type="text" value="', $this->InputSafeString($data['emailadd']), '"  required="required" />
						<input name="email" type="text" style="display:none;" >
					</div>
					<div class="clearfix">    
						<label>Phone Number:*</label>
						<input name="ga_phone" type="text" class="text" value="', $this->InputSafeString($data['phone']), '" required="required" />
					</div>
					<div class="clearfix">
						<label>Address:*</label>
						<textarea class="textarea" name="ga_address" required="required">', $this->InputSafeString($data['address']), '</textarea>
					</div>
					<div class="clearfix">
						<label>Postcode:*</label>
						<input name="ga_postcode" type="text" class="text" value="', $this->InputSafeString($data['postcode']), '" required="required" />
					</div>
				</div>
				<div class="grantTopHalfRight">
					<div class="clearfix">', $user->DOBInputField($data['dob'], true), '</div>
					<div class="clearfix">
						<label>Male or Female:</label>
						<select name="sex" required="required"><option value="">please choose from ...</option>';
				foreach ($this->sex_options as $option=>$text)
				{	echo '<option value="', $option, '"', $option == $data['sex'] ? ' selected="selected"' : '', '>', $this->InputSafeString($text), '</option>';
				}
				echo '</select></div>
					<div class="clearfix">
						<label>How long have you lived in the UK?:</label>
						<input name="howlonguk_y" type="text" class="text short number" value="', (int)$data['howlonguk_y'], '" /><span>years</span>
						<input name="howlonguk_m" type="text" class="text short number" value="', (int)$data['howlonguk_m'], '" /><span>months</span>
					</div>
					<div class="clearfix">
						<label>Country of birth:</label>
						<input name="birthctry" type="text" class="text" value="', $this->InputSafeString($data['birthctry']), '" />
					</div>
					<div class="clearfix">
						<label>Nationality:</label>
						<input name="nationality" type="text" class="text" value="', $this->InputSafeString($data['nationality']), '" />
					</div>
					<div class="clearfix">
						<label>Languages:</label>
						<textarea class="textarea" name="languages">', $this->InputSafeString($data['languages']), '</textarea>
					</div>
					<div class="clearfix">
						<label>Religion:</label>
						<input name="religion" type="text" class="text" value="', $this->InputSafeString($data['religion']), '" />
					</div>
				</div>
				<div class="clear"></div>
				<div class="grantFullWidth">
					<h3>Fund Type</h3>
					<div class="clearfix">
						<label>Please select the type of grant you are applying for:</label>
						<select name="fundtype" required="required"><option value="">please choose from ...</option>';
				foreach ($this->fund_options as $option=>$text)
				{	echo '<option value="', $option, '"', $option == $data['fundtype'] ? ' selected="selected"' : '', '>', $this->InputSafeString($text), '</option>';
				}
				echo '</select></div>
				</div>
				<div class="clear"></div>
				<div class="grantFullWidth">
					<h3>Course for which you are requesting a grant</h3>
					<div class="clearfix">
						<label>Name and address of college/university/training agency:</label>
						<textarea class="textarea" name="collegename">', $this->InputSafeString($data['collegename']), '</textarea>
					</div>
					<div class="clearfix">
						<label>Title of course:</label>
						<input name="coursetitle" type="text" class="text" value="', $this->InputSafeString($data['coursetitle']), '" />
					</div>
					<div class="clearfix">
						<label>Qualification acquired at end of studies:</label>
						<input name="qualification" type="text" class="text" value="', $this->InputSafeString($data['qualification']), '" />
					</div>
					<div class="clearfix">
						<label>Level of Study (e.g. further education, higher education):</label>
						<input name="studylevel" type="text" class="text" value="', $this->InputSafeString($data['studylevel']), '" />
					</div>
					<div class="clearfix">
						<label>Start date:</label>';
			$startdateinput = new FormLineDate('', 'startdate', $data['startdate'], $this->datefn->GetYearList(date('Y'), 10), array(), array(), true, false, date('Y'));
			$startdateinput->OutputField();
			echo '</div>
					<div class="clearfix">
						<label>Finish date:</label>';
			$startdateinput = new FormLineDate('', 'enddate', $data['enddate'], $this->datefn->GetYearList(date('Y'), 10), array(), array(), true, false, date('Y'));
			$startdateinput->OutputField();
			echo '</div>
					<div class="clearfix">
						<label>Number of weeks attending college (excl. holidays):</label>
						<input name="weeks" type="text" class="text short number" value="', (int)$data['weeks'], '" />
					</div>
					<div class="clearfix">
						<label>Your highest qualification to date:</label>
						<input name="highqual" type="text" class="text" value="', $this->InputSafeString($data['highqual']), '" />
					</div>
					<div class="clearfix">
						<label>Type of study:</label><select name="studytype"><option value="">please choose from ...</option>';
			foreach ($this->studytypes as $key=>$value)
			{	echo '<option value="', $key, '"', $key == $data['studytype'] ? ' selected="selected"' : '', '>', $this->InputSafeString($value), '</option>';
			}
			echo '</select>
					</div>
					<div class="clearfix">
						<label>If studying part time, how many hours and days per week?:</label>
						<input name="perweek_h" type="text" class="text short number" value="', (int)$data['perweek_h'], '" /><span>Hours</span>
						<input name="perweek_d" type="text" class="text short number" value="', (int)$data['perweek_d'], '" /><span>Days</span>
					</div>
				</div>
				<div class="grantFullWidth">
					<h3>Amount of grant requested</h3>
					<p>When completing this section please note that grants are allocated on a discretionary basis</p>
					<table>
						<tr><th>Item</th><th>Total cost (&pound;)</th><th>Grant requested (&pound;)</th></tr>
						<tr class="subheading"><td colspan="3">Fees (written confirmation from college is required)</td></tr>
						<tr><td class="costLabel">Tuition</td><td class="costAmount"><input type="text" name="tuition_c" id="cost_', ++$ccount, '" value="', number_format($data['tuition_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="tuition_g" id="grant_', $ccount, '" value="', number_format($data['tuition_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Registration</td><td class="costAmount"><input type="text" name="reg_c" id="cost_', ++$ccount, '" value="', number_format($data['reg_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="reg_g" id="grant_', $ccount, '" value="', number_format($data['reg_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Enrolment</td><td class="costAmount"><input type="text" name="enrol_c" id="cost_', ++$ccount, '" value="', number_format($data['enrol_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="enrol_g" id="grant_', $ccount, '" value="', number_format($data['enrol_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Examination</td><td class="costAmount"><input type="text" name="exam_c" id="cost_', ++$ccount, '" value="', number_format($data['exam_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="exam_g" id="grant_', $ccount, '" value="', number_format($data['exam_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Other (specify)<textarea name="other_list">', $this->InputSafeString($data['other_list']), '</textarea></td><td class="costAmount"><input type="text" name="other_c" id="cost_', ++$ccount, '" value="', number_format($data['other_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="other_g" id="grant_', $ccount, '" value="', number_format($data['other_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr class="subheading"><td colspan="3">Course Costs</td></tr>
						<tr><td class="costLabel">Required books - give list of book titles and prices<textarea name="books_list">', $this->InputSafeString($data['books_list']), '</textarea></td><td class="costAmount"><input type="text" name="books_c" id="cost_', ++$ccount, '" value="', number_format($data['books_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="books_g" id="grant_', $ccount, '" value="', number_format($data['books_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Equipment - give list of items of equipment and prices<textarea name="equip_list">', $this->InputSafeString($data['equip_list']), '</textarea></td><td class="costAmount"><input type="text" name="equip_c" id="cost_', ++$ccount, '" value="', number_format($data['equip_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="equip_g" id="grant_', $ccount, '" value="', number_format($data['equip_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr><td class="costLabel">Other - not childcare, specify AND give a list of items and prices<textarea name="courseother_list">', $this->InputSafeString($data['courseother_list']), '</textarea></td><td class="costAmount"><input type="text" name="courseother_c" id="cost_', ++$ccount, '" value="', number_format($data['courseother_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="courseother_g" id="grant_', $ccount, '" value="', number_format($data['courseother_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr class="subheading"><td colspan="3">Travel (term time only)</td></tr>
						<tr><td colspan="3"><div class="detailBlock"><div><span>From</span><input type="text" name="travel_from" class="medium" value="', $this->InputSafeString($data['travel_from']), '" /><span>to</span><input type="text" name="travel_to" class="medium" value="', $this->InputSafeString($data['travel_to']), '" /><span> distance</span><input type="text" name="travel_distance" class="medium" value="', $this->InputSafeString($data['travel_distance']), '" /><div class="clear"></div></div><div><span>Method of travel</span><input type="text" name="travel_method" class="medium" value="', $this->InputSafeString($data['travel_method']), '" /><span> Number of weeks remaining</span><input type="text" id="travel_weeks" name="travel_weeks" class="short number" value="', (int)$data['travel_weeks'], '" /><div class="clear"></div></div></div></td></tr>
						<tr><td class="costLabel">Costs per week</td><td class="costAmount"><input type="text" name="travel_c" id="travel_c" value="', number_format($data['travel_c'], 2, '.', ''), '" onchange="CalculateTotals();" /></td><td class="costAmount"><input type="text" name="travel_g" id="travel_g" value="', number_format($data['travel_g'], 2, '.', ''), '" onchange="CalculateTotals();" /></td></tr>
						<tr class="totals"><td class="costLabel">Totals</td><td class="costAmount"><span class="total_cost" id="total_c"></span></td><td class="costAmount"><span class="total_cost" id="total_g"></span></td></tr>
					</table>
					<h3>References and Statement</h3>
					<h4>References to exclude family and friends</h4>
					<div class="clearfix">
						<label>Reference 1:</label>
						<textarea name="reference1" class="grantRef">', $this->InputSafeString($data['reference1']), '</textarea>
					</div>
					<div class="clearfix">
						<label>Reference 2:</label>
						<textarea name="reference2" class="grantRef">', $this->InputSafeString($data['reference2']), '</textarea>
					</div>
					<h4>Please explain how this funding will help you? And how this course/qualification will benefit you? (minimum 150 words)</h4>
					<div class="clearfix">
						<label>Your statement:</label>
						<textarea name="statement" class="grantState">', $this->InputSafeString($data['statement']), '</textarea>
					</div>
					<div class="clearfix">
						<label>Upload your CV (zip, doc, txt, rtf or pdf):</label>
						<input type="file" name="cv_file" />
					</div>
					<div class="clearfix">
						<label>Upload a photo of yourself (jpg or png):</label>
						<input type="file" name="photo_file" />
					</div>';
			$legals_page = new PageContent('terms-and-policies');
			$captcha = $this->Captcha();
			echo '<h3>Authorisation</h3>
<p><input type= "checkbox" name="submit_authorise" ', $data['submit_authorise'] ? 'checked="checked" ' : '', ' required="required" />&nbsp;I have read and agree to the <a href="', $legals_page->Link(), '" target="_blank" alt="', $this->InputSafeString($legals_page->details['pagetitle']), '" >terms and conditions</a> *</p>
<p>Data Protection: The information you provide in this application form will be held and processed in accordance with the Data Protection Act 1998 and will be used by IIDR and its agents to enable IIDR to carry out grant processing, analysis, auditing, accounting and evaluation. The information on this form may be used as a case study for use on our website, in publicity and reports; personal details will be changed to ensure anonymity. We may need to discuss the information on this form with other agencies and organisations. However we need your consent to do this.</p>
<p>By submitting this form you are consenting to IIDR recording and sharing relevant personal information about you.</p>
				</div>
				<div class="clearfix">', $captcha->OutputInForm(), '</div>
				<div class="clearfix">
					<input type="submit" name="submit" class="submit" value="Submit" />
				</div>
				</form></div><div class="clear"></div></div>';
		}
		return ob_get_clean();
	} // end of fn UserInputForm
	
	public function StatusString()
	{	if (isset($this->details['adminstatus']))
		{	return $this->status_options[(int)$this->details['adminstatus']];
		}
	} // end of fn StatusString
	
} // end of class GrantApp
?>