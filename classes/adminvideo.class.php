<?php
class AdminVideo extends Video
{	
	function __construct($id = 0)
	{	parent::__construct($id);
		
	} // fn __construct
	
	function CanDelete()
	{	return $this->id && $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query("DELETE FROM videos WHERE vid=$this->id"))
			{	if ($this->db->AffectedRows())
				{	
					$this->RecordAdminAction(array("tablename"=>"videos", "tableid"=>$this->id, "area"=>"videos", "action"=>"deleted"));
					// delete image
					@unlink($this->ImageFile());
					@unlink($this->ThumbFile());
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete

	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($vtitle = $this->SQLSafe($data["vtitle"]))
		{	$fields[] = "vtitle='$vtitle'";
			if ($this->id && ($data["vtitle"] != $this->details["vtitle"]))
			{	$admin_actions[] = array("action"=>"Video title", "actionfrom"=>$this->details["vtitle"], "actionto"=>$data["vtitle"]);
			}
		}
		
		if ($vdesc = $this->SQLSafe($data["vdesc"]))
		{	$fields[] = "vdesc='$vdesc'";
			if ($this->id && ($data["vdesc"] != $this->details["vdesc"]))
			{	$admin_actions[] = array("action"=>"Video Description", "actionfrom"=>$this->details["vdesc"], "actionto"=>$data["vdesc"]);
			}
		}
		
		if ($vhits = $this->SQLSafe($data["vhits"]))
		{	$fields[] = "vhits='$vhits'";
			if ($this->id && ($data["vhits"] != $this->details["vhits"]))
			{	$admin_actions[] = array("action"=>"Video Hits", "actionfrom"=>$this->details["vhits"], "actionto"=>$data["vhits"]);
			}
		}
		
		if ($vfile = $this->SQLSafe($data["vfile"]))
		{	$fields[] = "vfile='$vfile'";
			if ($this->id && ($data["vfile"] != $this->details["vfile"]))
			{	$admin_actions[] = array("action"=>"Video File", "actionfrom"=>$this->details["vfile"], "actionto"=>$data["vfile"]);
			}
		}
		
		if ($vtype = $this->SQLSafe($data["vtype"]))
		{	$fields[] = "vtype='$vtype'";
			if ($this->id && ($data["vtype"] != $this->details["vtype"]))
			{	$admin_actions[] = array("action"=>"Video Type", "actionfrom"=>$this->details["vtype"], "actionto"=>$data["vtype"]);
			}
		}
		
		if ($vduration = $this->SQLSafe($data["vduration"]))
		{	$fields[] = "vduration='$vduration'";
			if ($this->id && ($data["vduration"] != $this->details["vduration"]))
			{	$admin_actions[] = array("action"=>"Video Duration", "actionfrom"=>$this->details["vduration"], "actionto"=>$data["vduration"]);
			}
		}
		

		$live = ($data["live"] ? "1" : "0");
		$fields[] = "live=" . $live;
		if ($this->id && ($live != $this->details["live"]))
		{	$admin_actions[] = array("action"=>"Live?", "actionfrom"=>$this->details["live"], "actionto"=>$live, "actiontype"=>"boolean");
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE videos SET $set WHERE vid={$this->id}";
			} else
			{	$sql = "INSERT INTO videos SET $set";
			} 
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New video created";
						$this->RecordAdminAction(array("tablename"=>"videos", "tableid"=>$this->id, "area"=>"videos", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
				
				if ($record_changes)
				{	$base_parameters = array("tablename"=>"videos", "tableid"=>$this->id, "area"=>"videos");
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		}
		
		// Vimeo image save
		if($this->id)
		{
			if(isset($_POST['vimeoimage']) && $_POST['vimeoimage'] != '')
			{
				$uploaded = $this->UploadPhoto($_POST['vimeoimage']);
				
				if ($uploaded["successmessage"])
				{	$success[] = $uploaded["successmessage"];
					$this->RecordAdminAction(array("tablename"=>"videos", "tableid"=>$this->id, "area"=>"videos", "action"=>"New image uploaded"));
				}
				if ($uploaded["failmessage"])
				{	$fail[] = $uploaded["failmessage"];
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function UploadPhoto($file)
	{	$fail = array();
		$successmessage = "";
		
		$this->ReSizePhoto($file, $this->ImageFile(), $this->image_w, $this->image_h);
		$this->ReSizePhoto($file, $this->ThumbFile(), $this->thumb_w, $this->thumb_h);		
		$successmessage = "New photo uploaded";
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>$successmessage);

	} // end of fn UploadPhoto
	
	function InputForm()
	{	ob_start();
		$data = $this->details;
		if ($this->id)
		{	
			if ($_POST)
			{	// initialise details from this
				foreach ($_POST as $field=>$value)
				{	$data[$field ] = $value;
				}
			}
			
		} else
		{	$data = $_POST;
			
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id);
		
		$form->AddRawText("<p><label>Vimeo ID: http://www.vimeo.com/</label> <input type='text' name='vimeourl' size='20' /> <input type='button' name='vimeofetch' value='Fetch' /> <div class='ajaxloading'></div></p><div class='clear'></div>");
		
		$form->AddHiddenInput("vimeoimage", "");
		
		$form->AddTextInput("Title", "vtitle", $this->InputSafeString($data["vtitle"]), "", 255, 0);
		$form->AddTextArea("Description", "vdesc", $this->InputSafeString($data["vdesc"]), "tinymce", 0, 0, 20, 60);
		$form->AddSelect("Category", "catid", ($this->id ? $this->details["catid"] : ""), "", $this->GetCategoryList(), true, true);	
		
		if($this->HasImage())
		{
			$form->AddRawText("<p><label>Thumbnail:</label> <span style='float: left'><img src='". $this->ThumbSRC() ."' alt='' /></span></p><div class='clear'></div>");
		}
		
		$form->AddCheckBox("Visible on website?", "live", "", $data["live"]);
		
		$form->AddHiddenInput("vfile", $this->InputSafeString($data['vfile']));
		$form->AddHiddenInput("vduration", $this->InputSafeString($data['vduration']));
		$form->AddHiddenInput("vtype", ($this->id ? $this->InputSafeString($data['vtype']) : 'vimeo'));
		
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Video", "submit");
		if ($histlink = $this->DisplayHistoryLink("videos", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
						$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
						"delete this video</a></p>\n";
			}
			
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	function Filter($category = null)
	{
		$sql = "SELECT * FROM videos";
		
		if(!is_null($category) && $category != '')
		{
			$sql .= " WHERE catid = ". (int)$category;	
		}
		
		$sql .= " ORDER BY vtitle ASC";
		
		$results = array();
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$results[] = new AdminVideo($row);
			}
		}
		
		return $results;
	} // end of fn Filter
	
	function GetCategoryList()
	{	return parent::GetCategoryList(false);
	} // end of fn GetCategoryList
	
} // end of defn AdminInstructor
?>