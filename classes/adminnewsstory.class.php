<?php
class AdminNewsStory extends NewsStory
{	var $admintitle = "";
	var $langused = array();

	function __construct($id = 0)
	{	parent::__construct($id);
		$this->GetLangUsed();
		$this->GetAdminTitle();
	} //  end of fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	if (!$this->admintitle = $this->details["headline"])
			{	if ($details = $this->GetDefaultDetails())
				{	$this->admintitle = $details["headline"] . " [{$this->language}]";
				}
			}
		}
	} // end of fn GetAdminTitle
	
	function GetDefaultDetails()
	{	$sql = "SELECT * FROM news_lang WHERE newsid=$this->id AND lang='{$this->def_lang}'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row;
			}
		}
		return array();
	} // end of fn GetDefaultDetails
	
	function AssignNewsLanguage()
	{	if (!$this->language = $_GET["lang"])
		{	$this->language = $this->def_lang;
		}
	} // end of fn AssignNewsLanguage
	
	function AddDetailsForDefaultLang(){}
	
	function GetLangUsed()
	{	$this->langused = array();
		if ($this->id)
		{	if ($result = $this->db->Query("SELECT lang FROM news_lang WHERE newsid=$this->id"))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->langused[$row["lang"]] = true;
				}
			}
		}
	} // end of fn GetLangUsed
	
	function Save($data = array())
	{	
		$fail = array();
		$success = array();
		$fields = array();
		$l_fields = array();
		$admin_actions = array();
		
		if ($headline = $this->SQLSafe($data["headline"]))
		{	$l_fields[] = "headline='$headline'";
			if ($this->id && ($data["headline"] != $this->details["headline"]))
			{	$admin_actions[] = array("action"=>"Headline ({$this->language})", "actionfrom"=>$this->details["headline"], "actionto"=>$data["headline"]);
			}
		} else
		{	$fail[] = "headline cannot be empty";
		}
		
		if ($newstext = $this->SQLSafe($data["newstext"]))
		{	$l_fields[] = "newstext='$newstext'";
			if ($this->id && ($data["newstext"] != $this->details["newstext"]))
			{	$admin_actions[] = array("action"=>"News text ({$this->language})", "actionfrom"=>$this->details["newstext"], "actionto"=>$data["newstext"], "actiontype"=>"html");
			}
		} else
		{	$fail[] = "story cannot be empty";
		}
		
		if ($this->id)
		{	// then allow update of timestamp
			if (($y = (int)$data["ydate"]) && ($m = (int)$data["mdate"]) && ($d = (int)$data["ddate"]))
			{	
				$submitted = $this->datefn->SQLDateTime(mktime(substr($data["time"], 0, 2), substr($data["time"], 3, 2), 0, $m, $d, $y));
				$fields[] = "submitted='$submitted'";
				if ($this->id && ($submitted != $this->details["submitted"]))
				{	$admin_actions[] = array("action"=>"Story posted", "actionfrom"=>$this->details["submitted"], "actionto"=>$submitted, "actiontype"=>"datetime");
				}
			}
			
			// if editing allow to put live
			$live = ($data["live"] ? "1" : "0");
			$fields[] = "live=" . $live;
			if ($this->id && ($live != (int)$this->details["live"]))
			{	$admin_actions[] = array("action"=>"Live?", "actionfrom"=>$this->details["live"], "actionto"=>$live, "actiontype"=>"boolean");
			}
			
		} else
		{	// new story so create timestamp
			$submitted = date("Y-m-d H:i:00");
			$fields[] = "submitted='$submitted'";
		}
		
		if ($this->id || !$fail)
		{	
			$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = "UPDATE news SET $set WHERE newsid=" . $this->id;
			} else
			{	$sql = "INSERT INTO news SET $set";
			}
			
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$this->Get($this->id);
					} else
					{	if ($id = $this->db->InsertID())
						{	$this->Get($id);
							$success[] = "new story added";
							$this->RecordAdminAction(array("tablename"=>"news", "tableid"=>$this->id, "area"=>"news", "action"=>"created"));
						} else
						{	$fail[] = "insert failed";
						}
					}
				}
				
				if ($this->id)
				{	
					if ($set = implode(", ", $l_fields))
					{	if ($this->langused[$this->language])
						{	$sql = "UPDATE news_lang SET $set WHERE newsid=$this->id AND lang='$this->language'";
						} else
						{	$sql = "INSERT INTO news_lang SET $set, newsid=$this->id, lang='$this->language'";
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
				{	$base_parameters = array("tablename"=>"news", "tableid"=>$this->id, "area"=>"news");
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
			
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function Delete()
	{	if ($this->CanDelete())
		{	$sql = "DELETE FROM news WHERE newsid=" . $this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->db->Query("DELETE FROM news_lang WHERE newsid=$this->id");
					$this->RecordAdminAction(array("tablename"=>"news", "tableid"=>$this->id, "area"=>"news", "action"=>"deleted"));
					$this->Reset();
					return true;
				}
			}
		}
	} // end of fn Delete
	
	function CanDelete()
	{	return $this->id && $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
	function InputForm()
	{	 
		$data = $this->details;
		if ($this->id)
		{	
			if (!$this->langused[$this->language])
			{	if ($_POST)
				{	// initialise details from this
					foreach ($_POST as $field=>$value)
					{	$data[$field ] = $value;
					}
					if (($y = (int)$data["ydate"]) && ($m = (int)$data["mdate"]) && ($d = (int)$data["ddate"]))
					{	
						$data["submitted"] = $this->datefn->SQLDateTime(mktime(substr($data["time"], 0, 2), substr($data["time"], 3, 2), 0, $m, $d, $y));
					}
				}
			}
		} else
		{	$data = $_POST;
		}

		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&lang=" . $this->language, "newsform");
		$form->AddTextInput("Headline", "headline", $this->InputSafeString($data["headline"]));
		$form->AddTextArea("Story", "newstext", stripslashes($data["newstext"]), "tinymce", 0, 0, 10, 60);
	//	$form->AddRawText("<p><label></label><a href='#' onclick='javascript:window.open(\"newsimagelist.php\", \"newsimages\", \"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=550\"); return false;'>view available images</a></p>");
	//	$form->AddRawText("<p><label></label><a href='#' onclick='javascript:window.open(\"newsdoclist.php\", \"newsdocs\", \"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=550\"); return false;'>view available downloadable documents</a></p>");
		if ($this->id)
		{	$years = array($y = date("Y"));
			$startyear = date("Y", strtotime($data["submitted"]));
			while ($y > $startyear)
			{	$years[] = --$y;
			}
			$form->AddDateInput("Story Date", "date", $data["submitted"], $years);
			$form->AddTextInput("Time", "time", substr($data["submitted"], 11, 5));
			$form->AddCheckBox("Live?", "live", 1, $data["live"]);
		}
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Create Story", "submit");
		if ($histlink = $this->DisplayHistoryLink("news", $this->id))
		{	echo "<p>", $histlink, "</p>";
		}
		if ($this->id)
		{	echo "<h3>Editing ... ", $this->InputSafeString($this->admintitle), "</h3>";
			if ($this->CanDelete())
			{	echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", 
					$_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", 
					"delete this story</a></p>\n";
			}
			$this->AdminEditLangList();
		}
		$form->Output();
	} // end of fn InputForm
	
} // end of defn NewsStory
?>