<?php
class AdminSubscriptionProduct extends SubscriptionProduct
{
	public function __construct($id = null)
	{	parent::__construct($id);
	} // end of fn __construct
	
	public function CanDelete()
	{	return $this->id;
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	$sql = 'DELETE FROM subproducts WHERE id=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->DeletePhotos();
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	public function InputForm()
	{	ob_start();
		
		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'course_edit');
		$this->AddBackLinkHiddenField($form);
		$form->AddTextInput('Product name', 'title', $this->InputSafeString($data['title']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Slug (for url)', 'slug', $this->InputSafeString($data['slug']), 'long', 255, 1);
		}
		
		$form->AddTextInput('Price', 'price', number_format($data['price'], 2, '.', ''), 'number', 11);
		$form->AddTextInput('Months duration', 'months', (int)$data['months'], 'number', 3);
		$form->AddSelect('Tax rate', 'taxid', $data['taxid'], '', $this->TaxRatesForDropDown(), true, false);
		$form->AddCheckBox('Live (visible in front-end)', 'live', '1', $data['live']);
		$form->AddTextInput('List order', 'listorder', (int)$data['listorder'], 'number', 5);

		//$form->AddTextArea('Overview', 'overview', $this->InputSafeString($data['overview']), 'tinymce', 0, 0, 15, 60);
		$form->AddTextArea('Description', 'description', $this->InputSafeString($data['description']), 'tinymce', 0, 0, 20, 60);
		
		if ($img = $this->HasImage('thumbnail'))
		{	$form->AddRawText('<label>Existing product image</label><img src="' . $img . '?' . time() . '" /><br />');
			$form->AddCheckBox('Delete this', 'delphoto');
		}
		$form->AddFileUpload('Product image (png, jpg or jpeg; ideally square; thumbnail will be created)', 'product_image');
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Subscription Product', 'submit');
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this subscription product</a></p>';
			}
			
			if ($histlink = $this->DisplayHistoryLink('subproducts', $this->id))
			{	echo '<p>', $histlink, '</p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm

	public function SlugExists($slug = '')
	{	$sql = 'SELECT id FROM subproducts WHERE slug="' . $this->SQLSafe($slug) . '"';
		if ($this->id)
		{	$sql .= ' AND NOT id=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['id'];
			}
		}
		return false;
	} // end of fn SlugExists
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	function Save($data = array(), $product_image = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($title = $this->SQLSafe($data['title']))
		{	$fields[] = 'title="' . $title . '"';
			if ($this->id && ($data['title'] != $this->details['title']))
			{	$admin_actions[] = array('action'=>'Name', 'actionfrom'=>$this->details['title'], 'actionto'=>$data['title']);
			}
		} else
		{	$fail[] = 'name missing';
		}
	
		// create slug
		if ($slug = $this->ValidSlug(($this->id && $data['slug']) ? $data['slug'] : $title))
		{	$fields[] = 'slug="' . $slug . '"';
			if ($this->id && ($slug != $this->details['slug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['slug'], 'actionto'=>$data['slug']);
			}
		} else
		{	if ($pagetitle)
			{	$fail[] = 'slug missing';
			}
		}
		
		$overview = $this->SQLSafe($data['overview']);
		$fields[] = 'overview="' . $overview . '"';
		if ($this->id && ($data['overview'] != $this->details['overview']))
		{	$admin_actions[] = array('action'=>'Overview', 'actionfrom'=>$this->details['overview'], 'actionto'=>$data['overview'], 'actiontype'=>'html');
		}
		
		$description = $this->SQLSafe($data['description']);
		$fields[] = 'description="' . $description . '"';
		if ($this->id && ($data['description'] != $this->details['description']))
		{	$admin_actions[] = array('action'=>'Description', 'actionfrom'=>$this->details['description'], 'actionto'=>$data['description'], 'actiontype'=>'html');
		}
		
		$price = round($data['price'], 2);
		$fields[] = 'price=' . $price;
		if ($this->id && ($price != $this->details['price']))
		{	$admin_actions[] = array('action'=>'Price', 'actionfrom'=>$this->details['price'], 'actionto'=>$data['price']);
		}
		
		if ($months = (int)$data['months'])
		{	$fields[] = 'months=' . $months;
			if ($this->id && ($months != $this->details['months']))
			{	$admin_actions[] = array('action'=>'Months', 'actionfrom'=>$this->details['months'], 'actionto'=>$data['months']);
			}
		} else
		{	$fail[] = 'number of months is missing';
		}
		
		$listorder = (int)$data['listorder'];
		$fields[] = 'listorder=' . $listorder;
		if ($this->id && ($listorder != $this->details['listorder']))
		{	$admin_actions[] = array('action'=>'List order', 'actionfrom'=>$this->details['listorder'], 'actionto'=>$data['listorder']);
		}
		
		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}
		
		$taxrates = $this->TaxRatesForDropDown();
		if ($taxid = (int)$data['taxid'])
		{	if ($taxrates[$taxid])
			{	$fields[] = 'taxid=' . $taxid;
				if ($this->id && ($taxid != $this->details['taxid']))
				{	$admin_actions[] = array('action'=>'Tax rate', 'actionfrom'=>$taxrates[$this->details['taxid']], 'actionto'=>$taxrates[$taxid]);
				}
			} else
			{	$fail[] = 'Tax rate not found';
			}
		} else
		{	$fields[] = 'taxid=0';
			if ($this->id && $this->details['taxid'])
			{	$admin_actions[] = array('action'=>'Tax rate', 'actionfrom'=>$taxrates[$this->details['taxid']], 'actionto'=>'');
			}
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE subproducts SET ' . $set . ' WHERE id=' . $this->id;
			} else
			{	$sql = 'INSERT INTO subproducts SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New subscription product created';
						$this->RecordAdminAction(array('tablename'=>'subproducts', 'tableid'=>$this->id, 'area'=>'subproducts', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'subproducts', 'tableid'=>$this->id, 'area'=>'subproducts');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
				
			}
			
			if ($this->id)
			{	if ($product_image['size'])
				{	if ((!stristr($product_image['type'], 'jpeg') && !stristr($product_image['type'], 'jpg') && !stristr($product_image['type'], 'png')) || $product_image['error'])
					{	$fail[] = 'error uploading image (jpegs, pngs only)';
					} else
					{	$photos_created = 0;
						foreach ($this->imagesizes as $size_name=>$size)
						{	if (!file_exists($this->ImageFileDirectory($size_name)))
							{	mkdir($this->ImageFileDirectory($size_name));
							}
							if ($this->ReSizePhotoPNG($product_image['tmp_name'], $this->GetImageFile($size_name), $size[0], $size[1], stristr($product_image['type'], 'png') ? 'png' : 'jpg'))
							{	$photos_created++;
							}
						}
						unset($product_image['tmp_name']);
						if ($photos_created)
						{	$success[] = 'product image uploaded';
						}
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
			
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function DeletePhotos()
	{	foreach ($this->imagesizes as $sizename=>$size)
		{	@unlink($this->GetImageFile($sizename));
		}
	} // end of fn DeletePhotos

	function ReSizePhotoPNG($uploadfile = '', $file = '', $maxwidth = 0, $maxheight = 0, $imagetype = '')
	{	$isize = getimagesize($uploadfile);
		$ratio = $maxwidth / $isize[0];
		$h_ratio = $maxheight / $isize[1];
		if ($h_ratio > $ratio)
		{	$ratio = $h_ratio;
		}
		switch ($imagetype)
		{	case 'png': $oldimage = imagecreatefrompng($uploadfile);
							break;
			case 'jpg':
			case 'jpeg': $oldimage = imagecreatefromjpeg($uploadfile);
							break;
		}
		
		if ($oldimage)
		{	$w_new = ceil($isize[0] * $ratio);
			$h_new = ceil($isize[1] * $ratio);
			
			if ($ratio != 1)
			{	$newimage = imagecreatetruecolor($w_new,$h_new);
				if ($imagetype == 'png')
				{	imagealphablending( $newimage, false );
					imagesavealpha( $newimage, true );
				}
				imagecopyresampled($newimage,$oldimage,0,0,0,0,$w_new, $h_new, $isize[0], $isize[1]);
			} else
			{	$newimage = $oldimage;
				if ($imagetype == 'png')
				{	imagealphablending( $newimage, false );
					imagesavealpha( $newimage, true );
				}
			}
			
			// now get middle chunk - horizontally
			if ($w_new > $maxwidth || $h_new > $maxheight)
			{	$resizeimg = imagecreatetruecolor($maxwidth,$maxheight);
				if ($imagetype == 'png')
				{	imagealphablending( $resizeimg, false );
					imagesavealpha( $resizeimg, true );
				}
				$leftoffset = floor(($w_new - $maxwidth) / 2);
				imagecopyresampled($resizeimg, $newimage,0,0,floor(($w_new - $maxwidth) / 2),floor(($h_new - $maxheight) / 2),$maxwidth, $maxheight, $maxwidth, $maxheight);
				$newimage = $resizeimg;
			}
			
			ob_start();
			imagepng($newimage, NULL, 3);
			return file_put_contents($file, ob_get_clean());
		}
	} // end of fn ReSizePhotoPNG
	
	public function BundlesList()
	{	if ($this->id)
		{	ob_start();
			echo '<h2>Bundles involving this product</h2><table><tr class="newlink"><th colspan="6"><a href="bundleedit.php">Create new bundle</a></th></tr><tr><th>Title</th><th>Description</th><th>Products</th><th>Discount</th><th>Live?</th><th>Actions</th></tr>';
			foreach ($this->GetBundles() as $bundle_row)
			{	$bundle = new AdminBundle($bundle_row);
				echo '<tr class="stripe', $i++ % 2, '"><td>', $this->InputSafeString($bundle->details['bname']), '</td><td>', nl2br($this->InputSafeString($bundle->details['bdesc'])), '</td><td>', $bundle->ProductTextList('<br />'), '</td><td>',number_format($bundle->details['discount'], 2), '</td><td>', $bundle->details['live'] ? 'Yes' : 'No', '</td><td><a href="bundleedit.php?id=', $bundle->id, '">edit</a>';
				if ($histlink = $this->DisplayHistoryLink('bundles', $bundle->id))
				{	echo '&nbsp;|&nbsp;', $histlink;
				}
				if ($bundle->CanDelete())
				{	echo '&nbsp;|&nbsp;<a href="bundleedit.php?id=', $bundle->id, '&delete=1">delete</a>';
				}
				echo '</td></tr>';
			}
			echo "</table>";
			return ob_get_clean();
		}
	} // end of fn BundlesList
	
	public function GetBundles()
	{	$bundles = array();
		$sql = 'SELECT bundles.* FROM bundles, bundleproducts WHERE bundles.bid=bundleproducts.bid AND pid=' . (int)$this->id . ' AND bundleproducts.ptype="store" ORDER BY bundles.bid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$bundles[$row['bid']] = $row;
			}
		}
		return $bundles;
	} // end of fn GetBundles
	
} // end of class AdminSubscriptionProduct
?>