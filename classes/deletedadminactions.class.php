<?php
class DeletedAdminActions extends AdminActions
{	
	function __construct($tablename = "")
	{	parent::__construct($tablename, 0);
	} // end of fn __construct
	
	function Get($tablename = "", $tableid = "")
	{	$this->changes = array();
		$sql = "SELECT * FROM adminactions WHERE tablename='" . $this->SQLSafe($tablename) . "' AND action='deleted' ORDER BY actiontime DESC";
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->changes[$this->KeyFromRow($row)] = $row;
			}
		}
		
	} // end of fn Get
	
	function DisplayTable()
	{	ob_start();
		echo "<div class='adminactions'>\n<p class='aaWarning'>Changes were only recorded from ", $this->CreatedDisplayDate(), "</p>";
		if ($this->changes)
		{	echo "<div class='aaTable'><table>\n<tr><th>When</th><th>By</th><th>Change</th><th>From</th><th></th></tr>\n";
			$adminusers = array();
			foreach ($this->changes as $change)
			{	if (!$adminusers[$change["auserid"]])
				{	$adminusers[$change["auserid"]] = new AdminUser((int)$change["auserid"]);
					//$this->VarDump($adminusers[$change["auserid"]]);
				}
				echo "<tr>\n<td class='aatdDate'>", date("d/m/y @H:i", strtotime($change["actiontime"])), "</td>\n<td class='aatdUser'><a href='useredit.php?userid=", $change["auserid"], "' target='_blank'>", $this->InputSafeString($adminusers[$change["auserid"]]->username), "</a></td>\n<td class='aatdDesc'>", $change["action"], "</td><td class='aatdChange'>", $this->DisplayChangeDetail($change, $change["actionfrom"]), "</td>\n<td class='aatdChange'></td>\n</tr>\n";
			}
			echo "</table></div>\n";
		} else
		{	echo "<p class='aaNoChanges'>No deletions recorded</p>\n";
		}
		echo "</div>\n";
		return ob_get_clean();
	} // end of fn DisplayTable
	
} // end of defn DeletedAdminActions
?>