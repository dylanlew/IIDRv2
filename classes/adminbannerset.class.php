<?php
class AdminBannerSet extends BannerSet
{	
	public function __construct($id = 0)
	{	parent::__construct($id);	
	} // end of fn __construct
	
	public function CanDelete()
	{	return false;
	} // end of fn CanDelete
	
	public function Delete()
	{
		if ($result = $this->db->Query('DELETE FROM bannersets WHERE id=' . (int)$this->id))
		{	if ($this->db->AffectedRows())
			{	$this->db->Query('DELETE FROM banneritems WHERE setid=' . (int)$this->id);
				$this->Reset();
				return true;
			}
		}
	} // end of fn Delete
	
	public function InputForm()
	{
		if(!$data = $this->details)
		{	$data = $_POST;	
		}
		
		ob_start();
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id, 'set_edit');
		
		$form->AddTextInput('ID', 'title', $this->InputSafeString($data['title']), 'long', 255);
		$form->AddSubmitButton('', 'Save', 'submit');
		$form->Output();
		
		return ob_get_clean();
	} // end of fn InputForm
	
	public function ListItems()
	{	ob_start();
		if ($this->id)
		{
			echo '<h3>Items</h3><table><tr class="newlink"><th colspan="5"><a href="banneritem.php?bannerid=', $this->id, '">new banner item</a></th></tr><tr><th>Display Order</th><th>Title, Description and URL</th><th>Image</th><th>Video</th><th>Actions</th></tr>';
			foreach ($this->items as $item)
			{	//$img = new GalleryPhoto($item->details['itemid']);
				echo '<tr><td>', $item->details['disporder'], '</td><td><h4>', $this->InputSafeString($item->details['disptitle']), '</h4><div>', $this->InputSafeString($item->details['dispdesc']), '</div>', $item->details['url'] ? ('<p>When clicked: <a href="'. $item->details['url'] .'">'. $item->details['url'] .'</a></p>') : '', '</td><td>';
				if ($item->details['itemid'] && ($img = new GalleryPhoto($item->details['itemid'])))
				{	echo '<img src="', $img->HasImage('default'), '" alt="" width="300" />';
				}
				echo '</td><td>';
				if ($item->details['multimedia'] && ($mm = new Multimedia($item->details['multimedia'])) && $mm->id)
				{	echo $this->InputSafeString($mm->details['mmname']);
				}
				echo '</td><td><a href="banneritem.php?id=', $item->id, '">edit</a>&nbsp;|&nbsp;<a href="banneritem.php?id=', $item->id, '&delete=1">delete</a></td></tr>';
			}
			echo '</table>';
		}
		return ob_get_clean();
	} // end of fn ListItems
	
	function Save($data = array())
	{
		$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($title = $this->SQLSafe($data['title']))
		{	$fields[] = 'title="' . $title . '"';
			if ($this->id && ($data['title'] != $this->details['title']))
			{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['title'], 'actionto'=>$data['title']);
			}
		} else
		{	$fail[] = 'ID missing';
		}	
		
		if ($this->id || !$fail)
		{
			$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE bannersets SET ' . $set . ' WHERE id=' . $this->id;
			} else
			{	$sql = 'INSERT INTO bannersets SET ' . $set;
			}
			
			if($this->db->Query($sql))
			{
				if($this->db->AffectedRows())
				{	
					if ($this->id)
					{	
						$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	
						$this->id = $this->db->InsertID();
						$success[] = 'New banner set created';
						$this->RecordAdminAction(array('tablename'=>'bannersets', 'tableid'=>$this->id, 'area'=>'bannersets', 'action'=>'created'));
					}
					$this->Get($this->id);
				
				} else
				{	
					if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'bannersets', 'tableid'=>$this->id, 'area'=>'bannersets');
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
	
} // end of class AdminBannerSet
?>