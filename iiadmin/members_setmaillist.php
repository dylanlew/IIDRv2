<?php
include_once("sitedef.php");

class MembersSetMailList extends AdminAKMembersPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("members_setmaillist.php", "Setting email list");
		$this->bodyOnLoadJS[] = "window.location='" . CIT_FULLLINK . "suadmin/siteemails.php';";
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	echo "<p>you will be redirected to the emails ready to send when the list has been built ...</p>";
		$maillist = array();
		foreach ($this->GetMembers() as $memberrow)
		{	if ($this->ValidEmail($memberrow["username"]))
			$maillist[$memberrow["userid"]] = array(
									"userid"=>$memberrow["userid"], 
									"email"=>$memberrow["username"], 
									"name"=>trim($memberrow["firstname"] . " " . $memberrow["surname"]));
		}
		$_SESSION["adminmailist"] = $maillist;
		//$this->VarDump($maillist);
	} // end of fn AKMembersBody
	
	function GetMembers()
	{	$members = array();
		$where = array();
		$tables = array('students');
		
		if ($_GET['morf'])
		{	$where[] = 'students.morf="' . $this->SQLSafe($_GET['morf']) . '"';
		}
	
		if ($_GET['ctry'])
		{	$where[] = 'students.country="' . $this->SQLSafe($_GET['ctry']) . '"';
		}
		
		if ($name = $this->SQLSafe($_GET['name']))
		{	$where[] = '(CONCAT(students.firstname, " ", students.surname, "|", students.username) LIKE "%' . $name . '%")';
		}
		
		$sql = 'SELECT students.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' GROUP BY students.userid ORDER BY students.surname, students.firstname';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$row["country"] || $this->user->CanAccessCountry($row["country"]))
				{	$members[] = $row;
				} //else echo $row["country"], "<br />";
			}
		}
		
		return $members;
	} // end of fn GetMembers
	
} // end of defn MembersSetMailList

$page = new MembersSetMailList();
$page->Page();
?>