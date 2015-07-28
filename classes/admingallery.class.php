<?php
class AdminGallery extends Gallery
{	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // fn __construct
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		
		$form->AddTextInput('Title', 'title', $this->InputSafeString($data['title']), 'long', 255);
		$form->AddTextInput('Description', 'description', $this->InputSafeString($data['description']), 'long', 255);
		$form->AddCheckBox('Show live', 'live', '1', $data['live']);
		$form->AddRawText($this->PhotoList());
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create Gallery', 'submit');
		
		if ($this->CanDelete())
		{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', 
					$_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 
					'delete this gallery</a></p>';
		}
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	function Save($data = array())
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

		$cover = (int)$data['cover'];
		$fields[] = 'cover=' . $cover;
		if ($this->id && ($cover != $this->details['cover']))
		{	$admin_actions[] = array('action'=>'Cover photo', 'actionfrom'=>$this->details['cover'], 'actionto'=>$cover);
		}
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE galleries SET ' . $set . ' WHERE gid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO galleries SET ' . $set;
			}
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New gallery created';
						$this->RecordAdminAction(array('tablename'=>'galleries', 'tableid'=>$this->id, 'area'=>'galleries', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'galleries', 'tableid'=>$this->id, 'area'=>'galleries');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function CanDelete()
	{	return $this->id && !$this->photos;
	} // end of fn CanDelete
	
	protected function DeleteExtra()
	{	
	} // end of fn DeleteExtra
	
	public function PhotoList()
	{	ob_start();
		if ($this->id)
		{	echo '<table><tr class="newlink"><th colspan="7"><a href="galleryphoto.php?gid=', $this->id, '">New photo for this gallery</a></th></tr><tr><th></th><th>Title</th><th>Description</th><th>Display order</th><th>Live</th><th>Cover for gallery?</th><th>Actions</th></tr>';
			foreach ($this->photos as $photo_row)
			{	$photo = new AdminGalleryPhoto($photo_row);
				echo '<tr><td>';
				if ($src = $photo->HasImage('thumbnail'))
				{	echo '<img src="', $src, '?', time(), '" />';
				}
				echo '</td><td>', $this->InputSafeString($photo->details['title']), '</td><td>', $this->InputSafeString($photo->details['description']), '</td><td>', (int)$photo->details['displayorder'], '</td><td>', $photo->details['live'] ? 'Yes' : '', '</td><td><input type="radio" name="cover" value="', $photo->id, '" ', (($photo->id == $this->details['cover']) || (!$this->details['cover'] && !$photocount++)) ? 'checked="checked" ' : '', '/></td><td><a href="galleryphoto.php?id=', $photo->id, '">edit</a>';
				if ($photo->CanDelete())
				{	echo '&nbsp;|&nbsp;<a href="galleryphoto.php?id=', $photo->id, '&delete=1">delete</a>';
				}
				echo '</td></tr>';
			}
			echo '</table>';
		}
		return ob_get_clean();
	} // end of fn PhotoList
	
} // end of class defn AdminGallery
?>