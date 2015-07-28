<?php
class AdminProductPhoto extends ProductPhoto
{
	
	public function __construct($id = null)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function InputForm($product_id = 0)
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
			if (!isset($data['gid']))
			{	$data['gid'] = (int)$gallery_id;
			}
		}
		
		if ($this->id)
		{	$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, '');
		} else
		{	$form = new Form($_SERVER['SCRIPT_NAME'] . '?prodid=' . $product_id, '');
		}
		
		$form->AddTextInput('Title (uses product if blank)', 'phototitle', $this->InputSafeString($data['phototitle']), 'long', 255);
		$form->AddFileUpload('Image file', 'photofile');
		if ($this->id && ($src = $this->HasImage('default')))
		{	$form->AddRawText('<label>current image</label><img src="' . $src . '?' . time() . '" height="200px" /><br />');
		}
		$form->AddTextInput('Display order', 'listorder', (int)$data['listorder'], 'short num', 6);
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create Image', 'submit');
		
		if ($this->CanDelete())
		{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this image</a></p>';
		}
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	function Save($data = array(), $prodid = 0, $photo_image = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
	
		$phototitle = $this->SQLSafe($data['phototitle']);
		$fields[] = 'phototitle="' . $phototitle . '"';
		if ($this->id && ($data['phototitle'] != $this->details['phototitle']))
		{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['phototitle'], 'actionto'=>$data['phototitle']);
		}

		$listorder = (int)$data['listorder'];
		$fields[] = 'listorder=' . $listorder;
		if ($this->id && ($listorder != $this->details['listorder']))
		{	$admin_actions[] = array('action'=>'Display Order', 'actionfrom'=>$this->details['listorder'], 'actionto'=>$listorder);
		}
		
		if (!$this->id)
		{	if (($prodid = (int)$prodid) && ($product = new StoreProduct($prodid)) && $product->id)
			{	$fields[] = 'prodid=' . $prodid;
			} else
			{	$fail[] = 'product not found';
			}
			// check for photo upload
			if (!$this->ValidPhotoUpload($photo_image))
			{	$fail[] = 'you must upload a valid image';
			}
		}
		
		if ($this->id || !$fail)
		{	$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE storeproducts_photos SET ' . $set . ' WHERE sppid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO storeproducts_photos SET ' . $set;
			}
			
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array('tablename'=>'storeproducts_photos', 'tableid'=>$this->id, 'area'=>'storeproducts_photos');
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New image created';
						$this->RecordAdminAction(array('tablename'=>'storeproducts_photos', 'tableid'=>$this->id, 'area'=>'storeproducts_photos', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
			}
			
			if ($this->id && $photo_image['size'])
			{	//print_r($photo_image);
				if ($this->ValidPhotoUpload($photo_image))
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
				} else
				{	$fail[] = 'error uploading photo (jpegs, pngs only)';
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function ValidPhotoUpload($photo_image = array())
	{	return is_array($photo_image) && (stristr($photo_image['type'], 'jpeg') || stristr($photo_image['type'], 'jpg') || stristr($photo_image['type'], 'png')) && !$photo_image['error'];
	} // end of fn ValidPhotoUpload
	
	public function CanDelete()
	{	return true;
	} // end of fn CanDelete
	
	public function DeleteExtra()
	{	foreach ($this->imagesizes as $size_name=>$size)
		{	@unlink($this->GetImageFile($size_name));
		}
	} // end of fn DeleteExtra
	
} // end of defn AdminProductPhoto
?>