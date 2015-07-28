<?php
class AdminStoreProductDownload extends StoreProductDownload
{	
	function __construct($id = '')
	{	parent::__construct($id);
	} // fn __construct
	
	function CanDelete()
	{	
		if ($this->id)
		{	// check for any purchases of products
			
			return true;
		}
		
		return false;

	} // end of fn CanDelete
	
	public function DeleteExtra()
	{	@unlink($this->FileFilename());
	} // end of fn DeleteExtra
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT pfid FROM storeproductfiles WHERE fileslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT pfid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['pfid'];
			}
		}
		return false;
	} // end of fn SlugExists
	
	function Save($data = array(), $prodid = 0, $file = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($filetitle = $this->SQLSafe($data['filetitle']))
		{	$fields[] = 'filetitle="' . $filetitle . '"';
			if ($this->id && ($data['filetitle'] != $this->details['filetitle']))
			{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['filetitle'], 'actionto'=>$data['filetitle']);
			}
		} else
		{	$fail[] = 'title missing';
		}
	
		if ($fileslug = $this->ValidSlug(($this->id && $data['fileslug']) ? $data['fileslug'] : $filetitle))
		{	$fields[] = 'fileslug="' . $fileslug . '"';
			if ($this->id && ($data['fileslug'] != $this->details['fileslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['fileslug'], 'actionto'=>$data['fileslug']);
			}
		} else
		{	$fail[] = 'slug missing';
		}
		
		$filepass = $this->SQLSafe($data['filepass']);
		$fields[] = 'filepass="' . $filepass . '"';
		if ($this->id && ($data['filepass'] != $this->details['filepass']))
		{	$admin_actions[] = array('action'=>'Password', 'actionfrom'=>$this->details['filepass'], 'actionto'=>$data['filepass']);
		}
		
		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}
		
		if (!$this->id)
		{	if ($prodid = (int)$prodid)
			{	$fields[] = 'prodid=' . $prodid;
			} else
			{	$fail[] = 'product missing';
			}
			
			// check for valid filetype
			if ($file && is_array($file) && $file['size'])
			{	if (!$this->valid_types[$filetype = $this->FiletypeFromFilename($file['name'])])
				{	$fail[] = 'file is not a valid type (must be ' . $this->ValidTypesString() . ')' . $filetype;
				}
			} else
			{	$fail[] = 'file missing';
			}
		}

		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE storeproductfiles SET ' . $set . ' WHERE pfid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO storeproductfiles SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New product download created';
						$this->RecordAdminAction(array('tablename'=>'storeproductfiles', 'tableid'=>$this->id, 'area'=>'storeproductfiles', 'action'=>'created'));
					}
					$this->Get($this->id);
				}
				
				if ($this->id)
				{	
					$this->Get($this->id);
					
					if ($file && is_array($file) && $file['size'])
					{	if ($file['error'])
						{	$fail[] = 'file upload failed';
						} else
						{	if ($this->valid_types[$filetype = $this->FiletypeFromFilename($file['name'])])
							{	//$this->VarDump($file);
								if (move_uploaded_file($file['tmp_name'], $this->FileFilename()))
								{	$success[] = 'new file for download uploaded';
									// record filetype
									$sql = 'UPDATE storeproductfiles SET filetype="' . $filetype . '" WHERE pfid=' . $this->id;
									$this->db->Query($sql);
									$this->details['filetype'] = $filetype;
								}
							} else
							{	$fail[] = 'file is not a valid type (must be ' . $this->ValidTypesString() . ')';
							}
						}
					}
				}
			
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'storeproductfiles', 'tableid'=>$this->id, 'area'=>'storeproductfiles');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function ValidTypesString($separator = ', ')
	{	$names = array();
		foreach ($this->valid_types as $type=>$details)
		{	$names[] = $details['label'];
		}
		return implode($separator, $names);
	} // end of fn ValidTypesString
	
	function InputForm($prodid = 0)
	{	
		ob_start();

		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id . '&prodid=' . (int)$prodid);
		$form->AddTextInput('Title', 'filetitle', $this->InputSafeString($data['filetitle']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Slug', 'fileslug', $this->InputSafeString($data['fileslug']), 'long', 255, 1);
		}
		$form->AddTextInput('File password (if required)', 'filepass', $this->InputSafeString($data['filepass']), '', 50, 1);
		
		$form->AddCheckBox('Live', 'live', '1', $data['live']);
		$form->AddFileUpload('File for download (' . $this->ValidTypesString() . ')', 'filedownload');
		if ($this->FileExists())
		{	$form->AddRawText('<p><label>Current File</label>' . $this->DownloadName() . '<br class="clear" /></p>');
		}
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Download File', 'submit');
		if ($histlink = $this->DisplayHistoryLink('multimedia', $this->id))
		{	echo '<p>', $histlink, '</p>';
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this download file</a></p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn AdminStoreProductDownload
?>