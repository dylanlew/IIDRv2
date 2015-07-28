<?php
class AdminDiscount extends Discount
{	
	function __construct($id = 0)
	{	parent::__construct($id);
	} // end of fn __construct
	
	function CountryList()
	{	$countries = array();
		
		$adminuser = new AdminUser((int)$_SESSION[SITE_NAME]['auserid']);
		$sql = 'SELECT * FROM countries WHERE paypalac>0 ORDER BY shortname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[$row['ccode']] = $this->InputSafeString($row['shortname']);
			}
		}
		
		return $countries;
		
	} // end of fn CountryList
	
	function InputForm()
	{	ob_start();
		if (!$data = $this->details)
		{	$data = $_POST;
			
			if (($d = (int)$data['dlastdate']) && ($m = (int)$data['mlastdate']) && ($y = (int)$data['ylastdate']))
			{	$data['lastdate'] = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
			}
			
		}
		
		if ($data['courseid'])
		{	
			$course = new Course($data['courseid']);
			$content_select = $course->details['coursecontent'];
			if ($course->id)
			{	$data['country'] = '';
				$country = new Country($course->details['country']);
				$currency = $country->GetCurrency();
				$cursymbol =  $currency->details['cursymbol'];
			}
			
		} else
		{	if ($data['country'])
			{	$country = new Country($data['country']);
				$currency = $country->GetCurrency();
				$cursymbol =  $currency->details['cursymbol'];
			}
		}
		if ($data['courseidprev'])
		{	
			$courseprev = new Course($data['courseidprev']);
			$content_selectprev = $courseprev->details['coursecontent'];
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		if (!$this->id || $this->CanDelete())
		{	$form->AddTextInput('Discount code', 'disccode', $this->InputSafeString($data['disccode']), '', 20, 1);
		} else
		{	$form->AddRawText('<label>Discount code</label><label class="content_label">' . $this->InputSafeString($data['disccode']) . '</label><br />');
		}
		$form->AddSelect('Course (if specific)', $name = 'content_select', $content_select, '', $this->GetAvailableCourses(), 1, 0, 'onchange="jsGetCourses(0);"');
		$form->AddRawText('<div id="nb_schedule">');
		if ($content_select)
		{	$form->AddRawText($this->ScheduleDropDown($content_select, $data['courseid']));
		}
		$form->AddRawText('</div>');
		$form->AddSelect('or country (if specific)', 'country', $data['country'], '', $this->CountryList(), true, false, 'onchange="DECountryChange();"');
		$form->AddTextInput('Percentage discount', 'discpc', (int)$data['discpc'], 'short', 3);
		$form->AddTextInput('or Fixed amount <span id="ce_cursymbol">' . $cursymbol . '</span><br />(for country or course only)', 'discamount', number_format($data['discamount'], 2, '.', ''), 'short', 10);
		$form->AddDateInput('Last date valid (blank for no time limit)', 'lastdate', $data['lastdate'], array(), array(), 
							array(), true);
		$maxuses_label = 'Usage limit (0 for no limit)';
		if ($this->id)
		{	$maxuses_label .= '<br />uses so far: ' . $this->BookingsCount();
		}
		$form->AddTextInput($maxuses_label, 'maxuses', (int)$data['maxuses'], 'short', 6);
		$form->AddSelect('Previous course needed', $name = 'content_selectprev', $content_selectprev, '', $this->GetAvailableCourses(), 1, 0, 'onchange="jsGetCourses(0, \'prev\');"');
		$form->AddRawText('<div id="nb_scheduleprev">');
		if ($content_selectprev)
		{	$form->AddRawText($this->ScheduleDropDown($content_selectprev, $data['courseidprev'], 'prev'));
		}
		$form->AddRawText('</div>');
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Discount', 'submit');
		if ($histlink = $this->DisplayHistoryLink('discounts', $this->id))
		{	echo '<p>', $histlink, '</p>';
		}
		if ($this->CanDelete())
		{	
			echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this discount</a></p>';
		}
		$form->Output();
		return ob_get_clean();

	} // end of fn InputForm

	function ScheduleDropDown($contentid = 0, $courseid = 0, $namextra = "")
	{	ob_start();
		if ($schedule = $this->GetSchedule($contentid))
		{	class_exists("Form");
			$select = new FormLineSelect("Schedule", "courseid$namextra", (int)$courseid, "", $schedule, true, false,"onchange='DECourseChange();'");
			$select->Output();
		} else
		{	if ($contentid)
			{	echo "<p><label></label><span>Sorry nothing available for this course</span></p>";
			}
		}
		return ob_get_clean();
	} // end of fn ScheduleDropDown
	
	function GetSchedule($coursecontent = 0)
	{	$schedule = array();
		$adminuser = new AdminUser((int)$_SESSION[SITE_NAME]["auserid"]);
		$sql = "SELECT * FROM courses WHERE coursecontent=" . (int)$coursecontent . " ORDER BY starttime";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($adminuser->CanAccessCity($row["city"]))
				{	$course = new Course($row);
					$schedule[$course->id] = $course->OutputLocation() . " - " . $course->DateString();
				}
			}
		}
		
		return $schedule;
	} // end of fn GetSchedule
	
	function DiscCodeUsed($code = "")
	{	
		$checksql = "SELECT discid FROM discounts WHERE disccode='" . $this->SQLSafe($code) . "'";
		
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row["discid"];
			}
		}
		
		return false;
		
	} // end of fn DiscCodeUsed
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		$country = "";
		$courseid = 0;
		
		// check for valid discount code
		if (!$this->id || ($data["disccode"] && $this->CanDelete()))
		{	if ($data["disccode"])
			{	if ($data["disccode"] == $this->InputSafeString($data["disccode"]))
				{	if ($usedid = $this->DiscCodeUsed($data["disccode"]))
					{	$fail[] = "discount code already used <a href='discountedit.php?id=$usedid'>{$data["disccode"]}</a>";
					} else
					{	$fields[] = "disccode='" . $this->SQLSafe($data["disccode"]) . "'";
						if ($this->id && ($data["disccode"] != $this->details["disccode"]))
						{	$admin_actions[] = array("action"=>"Code", "actionfrom"=>$this->details["disccode"], "actionto"=>$data["disccode"]);
						}
					}
				} else
				{	$fail[] = "invalid discount code";
				}
			} else
			{	$fail[] = "discount code missing";
			}
		}
		
		// course chosen
		if ($courseid = (int)$data["courseid"])
		{	$fields[] = "courseid=$courseid";
			if ($this->id && ($courseid != $this->details["courseid"]))
			{	$admin_actions[] = array("action"=>"Course", "actionfrom"=>$this->details["courseid"], "actionto"=>$courseid, "actiontype"=>"link", "linkmask"=>"courseedit.php?id={linkid}");
			}
			$fields[] = "country=''";
			if ($this->id && $this->details["country"])
			{	$admin_actions[] = array("action"=>"Country", "actionfrom"=>$this->details["country"], "actiontype"=>"link", "linkmask"=>"ctryedit.php?ctry={linkid}");
			}
		} else
		{	$fields[] = "courseid=0";
			if ($this->id && $this->details["courseid"])
			{	$admin_actions[] = array("action"=>"Course", "actionfrom"=>$this->details["courseid"], "actiontype"=>"link", "linkmask"=>"courseedit.php?id={linkid}");
			}
			// country chosen, if any
			if ($country = $this->SQLSafe($data["country"]))
			{	$fields[] = "country='$country'";
				if ($this->id && ($data["country"] != $this->details["country"]))
				{	$admin_actions[] = array("action"=>"Country", "actionfrom"=>$this->details["country"], "actionto"=>$data["country"], "actiontype"=>"link", "linkmask"=>"ctryedit.php?ctry={linkid}");
				}
			} else
			{	$fields[] = "country=''";
				if ($this->id && $this->details["country"])
				{	$admin_actions[] = array("action"=>"Country", "actionfrom"=>$this->details["country"], "actiontype"=>"link", "linkmask"=>"ctryedit.php?ctry={linkid}");
				}
			}
		}
		
		// previous course needed
		$courseidprev = (int)$data["courseidprev"];
		$fields[] = "courseidprev=" . $courseidprev;
		if ($this->id && ($courseidprev != $this->details["courseidprev"]))
		{	$admin_actions[] = array("action"=>"Previous course needed", "actionfrom"=>$this->details["courseidprev"], "actionto"=>$courseidprev, "actiontype"=>"link", "linkmask"=>"courseedit.php?id={linkid}");
		}
		
		if ($discpc = (int)$data["discpc"])
		{	$fields[] = "discpc=$discpc";
			if ($this->id && ($discpc != $this->details["discpc"]))
			{	$admin_actions[] = array("action"=>"Discount %", "actionfrom"=>$this->details["discpc"], "actionto"=>$discpc);
			}
			$fields[] = "discamount=0";
			if ($this->id && $this->details["discamount"])
			{	$admin_actions[] = array("action"=>"Discount amount", "actionfrom"=>$this->details["discamount"]);
			}
		} else
		{	
			if ($discamount = round($data["discamount"], 2))
			{	if ($country || $courseid)
				{	$fields[] = "discamount=$discamount";
					if ($this->id && ($discamount != $this->details["discamount"]))
					{	$admin_actions[] = array("action"=>"Discount amount", "actionfrom"=>$this->details["discamount"], "actionto"=>$discamount);
					}
					$fields[] = "discpc=0";
					if ($this->id && $this->details["discpc"])
					{	$admin_actions[] = array("action"=>"Discount %", "actionfrom"=>$this->details["discpc"]);
					}
				} else
				{	$fail[] = "fixed discounts only applicable for specific countries or courses";
				}
			} else
			{	$fail[] = "you must apply some discount";
			}
		}
		if (($d = (int)$data["dlastdate"]) && ($m = (int)$data["mlastdate"]) && ($y = (int)$data["ylastdate"]))
		{	$lastdate = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y));
			$fields[] = "lastdate='" . $lastdate . "'";
			if ($this->id && ($lastdate != $this->details["lastdate"]))
			{	$admin_actions[] = array("action"=>"Valid to", "actionfrom"=>$this->details["lastdate"], "actionto"=>$lastdate, "actiontype"=>"date");
			}
		} else
		{	$fields[] = "lastdate=''";
			if ($this->id && (int)$this->details["lastdate"])
			{	$admin_actions[] = array("action"=>"Valid to", "actionfrom"=>$this->details["lastdate"], "actiontype"=>"date");
			}
		}
		
		$maxuses = (int)$data["maxuses"];
		$fields[] = "maxuses=" . $maxuses;
		if ($this->id && ($maxuses != $this->details["maxuses"]))
		{	$admin_actions[] = array("action"=>"Max uses", "actionfrom"=>$this->details["maxuses"], "actionto"=>$maxuses);
		}
		
		if (($this->id || !$fail) && ($set = implode(", ", $fields)))
		{	
			if ($this->id)
			{	$sql = "UPDATE discounts SET $set WHERE discid={$this->id}";
			} else
			{	$sql = "INSERT INTO discounts SET $set";
			}
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array("tablename"=>"discounts", "tableid"=>$this->id, "area"=>"discounts");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New discount created";
						$this->RecordAdminAction(array("tablename"=>"discounts", "tableid"=>$this->id, "area"=>"discounts", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function Delete()
	{	if ($this->CanDelete())
		{	$sql = "DELETE FROM discounts WHERE discid=" . $this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array("tablename"=>"discounts", "tableid"=>$this->id, "area"=>"discounts", "action"=>"deleted"));
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	function CanDelete()
	{	return ($this->BookingsCount() == 0) && $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
} // end of defn AdminDiscount
?>