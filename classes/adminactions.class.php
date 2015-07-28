<?php
class AdminActions extends Base
{	var $aa_created = "2011-10-28 09:10:00";
	var $changes = array();
	var $area = "";
	var $tableid = "";

	function __construct($tablename = "", $tableid = "")
	{	parent::__construct();
		$this->area = $tablename;
		$this->tableid = $tableid;
		$this->Get($tablename, $tableid);
	} // end of fn __construct
	
	function Get($tablename = "", $tableid = "")
	{	$this->changes = array();
		$sql = "SELECT * FROM adminactions WHERE tablename='" . $this->SQLSafe($tablename) . "' AND tableid='" . $this->SQLSafe($tableid) . "' ORDER BY actiontime DESC";
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->changes[$this->KeyFromRow($row)] = $row;
				$this->area = $row["area"];
			}
		}
		
		if (($deleted = $this->GetDeletedChildren($tablename, $tableid)) && is_array($deleted))
		{	$this->changes = array_merge($this->changes, $deleted);
		}
		
		if (($extra = $this->GetExtra($tablename, $tableid)) && is_array($extra))
		{	$this->changes = array_merge($this->changes, $extra);
		}
		
		krsort($this->changes);
		
	} // end of fn Get
	
	function KeyFromRow($row = array())
	{	return $row["actiontime"] . "_" . str_pad($row["aaid"], 10, "0", STR_PAD_LEFT);
	} // end of fn KeyFromRow
	
	function GetDeletedChildren($tablename = "", $tableid = "")
	{	$deleted = array();
		$sql = "SELECT * FROM adminactions WHERE deleteparenttable='" . $this->SQLSafe($tablename) . "' AND deleteparentid='" . $this->SQLSafe($tableid) . "' AND actiontype='deleted' ORDER BY actiontime DESC";
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$row["action"] = "{{$row["tablename"]}, {$row["tableid"]}}<br />{$row["action"]}";
				$deleted[$this->KeyFromRow($row)] = $row;
			}
		}
		
		return $deleted;
	} // end of fn GetDeletedChildren
	
	function GetExtra($tablename = "", $tableid = "")
	{	$extra = array();
		
		$sql = "SELECT * FROM adminactionschildren WHERE parenttable='" . $this->SQLSafe($tablename) . "'";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$ids_sql = "SELECT {$row["childidname"]} FROM {$row["childtable"]} WHERE {$row["parentidname"]}='" . $this->SQLSafe($tableid) . "'";
				if ($ids_result = $this->db->Query($ids_sql))
				{	while ($ids_row = $this->db->FetchArray($ids_result))
					{	$child_aa = new AdminActions($row["childtable"], $ids_row[$row["childidname"]]);
						if ($child_aa->changes)
						{	foreach ($child_aa->changes as $key=>$child_changes)
							{	$extra[$key] = $child_changes;
								$extra[$key]["action"] = "{{$row["childtable"]}, {$ids_row[$row["childidname"]]}}<br />{$extra[$key]["action"]}";
							}
						}
					}
				}
				
			}
		}
		
		return $extra;
	} // end of fn GetExtra
	
	function DisplayTable()
	{	ob_start();
		echo "<div class='adminactions'>\n<p class='aaWarning'>Changes were only recorded from ", $this->CreatedDisplayDate(), "</p>\n<p>", $this->area, " ... id:", $this->tableid, "</p>";
		if ($this->changes)
		{	echo "<div class='aaTable'><table>\n<tr><th>When</th><th>By</th><th>Change</th><th>From</th><th>To</th></tr>\n";
			$adminusers = array();
			foreach ($this->changes as $change)
			{	if (!$adminusers[$change["auserid"]])
				{	$adminusers[$change["auserid"]] = new AdminUser((int)$change["auserid"]);
					//$this->VarDump($adminusers[$change["auserid"]]);
				}
				if ($change["actiontime"] !== $actiontime)
				{	if ($actiontime)
					{	echo $this->SpacerRow();
					}
					$actiontime = $change["actiontime"];
				}
				echo "<tr>\n<td class='aatdDate'>", date("d/m/y @H:i", strtotime($change["actiontime"])), "</td>\n<td class='aatdUser'><a href='useredit.php?userid=", $change["auserid"], "' target='_blank'>", $this->InputSafeString($adminusers[$change["auserid"]]->username), "</a></td>\n<td class='aatdDesc'>", $change["action"], "</td><td class='aatdChange'>", $this->DisplayChangeDetail($change, $change["actionfrom"]), "</td>\n<td class='aatdChange'>";
				if ($change["actiontype"] != "deleted")
				{	echo $this->DisplayChangeDetail($change, $change["actionto"]);
				}
				echo "</td>\n</tr>\n";
			}
			echo "</table></div>\n";
		} else
		{	echo "<p class='aaNoChanges'>No changes recorded</p>\n";
		}
		echo "</div>\n";
		return ob_get_clean();
	} // end of fn DisplayTable
	
	function DisplayChangeDetail($change = array(), $value = "")
	{	ob_start();
		$text = "";
		switch ($change["actiontype"])
		{	case "boolean": $text = $value ? "Yes" : "No";
							break;
			case "date": 	if ((int)$value)
							{	$text = date("d/m/y", strtotime($value));
							}
							break;
			case "datetime": if ((int)$value)
							{	$text = date("d/m/y @H:i", strtotime($value));
							}
							break;
			case "html": $text = stripslashes($value);
							break;
			case "deleted": $text =$this->DisplayHistoryLink($change["tablename"], $change["tableid"]);
							if ($change["linkmask"])
							{	$text .= $change["linkmask"];
							}
							break;
			case "link": 	if ($value)
							{	if ($link = str_replace("{linkid}", $value, $change["linkmask"]))
								{	$text = "<a href='$link' target='_blank'>$link</a>";
								} else
								{	$text = $this->InputSafeString($value);
								}
							}
							break;
			default: $text = $this->InputSafeString($value);
		}
		if ($container = (strlen($text) > 180))
		{	echo "<div class='aaValueDiv'>";
		}
		echo $text;
		if ($container)
		{	echo "</div>";
		}
		
		return ob_get_clean();
	} // end of fn DisplayChangeDetail
	
	function SpacerRow()
	{	ob_start();
		echo "<tr class='aaSpacer'><td colspan='5'></a></tr>";
		return ob_get_clean();
	} // end of fn SpacerRow
	
	function CreatedDisplayDate()
	{	return date("d-M-Y H:i", strtotime($this->aa_created));
	} // end of fn CreatedDisplayDate
	
} // end of defn AdminActions
?>