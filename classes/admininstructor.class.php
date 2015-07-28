<?php
class AdminInstructor extends Instructor
{	
	function __construct($id = 0)
	{	parent::__construct($id);
	} // fn __construct
	
	function GetInterviews($live_only = false)
	{	return parent::GetInterviews($live_only);	
	} // end of fn GetInterview
	
	function CanDelete()
	{	return $this->id && !$this->GetCourses() && !$this->GetPosts();
	} // end of fn CanDelete
	
	function GetPosts($live_only = false)
	{	return parent::GetPosts(false);
	} // end of fn GetPosts
	
	public function DeleteExtra()
	{	$this->DeletePhotos();
	} // end of fn DeleteExtra
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT inid FROM instructors WHERE instslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT inid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['inid'];
			}
		}
		return false;
	} // end of fn SlugExists

	function Save($data = array(), $imagefile = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		$insttitle = $this->SQLSafe($data['insttitle']);
		$fields[] = 'insttitle="' . $insttitle . '"';
		if ($this->id && ($data['insttitle'] != $this->details['insttitle']))
		{	$admin_actions[] = array('action'=>'Instructor title', 'actionfrom'=>$this->details['insttitle'], 'actionto'=>$data['insttitle']);
		}
		
		if ($instname = $this->SQLSafe($data['instname']))
		{	$fields[] = 'instname="' . $instname . '"';
			if ($this->id && ($data['instname'] != $this->details['instname']))
			{	$admin_actions[] = array('action'=>'Instructor name', 'actionfrom'=>$this->details['instname'], 'actionto'=>$data['instname']);
			}
		} else
		{	$fail[] = 'name missing';
		}
	
		// create slug
		if ($instslug = $this->ValidSlug(($this->id && $data['instslug']) ? $data['instslug'] : $instname))
		{	$fields[] = 'instslug="' . $instslug . '"';
			if ($this->id && ($instslug != $this->details['instslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['instslug'], 'actionto'=>$data['instslug']);
			}
		} else
		{	if ($instname)
			{	$fail[] = 'slug missing';
			}
		}
		
		$instbio = $this->SQLSafe($data['instbio']);
		$fields[] = 'instbio="' . $instbio . '"';
		if ($this->id && ($data['instbio'] != $this->details['instbio']))
		{	$admin_actions[] = array('action'=>'Description text', 'actionfrom'=>$this->details['instbio'], 'actionto'=>$data['instbio'], 'actiontype'=>'html');
		}

		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}

		$showfront = (int)$data['showfront'];
		$fields[] = 'showfront=' . $showfront;
		if ($this->id && ($showfront != $this->details['showfront']))
		{	$admin_actions[] = array('action'=>'Show at front?', 'actionfrom'=>$this->details['showfront'], 'actionto'=>$showfront, 'actiontype'=>'boolean');
		}
		
		$socialbar = ($data['socialbar'] ? '1' : '0');
		$fields[] = 'socialbar=' . $socialbar;
		if ($this->id && ($socialbar != $this->details['socialbar']))
		{	$admin_actions[] = array('action'=>'Social bar?', 'actionfrom'=>$this->details['socialbar'], 'actionto'=>$socialbar, 'actiontype'=>'boolean');
		}
		
		if ($catid = (int)$data['catid'])
		{	if ($cats = $this->GetPossibleCategories())
			{	if ($cats[$catid])
				{	$fields[] = 'catid=' . $catid;
					if ($this->id && ($catid != $this->details['catid']))
					{	$admin_actions[] = array('action'=>'Category', 'actionfrom'=>$this->details['catid'], 'actionto'=>$catid, 'actiontype'=>'link', 'linkmask'=>'instructorcatedit.php?id={linkid}');
					}
				} else
				{	$fail[] = 'category not found';
				}
			}
		} else
		{	if ($this->details['catid'])
			{	$admin_actions[] = array('action'=>'Category', 'actionfrom'=>$this->details['catid'], 'actionto'=>0, 'actiontype'=>'link', 'linkmask'=>'instructorcatedit.php?id={linkid}');
			}
		}
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE instructors SET ' . $set . ' WHERE inid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO instructors SET ' . $set;
			} 
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New instructor created';
						$this->RecordAdminAction(array('tablename'=>'instructors', 'tableid'=>$this->id, 'area'=>'instructors', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'instructors', 'tableid'=>$this->id, 'area'=>'instructors');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		}
		
		if ($this->id)
		{	if ($imagefile['size'])
			{	$uploaded = $this->UploadPhoto($imagefile);
				if ($uploaded['successmessage'])
				{	$success[] = $uploaded['successmessage'];
					$this->RecordAdminAction(array('tablename'=>'instructors', 'tableid'=>$this->id, 'area'=>'instructors', 'action'=>'New image uploaded'));
				}
				if ($uploaded['failmessage'])
				{	$fail[] = $uploaded['failmessage'];
				}
			} else
			{	if ($data['delphoto'])
				{	
					$this->DeletePhotos();
					$success[] = 'photo deleted';
					$this->RecordAdminAction(array('tablename'=>'instructors', 'tableid'=>$this->id, 'area'=>'instructors', 'action'=>'Image deleted'));
				}
			}
			
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	function UploadPhoto($file)
	{
		$fail = array();
		$successmessage = '';
		
		if($file['size'])
		{
			if((!stristr($file['type'], 'jpeg') && !stristr($file['type'], 'jpg') && !stristr($file['type'], 'png')) || $file['error'])
			{
				$fail[] = 'File type invalid (jpeg or png only)';
			} else
			{	foreach ($this->imagesizes as $sizename=>$size)
				{	$this->ReSizePhoto($file['tmp_name'], $this->GetImageFile($sizename), $size[0], $size[1]);
				}
				unlink($file['tmp_name']);
				
				$successmessage = 'New photo uploaded';
			}
		} else
		{
			$fail[] = 'Photo not uploaded';	
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>$successmessage);
	} // end of fn UploadPhoto
	
	public function DeletePhotos()
	{	foreach ($this->imagesizes as $sizename=>$size)
		{	@unlink($this->GetImageFile($sizename));
		}
	} // end of fn DeletePhotos
	
	function InputForm()
	{	ob_start();
		$data = $this->details;
		if (!$data = $this->details)
		{	$data = $_POST;
			
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id);
		$form->AddTextInput('Title', 'insttitle', $this->InputSafeString($data['insttitle']), '', 255, 0);
		$form->AddTextInput('Name', 'instname', $this->InputSafeString($data['instname']), '', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Slug (for URL)', 'instslug', $this->InputSafeString($data['instslug']), 'long', 255);
			if ($link = $this->Link())
			{	$form->AddRawText('<p><label>Link to people page</label><span><a href="' . $link . '" target="_blank">' . $link . '</a></span><br /></p>');
			}
		}
		if ($cats = $this->GetPossibleCategories())
		{	$form->AddSelect('Category', 'catid', $data['catid'], '', $cats, true, true);
		}
		$form->AddCheckBox('Visible on website?', 'live', '', $data['live']);
		$form->AddCheckBox('Show on people front page?', 'showfront', '', $data['showfront']);
		$form->AddCheckBox('Show social media links?', 'socialbar', 1, $data['socialbar']);
		$form->AddTextArea('Biography', 'instbio', $this->InputSafeString($data['instbio']), 'tinymce', 0, 0, 20, 60);
		$form->AddFileUpload('Photo (thumbnail will be created for you)', 'imagefile');
		if (file_exists($this->GetImageFile('thumbnail')))
		{	$form->AddRawText('<p><label>Current photo</label><img src="' . $this->GetImageSRC('thumbnail') . '" /><br /></p>');
			$form->AddCheckBox('Delete this', 'delphoto');
		}
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Instructor', 'submit');
		if ($histlink = $this->DisplayHistoryLink('instructors', $this->id))
		{	echo '<p>', $histlink, '</p>';
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this instructor</a></p>';
			}
			
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	public function GetPossibleCategories($parentid = 0, $prefix = '')
	{	$cats = array();
		$sql = 'SELECT * FROM instructorcats WHERE parentcat=' . (int)$parentid;
		$sql .= ' ORDER BY catname';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[$row['icid']] = $prefix . $this->InputSafeString($row['catname']);
				if ($children = $this->GetPossibleCategories($row['icid'], '-&nbsp;' . $prefix))
				{	foreach ($children as $pid=>$ptitle)
					{	$cats[$pid] = $ptitle;
					}
				}
			}
		}
		return $cats;
	} // end of fn GetPossibleCategories
	
	public function GetMultiMedia()
	{	return parent::GetMultiMedia(false);
	} // end of fn GetMultiMedia
	
	public function MultiMediaDisplay()
	{	ob_start();
		echo '<div class="mmdisplay"><div id="mmdContainer">', $this->MultiMediaTable(), '</div><script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#rlp_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="rlp_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: 640px; margin-left: -320px; top: 10px; height: 600px; "><a href="#" class="jqmClose submit">Close</a><div id="rlpModalInner" style="height: 500px; overflow:auto;"></div></div></div>';
		return ob_get_clean();
	} // end of fn MultiMediaDisplay
	
	public function MultiMediaTable()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="7"><a onclick="MultiMediaPopUp(', $this->id, ');">Add multimedia</a></th></tr><tr><th></th><th>Multimedia name</th><th>Type</th><th>Categories</th><th>Live?</th><th>Posted</th><th>Actions</th></tr>';
		foreach ($this->GetMultiMedia() as $mmid=>$mm_row)
		{	echo '<tr><td>';
			$mm = new AdminMultimedia($mm_row);
			if ($img_src = $mm->Thumbnail())
			{	echo '<img src="', $img_src, '" width="100px" />';
			}
			echo '</td><td class="pagetitle">', $this->InputSafeString($mm->details['mmname']), '</td><td>', $mm->MediaType(), '</td><td>', $mm->CatsList(), '</td><td>', $mm->details['live'] ? 'Yes' : 'No', '</td><td>', date('d-M-y @H:i', strtotime($mm->details['posted'])), $mm->details['author'] ? ('<br />by ' . $this->InputSafeString($mm->details['author'])) : '', '</td><td><a href="multimedia.php?id=', $mm->id, '">edit</a>&nbsp;|&nbsp;<a onclick="MultiMediaRemove(', $this->id, ',', $mmid, ');">remove from person</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn MultiMediaTable
	
	public function AddMultimedia($mmid = 0)
	{	if ($this->id && ($mmid = (int)$mmid))
		{	if ((!$mmlist = $this->GetMultiMedia()) || !$mmlist[$mmid])
			{	$sql = 'INSERT INTO instructors_mm SET inid=' . $this->id . ', mmid=' . $mmid;
				if ($result = $this->db->Query($sql))
				{	return $this->db->AffectedRows();
				}
			}
		}
	} // end of fn AddMultimedia
	
	public function RemoveMultimedia($mmid = 0)
	{	if ($this->id && ($mmid = (int)$mmid))
		{	if (($mmlist = $this->GetMultiMedia()) && $mmlist[$mmid])
			{	$sql = 'DELETE FROM instructors_mm WHERE inid=' . $this->id . ' AND mmid=' . $mmid;
				if ($result = $this->db->Query($sql))
				{	return $this->db->AffectedRows();
				}
			}
		}
	} // end of fn RemoveMultimedia
	
	public function GetActivities($liveonly = false)
	{	return parent::GetActivities($liveonly);
	} // end of fn GetActivities
	
	public function InterviewsTable()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="4"><a href="instructoriv.php?inid=', $this->id, '">Create new interview</a></th></tr><tr><th>Title</th><th>Date</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetInterviews() as $iv_row)
		{	$iv = new AdminInstructorInterview($iv_row);
			echo '<tr><td>', $this->InputSafeString($iv->details['ivtitle']), '</td><td>', date('d/m/y', strtotime($iv->details['ivdate'])), '</td><td>', $iv->details['live'] ? 'Live' : '---', '</td><td><a href="instructoriv.php?id=', $iv->id, '">edit</a>&nbsp;|&nbsp;<a href="instructoriv.php?id=', $iv->id, '&delete=1">delete</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn InterviewsTable
	
	public function ActivitiesTable()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="4"><a href="instructoract.php?inid=', $this->id, '">Create new activity</a></th></tr><tr><th>Title</th><th>Date</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetActivities() as $act_row)
		{	$act = new AdminInstructorAct($act_row);
			echo '<tr><td>', $this->InputSafeString($act->details['acttitle']), '</td><td>', date('d/m/y', strtotime($act->details['actdate'])), '</td><td>', $act->details['live'] ? 'Live' : '---', '</td><td><a href="instructoract.php?id=', $act->id, '">edit</a>&nbsp;|&nbsp;<a href="instructoract.php?id=', $act->id, '&delete=1">delete</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn ActivitiesTable
	
	public function AddGallery($gid = 0)
	{	if ($this->id && ($gid = (int)$gid))
		{	if ((!$galleries = $this->GetGalleries()) || !$galleries[$gid])
			{	$sql = 'INSERT INTO gallerytoinstructor SET inid=' . $this->id . ', gid=' . $gid;
				if ($result = $this->db->Query($sql))
				{	return $this->db->AffectedRows();
				}
			}
		}
	} // end of fn AddGallery
	
	public function RemoveGallery($gid = 0)
	{	if ($this->id && ($gid = (int)$gid))
		{	if (($galleries = $this->GetGalleries()) && $galleries[$gid])
			{	$sql = 'DELETE FROM gallerytoinstructor WHERE inid=' . $this->id . ' AND gid=' . $gid;
				if ($result = $this->db->Query($sql))
				{	return $this->db->AffectedRows();
				}
			}
		}
	} // end of fn RemoveGallery
	
	public function GalleriesDisplay()
	{	ob_start();
		echo '<div class="mmdisplay"><div id="mmdContainer">', $this->GalleriesTable(), '</div><script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#rlp_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="rlp_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: 640px; margin-left: -320px; top: 10px; height: 600px; "><a href="#" class="jqmClose submit">Close</a><div id="rlpModalInner" style="height: 500px; overflow:auto;"></div></div></div>';
		return ob_get_clean();
	} // end of fn GalleriesDisplay
	
	public function GalleriesTable()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="7"><a onclick="GalleryPopUp(', $this->id, ');">Add gallery</a></th></tr><tr><th></th><th>Title</th><th>Description</th><th>Photos</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetGalleries() as $gid=>$gallery_row)
		{	$gallery = new AdminGallery($gallery_row);
			echo '<tr><td>';
			if ($cover = $gallery->HasCoverImage('thumbnail'))
			{	echo '<img src="', $cover, '" />';
			}
			echo '</td><td>', $this->InputSafeString($gallery->details['title']), '</td><td>', $this->InputSafeString($gallery->details['description']), '</td><td>', count($gallery->photos), '</td><td>', $gallery->details['live'] ? 'Yes' : '', '</td><td><a href="gallery.php?id=', $gallery->id, '">edit</a>&nbsp;|&nbsp;<a onclick="GalleryRemove(', $this->id, ',', $gallery->id, ');">remove from instructor</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn GalleriesTable
	
	public function ReviewsDisplay()
	{	ob_start();
		echo '<div class="mmdisplay"><div id="mmdContainer">', $this->ReviewsTable(), '</div><script type="text/javascript">instID=', $this->id, ';$().ready(function(){$("body").append($(".jqmWindow"));$("#rlp_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="rlp_modal_popup" class="jqmWindow"><a href="#" class="jqmClose submit">Close</a><div id="rlpModalInner"></div></div></div>';
		return ob_get_clean();
	} // end of fn ReviewsDisplay
	
	public function ReviewsTable()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="7"><a href="instructorreview.php?pid=', $this->id, '">Create new review</a></th></tr><tr><th>Created by</th><th>Reviewer displayed</th><th>Date</th><th>Review</th><th>Status</th><th>Admin notes</th><th>Actions</th></tr>';
		$students = array();
		$adminusers = array();
		foreach ($this->GetReviews() as $review_row)
		{	$review = new AdminProductReview($review_row);
			echo '<tr><td>';
			if ($review->details['sid'])
			{	if (!$students[$review->details['sid']])
				{	$students[$review->details['sid']] = new Student($review->details['sid']);
				}
				echo 'Student: <a href="member.php?id=', $students[$review->details['sid']]->id, '">', $this->InputSafeString($students[$review->details['sid']]->GetName()), '</a>';
			} else
			{	if (!$adminusers[$review->details['admincreated']])
				{	$adminusers[$review->details['admincreated']] = new AdminUser($review->details['admincreated']);
				}
				echo 'Admin: <a href="useredit.php?userid=', $adminusers[$review->details['admincreated']]->userid, '">',  $adminusers[$review->details['admincreated']]->username, '</a>';
			}
			echo '</td><td>', $this->InputSafeString($review->details['reviewertext']), '</td><td>', date('d/m/y @H:i', strtotime($review->details['revdate'])), '</td><td>', nl2br($this->InputSafeString($review->details['review'])), '</td><td>', $review->StatusString(), '</td><td>', nl2br($this->InputSafeString($review->details['adminnotes'])), '</td><td><a href="instructorreview.php?id=', $review->id, '">edit</a>';
			if ($review->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="instructorreview.php?id=', $review->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn ReviewsTable
	
} // end of defn AdminInstructor
?>