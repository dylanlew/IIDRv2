<?php
class SiteEmails extends Base
{	var $emails = array();
	
	function __construct()
	{	parent::__construct();
		$this->Get();
	} // end of fn __construct
	
	function Reset()
	{	$this->emails = array();
	} // end of fn Reset
	
	function Get($liveonly  = false)
	{	$this->Reset();
		$where = array();
		$sql = "SELECT * FROM siteemails";
		if ($wstr = implode(" AND ", $where))
		{	$sql .= " WHERE $wstr";
		}
		$sql .= " ORDER BY createtime DESC";
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->emails[$row["emid"]] = $this->AssignEmail($row);
			}
		}
	} // end of fn Get
	
	function AssignEmail($email = array())
	{	return new SiteEmail($email);
	} // end of fn AssignEmail
	
	function ListEmails()
	{	
		echo "<table>\n<tr class='newlink'><th colspan='6'><a href='siteemail.php'>create new email</a></th></tr>\n<tr><th>Email</th><th>Subject</th><th>Sends from</th><th>Sent to</th><th>Created</th><th>Actions</th></tr>\n";
		if (is_array($_SESSION["adminmailist"]))
		{	$emailstosendto = count($_SESSION["adminmailist"]);
		}
		foreach ($this->emails as $email)
		{	echo "<tr>\n<td>", $this->InputSafeString($email->details["emaildesc"]), "</td>\n<td>", $this->InputSafeString($email->details["subject"]), "</td>\n<td>", $email->SendFromEmail(), "</td>\n<td>", $email->sentCount, "</td>\n<td>", date("d/m/Y", strtotime($email->details["createtime"])), "</td>\n<td><a href='siteemail.php?id=", $email->id, "'>edit</a>";
			if ($email->CanDelete())
			{	echo "&nbsp;|&nbsp;<a href='siteemail.php?id=", $email->id, "&del=1'>delete</a>";
			}
			echo "&nbsp;|&nbsp;<a href='siteemail_display.php?id=", $email->id, "&disp=full' target='_blank'>preview</a>&nbsp;|&nbsp;<a href='siteemailsend.php?id=", $email->id, "'>send to ", (int)$emailstosendto, "</a></td>\n</tr>\n";
		}
		echo "</table>\n";
	} // end of fn ListEmails
	
} // end of defn SiteEmails
?>