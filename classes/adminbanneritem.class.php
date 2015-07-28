<?php

class AdminBannerItem extends BannerItem
{	
	public function __construct($id = 0)
	{	parent::__construct($id);	
	} // end of fn __construct
	
	public function Save($data = array(), $setid = 0)
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		//$this->VarDump($data);
		if (!$this->id)
		{	if ($setid = (int)$setid)
			{	$fields[] = 'setid=' . $setid;
				if ($this->id && ($data['setid'] != $this->details['setid']))
				{	$admin_actions[] = array('action'=>'Set ID', 'actionfrom'=>$this->details['setid'], 'actionto'=>$data['setid']);
				}
			} else
			{	$fail[] = "set id missing";
			}
		}
		
		if ($disptitle = $this->SQLSafe($data['disptitle']))
		{	$fields[] = 'disptitle="' . $disptitle . '"';
			if ($this->id && ($data['disptitle'] != $this->details['disptitle']))
			{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['disptitle'], 'actionto'=>$data['disptitle']);
			}
		} else
		{	$fail[] = "title is missing";
		}
		
		$dispdesc = $this->SQLSafe($data['dispdesc']);
		$fields[] = 'dispdesc="' . $dispdesc . '"';
		if ($this->id && ($data['dispdesc'] != $this->details['dispdesc']))
		{	$admin_actions[] = array('action'=>'Description', 'actionfrom'=>$this->details['dispdesc'], 'actionto'=>$data['dispdesc']);
		}
		
		$url = $this->SQLSafe($data['url']);
		$fields[] = 'url="' . $url . '"';
		if ($this->id && ($data['url'] != $this->details['url']))
		{	$admin_actions[] = array('action'=>'URL', 'actionfrom'=>$this->details['url'], 'actionto'=>$data['url']);
		}
		
		$disporder = (int)$data['disporder'];
		$fields[] = 'disporder=' . $disporder;
		if ($this->id && ($data['disporder'] != $this->details['disporder']))
		{	$admin_actions[] = array('action'=>'Display Order', 'actionfrom'=>$this->details['disporder'], 'actionto'=>$data['disporder']);
		}
		
		$itemid = (int)$data['itemid'];
		$multimedia = (int)$data['multimedia'];
		if ($itemid || $multimedia)
		{	$fields[] = 'itemid=' . $itemid;
			if ($this->id && ($data['itemid'] != $this->details['itemid']))
			{	$admin_actions[] = array('action'=>'Item ID', 'actionfrom'=>$this->details['itemid'], 'actionto'=>$data['itemid']);
			}
			$fields[] = 'multimedia=' . $multimedia;
			if ($this->id && ($data['multimedia'] != $this->details['multimedia']))
			{	$admin_actions[] = array('action'=>'Multimedia', 'actionfrom'=>$this->details['multimedia'], 'actionto'=>$data['multimedia']);
			}
		} else
		{	$fail[] = "you must have an image or a video for the banner item";
		}
		
		if ($setid && ($this->id || !$fail))
		{
			$set = implode(', ', $fields);
			if ($this->id)
			{	$sql = 'UPDATE banneritems SET ' . $set . ' WHERE id=' . $this->id;
			} else
			{	$sql = 'INSERT INTO banneritems SET ' . $set;
			}	
			
			if($this->db->Query($sql))
			{
				if ($this->db->AffectedRows())
				{
					if($this->id)
					{
						$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{
						$this->id = $this->db->InsertID();
						$success[] = 'New banner item created';
						$this->RecordAdminAction(array('tablename'=>'banneritems', 'tableid'=>$this->id, 'area'=>'banneritems', 'action'=>'created'));
					}
					$this->Get($this->id);
				}
				else
				{
					if (!$this->id)
					{	$fail[] = 'Insert failed';
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'banneritems', 'tableid'=>$this->id, 'area'=>'banneritems');
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
	{	return $this->id;
	} // end of fn CanDelete
	
	public function InputForm($setid = 0)
	{
		if (!$data = $this->details)
		{	$data = $_POST;	
		}
		
		ob_start();
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id . '&bannerid=' . (int)$setid, 'set_edit');
		
		//$form->AddHiddenInput('setid', (int)$setid);
		$form->AddTextInput('Title', 'disptitle', $this->InputSafeString($data['disptitle']), 'long', 255);
		$form->AddTextArea('Description', 'dispdesc', $this->InputSafeString($data['dispdesc']), '', 0, 0, 3, 60);
		$form->AddTextInput('Go to URL (when clicked)', 'url', $this->InputSafeString($data['url']), 'long', 255);
		$form->AddTextInput('Display order', 'disporder', $this->InputSafeString($data['disporder']), 'short num', 10);
		$form->AddMultiInput('Image from Gallery', array(array('type'=>'TEXT', 'name'=>'itemid', 'value'=>(int)$data['itemid'], 'css'=>'inputHidden'), array('type'=>'RAW', 'text'=>'<a onclick="OpenGalleries();">choose image</a>')));
		ob_start();
		echo '<Label>&nbsp;</label><span id="itemImageContainer">';
		if ($itemid = (int)$data['itemid'])
		{	$photo = new AdminGalleryPhoto($itemid);
			echo $photo->AdminPhotoDisplay('default', 0, 100);
		}
		echo '</span><br />';
		$form->AddRawText(ob_get_clean());
		
				
		ob_start();
		echo '<h3>Video from library:</h3><label id="bannerVideoPicked">', ($data['multimedia'] && ($mm = new MultiMedia($data['multimedia'])) && $mm->id) ? $this->InputSafeString($mm->details['mmname']) : 'none','</label><input type="hidden" name="multimedia" id="bannerVideoValue" value="', (int)$data['multimedia'], '" /><span class="dataText"><a onclick="BannerVideoPicker();">change this</a></span><br />';
		$form->AddRawText(ob_get_clean());
		
		$form->AddSubmitButton('', 'Save', 'submit');
		
		if ($this->CanDelete())
		{	echo '<p><a href="banneritem.php?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you want to ' : '', 'delete this item from the banner</a></p>';
		}
		
		$form->Output();
		echo '<script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#gal_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="gal_modal_popup" class="jqmWindow"><a href="#" class="jqmClose submit">Close</a><div id="galModalInner"></div></div></div>';
		echo $this->CVideoPickerPopUp();
		return ob_get_clean();
	} // end of fn InputForm

	public function CVideoPickerPopUp()
	{	ob_start();
		echo '<script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#cvpp_modal_popup").jqm();});</script>',
			'<!-- START instructor list modal popup --><div id="cvpp_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: 640px; margin-left: -320px; top: 10px; height: 600px; "><a href="#" class="jqmClose submit">Close</a><div id="cvppModalInner" style="height: 500px; overflow:auto;"></div></div></div>';
		return ob_get_clean();
	} // end of fn CVideoPickerPopUp

} // end of class AdminBannerItem
?>