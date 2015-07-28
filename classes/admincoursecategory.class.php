<?php
class AdminCourseCategory extends CourseCategory
{	
	function __construct($id = 0)
	{	parent::__construct($id);	
	} // fn __construct
	
	public function GetCourses($liveonly = false)
	{	return parent::GetCourses($liveonly);
	} // end of fn GetCourses
	
	function CanDelete()
	{	return $this->id && !$this->subcats && !$this->GetCourses() && !$this->GetAskImam();
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM coursecategories WHERE cid=' . $this->id))
			{	if ($this->db->AffectedRows())
				{	
					$this->RecordAdminAction(array('tablename'=>'coursecategories', 'tableid'=>$this->id, 'area'=>'course categories', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT cid FROM coursecategories WHERE catslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT cid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['cid'];
			}
		}
		return false;
	} // end of fn SlugExists

	function Save($data = array(), $bgfile = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($ctitle = $this->SQLSafe($data['ctitle']))
		{	$fields[] = 'ctitle="' . $ctitle . '"';
			if ($this->id && ($data['ctitle'] != $this->details['ctitle']))
			{	$admin_actions[] = array('action'=>'Name', 'actionfrom'=>$this->details['ctitle'], 'actionto'=>$data['ctitle']);
			}
		} else
		{	$fail[] = 'name missing';
		}
	
		// create slug
		if ($catslug = $this->ValidSlug(($this->id && $data['catslug']) ? $data['catslug'] : $ctitle))
		{	$fields[] = 'catslug="' . $catslug . '"';
			if ($this->id && ($catslug != $this->details['catslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['catslug'], 'actionto'=>$data['catslug']);
			}
		} else
		{	if ($ctitle)
			{	$fail[] = 'slug missing';
			}
		}
		
		$cattext = $this->SQLSafe($data['cattext']);
		$fields[] = 'cattext="' . $cattext . '"';
		if ($this->id && ($data['cattext'] != $this->details['cattext']))
		{	$admin_actions[] = array('action'=>'Text', 'actionfrom'=>$this->details['cattext'], 'actionto'=>$data['cattext']);
		}
		
		if ($this->types[$cattype = $data['cattype']])
		{	$fields[] = 'cattype="' . $cattype . '"';
			if ($this->id && ($cattype != $this->details['cattype']))
			{	$admin_actions[] = array('action'=>'Type', 'actionfrom'=>$this->details['cattype'], 'actionto'=>$cattype);
			}
		} else
		{	$fail[] = 'type missing';
		}
		
		if ($parentcat = (int)$data['parentcat'])
		{	if ($parents = $this->GetPossibleParents())
			{	if ($parents[$parentcat])
				{	$fields[] = 'parentcat=' . $parentcat;
					if ($this->id && ($parentcat != $this->details['parentcat']))
					{	$admin_actions[] = array('action'=>'Parent category', 'actionfrom'=>$this->details['parentcat'], 'actionto'=>$parentcat, 'actiontype'=>'link', 'linkmask'=>'coursecatedit.php?id={linkid}');
					}
				} else
				{	$fail[] = 'parent not found';
				}
			}
		} else
		{	$fields[] = 'parentcat=0';
			if ($this->id && $this->details['parentcat'])
			{	$admin_actions[] = array('action'=>'Parent category', 'actionfrom'=>$this->details['parentcat'], 'actionto'=>0, 'actiontype'=>'link', 'linkmask'=>'coursecatedit.php?id={linkid}');
			}
		}
		
	/*	$live = $data['live'] ? '1' : '0';
		$fields[] = 'live=' . $live;
		if ($this->id && ($data['live'] != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live', 'actionfrom'=>$this->details['live'], 'actionto'=>$data['live'], 'actiontype'=>'boolean');
		}*/
		$fields[] = 'live=1';
		
		$banner = (int)$data['banner'];
		$fields[] = 'banner=' . $banner;
		if ($this->id && ($banner != $this->details['banner']))
		{	$admin_actions[] = array('action'=>'Banner', 'actionfrom'=>$this->details['banner'], 'actionto'=>$banner);
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE coursecategories SET ' . $set . ' WHERE cid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO coursecategories SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array('tablename'=>'coursecategories', 'tableid'=>$this->id, 'area'=>'course categories');
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = 'Changes saved';
					} else
					{	if ($this->id = $this->db->InsertID())
						{	$success[] = 'New category created';
							$this->RecordAdminAction(array('tablename'=>'coursecategories', 'tableid'=>$this->id, 'area'=>'course categories', 'action'=>'created'));
						}
					}
					$this->Get($this->id);
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
			}
			
			if ($this->id && $bgfile['size'])
			{	//print_r($course_banner);
				if ((!stristr($bgfile['type'], 'jpeg') && !stristr($bgfile['type'], 'jpg') && !stristr($bgfile['type'], 'png')) || $bgfile['error'])
				{	$fail[] = 'error uploading background (jpegs, pngs only)';
				} else
				{	if (stristr($bgfile['type'], 'png'))
					{	move_uploaded_file($bgfile['tmp_name'], $this->GetImageFile());
					} else
					{	$oldimage = imagecreatefromjpeg($bgfile['tmp_name']);
						ob_start();
						imagepng($oldimage, NULL, 3);
						file_put_contents($this->GetImageFile(), ob_get_clean());
						unlink($bgfile['tmp_name']);
					}
					$success[] = 'background uploaded';
			//		if ($this->ReSizePhotoPNG($course_banner['tmp_name'], $this->GetImageFile('banner'), $this->imagesizes['banner'][0], $this->imagesizes['banner'][1], stristr($course_banner['type'], 'png') ? 'png' : 'jpg'))
			//		{	$success[] = 'course banner uploaded';
			//		}
			//		unlink($course_banner['tmp_name']);
				}
			} else
			{	if ($data['del_bg'])
				{	if (@unlink($this->GetImageFile()))
					{	$success[] = 'background deleted';
					}
				}
			}
			
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		$form->AddTextInput('Catgeory name', 'ctitle', $this->InputSafeString($data['ctitle']), 'long', 255, 1);
		$form->AddSelect('Type', 'cattype', $data['cattype'], '', $this->types, false, true);
		if ($this->id)
		{	$form->AddTextInput('Catgeory slug (in page url)', 'catslug', $this->InputSafeString($data['catslug']), 'long', 255, 1);
		}
		if ($parents = $this->GetPossibleParents())
		{	$form->AddSelectWithGroups('Parent category', 'parentcat', $data['parentcat'], '', $parents, 1, 0, '');
		}
		$form->AddTextArea('Page header text', 'cattext', stripslashes($data['cattext']), 'tinymce', 0, 0, 10, 60);
	//	$form->AddCheckBox($catname, 'live', '1', $data['live']);
				
		ob_start();
		echo '<label>Banner:</label><br /><label id="bannerPicked">', ($data['banner'] && ($banner = new BannerSet($data['banner'])) && $banner->id) ? $this->InputSafeString($banner->details['title']) : 'none','</label><input type="hidden" name="banner" id="bannerValue" value="', (int)$data['banner'], '" /><span class="dataText"><a onclick="BannerPicker();">change this</a></span><br />';
		$form->AddRawText(ob_get_clean());
		echo $this->BannerPickerPopUp();
		
		$form->AddFileUpload('Background image (jpeg, jpg or png):', 'bgfile');
		if ($src = $this->HasImage())
		{	$form->AddRawText('<label>Current background</label><img src="' . $src . '?' . time() . '" height="200px" /><br />');
			$form->AddCheckBox('Delete this', 'del_bg', '1', '0');
		}
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Category', 'submit');
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this category</a></p>';
			}
			
			if ($histlink = $this->DisplayHistoryLink('coursecategories', $this->id))
			{	echo '<p>', $histlink, '</p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm

	public function BannerPickerPopUp()
	{	ob_start();
		echo '<script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#banner_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="banner_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: 640px; margin-left: -320px; top: 10px; height: 600px; "><a href="#" class="jqmClose submit">Close</a><div id="bannerModalInner" style="height: 500px; overflow:auto;"></div></div>';
		return ob_get_clean();
	} // end of fn BannerPickerPopUp
	
	public function GetPossibleParents($parentid = 0, $prefix = '')
	{	$parents = array();
		$sql = 'SELECT * FROM coursecategories WHERE parentcat=' . (int)$parentid;
		if ($this->id)
		{	$sql .= ' AND NOT cid=' . $this->id;
		}
		$sql .= ' ORDER BY ctitle';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$this->subcats[$row['cid']])
				{	$parents[$row['cid']] = $prefix . $this->InputSafeString($row['ctitle']);
					if ($children = $this->GetPossibleParents($row['cid'], '-&nbsp;' . $prefix))
					{	foreach ($children as $pid=>$ptitle)
						{	$parents[$pid] = $ptitle;
						}
					}
				}
			}
		}
		return $parents;
	} // end of fn GetPossibleParents
	
	public function GetAskImam($liveonly = false)
	{	return parent::GetAskImam(false);
	} // end of fn GetAskImam
	
} // end of class AdminCourseCategory
?>