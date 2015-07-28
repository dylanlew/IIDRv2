<?php
class AdminGalleryPhoto extends GalleryPhoto
{
	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function InputForm($gallery_id = 0)
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
			if (!isset($data['gid']))
			{	$data['gid'] = (int)$gallery_id;
			}
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextInput('Title', 'title', $this->InputSafeString($data['title']), 'long', 255);
		$form->AddTextInput('Description', 'description', $this->InputSafeString($data['description']), 'long', 255);
		$form->AddSelect('Gallery', 'gid', $data['gid'], '', $this->GalleriesAvailable(), 1, 0);
		$form->AddFileUpload('Upload photo', 'photofile');
		if ($this->id && ($src = $this->HasImage('default')))
		{	$form->AddRawText('<label>current photo</label><img src="' . $src . '?' . time() . '" height="200px" /><br />');
		}
		$form->AddTextInput('Display order', 'displayorder', (int)$data['displayorder'], 'short num', 6);
		$form->AddCheckBox('Show live', 'live', '1', $data['live']);
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create Photo', 'submit');
		
		if ($this->CanDelete())
		{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this photo</a></p>';
		}
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	public function GalleriesAvailable()
	{	static $galleries = false;
		if ($galleries === false)
		{	$galleries = array();
			$sql = 'SELECT gid, title FROM galleries ORDER BY title, gid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$galleries[$row['gid']] = $this->InputSafeString($row['title']);
				}
			}
		}
		return $galleries;
	} // end of fn GalleriesAvailable
	
	function Save($data = array(), $photo_image = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($title = $this->SQLSafe($data['title']))
		{	$fields[] = 'title="' . $title . '"';
			if ($this->id && ($data['title'] != $this->details['title']))
			{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['title'], 'actionto'=>$data['title']);
			}
		} else
		{	$fail[] = 'title missing';
		}
		
		$description = $this->SQLSafe($data['description']);
		$fields[] = 'description="' . $description . '"';
		if ($this->id && ($data['description'] != $this->details['description']))
		{	$admin_actions[] = array('action'=>'Description', 'actionfrom'=>$this->details['description'], 'actionto'=>$data['description']);
		}

		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}

		$displayorder = (int)$data['displayorder'];
		$fields[] = 'displayorder=' . $displayorder;
		if ($this->id && ($displayorder != $this->details['displayorder']))
		{	$admin_actions[] = array('action'=>'Display Order', 'actionfrom'=>$this->details['displayorder'], 'actionto'=>$displayorder);
		}
		
		if ($gid = (int)$data['gid'])
		{	$galleries = $this->GalleriesAvailable();
			if ($galleries[$gid])
			{	$fields[] = 'gid=' . $gid;
				if ($this->id && ($gid != $this->details['gid']))
				{	$admin_actions[] = array('action'=>'Gallery', 'actionfrom'=>$this->details['gid'], 'actionto'=>$gid);
				}
			} else
			{	$fail[] = 'gallery not found';
			}
		} else
		{	$fail[] = 'gallery must be specified';
		}
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE galleryphotos SET ' . $set . ' WHERE id=' . $this->id;
			} else
			{	$sql = 'INSERT INTO galleryphotos SET ' . $set;
			}
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New photo created';
						$this->RecordAdminAction(array('tablename'=>'galleryphotos', 'tableid'=>$this->id, 'area'=>'galleryphotos', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'galleryphotos', 'tableid'=>$this->id, 'area'=>'galleryphotos');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
			
			if ($this->id && $photo_image['size'])
			{	//print_r($photo_image);
				if ((!stristr($photo_image['type'], 'jpeg') && !stristr($photo_image['type'], 'jpg') && !stristr($photo_image['type'], 'png')) || $photo_image['error'])
				{	$fail[] = 'error uploading photo (jpegs, pngs only)';
				} else
				{	$photos_created = 0;
					foreach ($this->imagesizes as $size_name=>$size)
					{	if (!file_exists($this->ImageFileDirectory($size_name)))
						{	mkdir($this->ImageFileDirectory($size_name));
						}
						if ($this->ReSizePhotoPNG($photo_image['tmp_name'], $this->GetImageFile($size_name), $size[0], $size[1], stristr($photo_image['type'], 'png') ? 'png' : 'jpg'))
						{	$photos_created++;
						}
					}
					unset($photo_image['tmp_name']);
					if ($photos_created)
					{	$success[] = 'photo uploaded';
					}
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function CanDelete()
	{	return true;
	} // end of fn CanDelete
	
	public function AdminPhotoDisplay($size = 'default', $w = 0, $h = 0)
	{	ob_start();
		echo '<img src="', $this->HasImage($size), '"';
		if ($w = (int)$w)
		{	echo ' width="', $w, 'px"';
		}
		if ($h = (int)$h)
		{	echo ' height="', $h, 'px"';
		}
		echo ' />';
		return ob_get_clean();
	} // end of fn AdminPhotoDisplay
	
} // end of defn AdminGalleryPhoto
?>