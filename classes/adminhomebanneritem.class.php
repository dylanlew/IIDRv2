<?php
class AdminHomeBannerItem extends HomeBannerItem
{	var $admintitle = "";
	var $langused = array();
	
	function __construct($id = 0)
	{	parent::__construct($id);
		$this->GetLangUsed();
		$this->GetAdminTitle();
	} // fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	if (!$this->admintitle = $this->details["hbtitle"])
			{	if ($details = $this->GetDefaultDetails())
				{	$this->admintitle = $details["hbtitle"] . " [{$this->language}]";
				}
			}
		}
	} // end of fn GetAdminTitle
	
	function AssignHBLanguage()
	{	if (!$this->language = $_GET["lang"])
		{	$this->language = $this->def_lang;
		}
	} // end of fn AssignHBLanguage
	
	function AddDetailsForDefaultLang(){}
	
	function GetLangUsed()
	{	$this->langused = array();
		if ($this->id)
		{	if ($result = $this->db->Query("SELECT lang FROM homebanner_lang WHERE hbid=$this->id"))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->langused[$row["lang"]] = true;
				}
			}
		}
	} // end of fn GetLangUsed
	
	function GetDefaultDetails()
	{	$sql = "SELECT * FROM homebanner_lang WHERE hbid=$this->id AND lang='{$this->def_lang}'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row;
			}
		}
		return array();
	} // end of fn GetDefaultDetails
	
	function CanDelete()
	{	return $this->id && !$this->courses && $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query("DELETE FROM homebanner WHERE hbid=$this->id"))
			{	if ($this->db->AffectedRows())
				{	$this->db->Query("DELETE FROM homebanner_lang WHERE hbid=$this->id");
					$this->db->Query("DELETE FROM homebanner_speak WHERE hbid=$this->id");
					@unlink($this->ImageFile());
					@unlink($this->ThumbFile());
					$this->RecordAdminAction(array("tablename"=>"homebanner", "tableid"=>$this->id, "area"=>"homebanner", "action"=>"deleted"));
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete

	function Save($data = array(), $imagefile = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$l_fields = array();
		$admin_actions = array();
		
		if ($hbtitle = $this->SQLSafe($data["hbtitle"]))
		{	$l_fields[] = "hbtitle='$hbtitle'";
			if ($this->id && ($data["hbtitle"] != $this->details["hbtitle"]))
			{	$admin_actions[] = array("action"=>"Title ({$this->language})", "actionfrom"=>$this->details["hbtitle"], "actionto"=>$data["hbtitle"]);
			}
		} else
		{	$fail[] = "title missing";
		}
		
		$bgcolour = $this->SQLSafe($data["bgcolour"]);
		$fields[] = "bgcolour='$bgcolour'";
		if ($this->id && ($data["bgcolour"] != $this->details["bgcolour"]))
		{	$admin_actions[] = array("action"=>"BG Colour", "actionfrom"=>$this->details["bgcolour"], "actionto"=>$data["bgcolour"]);
		}
		
		$description = $this->SQLSafe($data["description"]);
		$l_fields[] = "description='$description'";
		if ($this->id && ($data["description"] != $this->details["description"]))
		{	$admin_actions[] = array("action"=>"Description ({$this->language})", "actionfrom"=>$this->details["description"], "actionto"=>$data["description"]);
		}
		
		$hblink = $this->SQLSafe($data["hblink"]);
		$fields[] = "hblink='$hblink'";
		if ($this->id && ($data["hblink"] != $this->details["hblink"]))
		{	$admin_actions[] = array("action"=>"Link", "actionfrom"=>$this->details["hblink"], "actionto"=>$data["hblink"]);
		}
		
		$hborder = (int)$data["hborder"];
		$fields[] = "hborder=" . $hborder;
		if ($this->id && ($hborder != $this->details["hborder"]))
		{	$admin_actions[] = array("action"=>"List order", "actionfrom"=>$this->details["hborder"], "actionto"=>$hborder);
		}

		$live = ($data["live"] ? "1" : "0");
		$fields[] = "live=" . $live;
		if ($this->id && ($live != $this->details["live"]))
		{	$admin_actions[] = array("action"=>"Live?", "actionfrom"=>$this->details["live"], "actionto"=>$live, "actiontype"=>"boolean");
		}
		
		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE homebanner SET $set WHERE hbid={$this->id}";
			} else
			{	$sql = "INSERT INTO homebanner SET $set";
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = "Changes saved";
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New course content created";
						$this->RecordAdminAction(array("tablename"=>"homebanner", "tableid"=>$this->id, "area"=>"homebanner", "action"=>"created"));
					}
					$this->Get($this->id);
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
				
				if ($this->id)
				{	
					if ($set = implode(", ", $l_fields))
					{	if ($this->langused[$this->language])
						{	$sql = "UPDATE homebanner_lang SET $set WHERE hbid=$this->id AND lang='$this->language'";
						} else
						{	$sql = "INSERT INTO homebanner_lang SET $set, hbid=$this->id, lang='$this->language'";
						}
						if ($result = $this->db->Query($sql))
						{	if ($this->db->AffectedRows())
							{	$success[] = "text changes saved";
								$record_changes = true;
							}
						}
					}
					$this->Get($this->id);
					$this->GetLangUsed();
				}
				
				if ($record_changes)
				{	$base_parameters = array("tablename"=>"homebanner", "tableid"=>$this->id, "area"=>"homebanner");
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}//else echo "<p>", $this->db->Error(), "</p>\n";
		}
		
		if ($this->id)
		{	if ($imagefile["size"])
			{	$uploaded = $this->UploadPhoto($imagefile);
				if ($uploaded["successmessage"])
				{	$success[] = $uploaded["successmessage"];
					$this->RecordAdminAction(array("tablename"=>"homebanner", "tableid"=>$this->id, "area"=>"homebanner", "action"=>"New image uploaded"));
				}
				if ($uploaded["failmessage"])
				{	$fail[] = $uploaded["failmessage"];
				}
			} else
			{	if ($data["delposter"])
				{	@unlink($this->ImageFile());
					@unlink($this->ThumbFile());
					$success[] = "image deleted";
					$this->RecordAdminAction(array("tablename"=>"homebanner", "tableid"=>$this->id, "area"=>"homebanner", "action"=>"Image deleted"));
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function UploadPhoto($file)
	{	$fail = array();
		$successmessage = "";

		if ($file["size"])
		{	if ((!stristr($file["type"], "jpeg") && !stristr($file["type"], "jpg")) 
								|| $file["error"])
			{	$fail[] = "File type invalid (jpeg only)";
			} else
			{	
				$this->ReSizePhoto($file["tmp_name"], $this->ImageFile(), $this->image_w, $this->image_h);
				$this->ReSizePhoto($file["tmp_name"], $this->ThumbFile(), $this->thumb_w, $this->thumb_h);
				unlink($file["tmp_name"]);
				
				$successmessage = "New image uploaded";
			}
		} else
		{	$fail[] = "image not uploaded";
		}
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>$successmessage);

	} // end of fn UploadPhoto
	
	function InputForm()
	{	
		ob_start();
		$data = $this->details;
		if ($this->id)
		{	
			if (!$this->langused[$this->language])
			{	if ($_POST)
				{	// initialise details from this
					foreach ($_POST as $field=>$value)
					{	$data[$field] = $value;
					}
				}
			}
			$speaks = $this->ShowsInLanguages();
		} else
		{	$data = $_POST;
			if (!$data)
			{	$data = array("live"=>1);
			}
			$speaks = array();
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&lang=" . $this->language, "course_edit");
		$form->AddTextInput("Title", "hbtitle", $this->InputSafeString($data["hbtitle"]), "long", 255, 1);
		//$form->AddTextInput("Tagline", "hbtagline", $this->InputSafeString($data["hbtagline"]), "long", 255);
		$form->AddTextInput("Link (relative to homepage or full address) for all languages", "hblink", $this->InputSafeString($data["hblink"]), "long", 255);
		$form->AddCheckBox("Show live", "live", "1", $data["live"]);
		$form->AddTextInput("Display order", "hborder", (int)$data["hborder"], "short", 5);
		$form->AddTextInput("Background colour", "bgcolour", $this->InputSafeString($data["bgcolour"]), "colourpicker", 10);
		$form->AddTextArea("Description", "description", stripslashes($data["description"]), "tinymce", 0, 0, 10, 60);
		$form->AddFileUpload("Image (thumbnail will be created for you)", "imagefile");
		if (file_exists($this->ThumbFile()))
		{	$form->AddRawText("<p><label>Current image</label><img src='" . $this->ThumbSRC() . "' /><br /></p>");
			$form->AddCheckBox("Delete this", "delposter");
		}
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create New Item", "submit");
		if ($histlink = $this->DisplayHistoryLink("homebanner", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", $_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", "delete this item</a></p>\n";
			}
			$this->AdminEditLangList();
		}
		echo "<script>$(function() {
	$('.colourpicker').jPicker({window:{position:{x:'30',y:'center'}}, images:{clientPath: '../img/jpicker/'}});
});</script>
";
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn AdminHomeBannerItem
?>