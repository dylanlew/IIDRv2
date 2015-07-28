<?php
include_once("sitedef.php");

class MembersCSV extends AdminAKMembersPage
{	var $course;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersBody()
	{	
		$nl = "\n";
		header('Pragma: ');
		header('Cache-Control: ');
		header('Content-Type: application/csv;charset=UTF-8');
		header('Content-Disposition: attachment; filename="iidr_members.csv"');
		echo 'userid,email,title,firstname,surname,phone,address,city,postcode,country,m/f,date of birth,registered,education,profession,how heard, newsletter?', $nl;
		foreach ($this->GetMembers() as $memberrow)
		{	
			$phones = array();
			if ($memberrow['phone'])
			{	$phones[] = stripslashes($memberrow['phone']);
			}
			if ($memberrow['phone2'])
			{	$phones[] = stripslashes($memberrow['phone2']);
			}
			$address = array();
			for ($addcount = 1; $addcount <= 3; $addcount++)
			{	if ($memberrow['address' . $addcount])
				{	$address[] = $this->CSVSafeString($memberrow['address' . $addcount]);
				}
				echo $addcount;
			}
			echo $memberrow['userid'], ',"', $this->CSVSafeString($memberrow['username']), '","', $this->CSVSafeString($memberrow['title']), '","', $this->CSVSafeString($memberrow['firstname']), '","', $this->CSVSafeString($memberrow['surname']), '","', implode(', ', $phones), '","', implode($nl, $address), '","', $this->CSVSafeString($memberrow['city']), '","', $this->CSVSafeString($memberrow['postcode']), '","', $this->GetCountry($memberrow['country']), '","', $memberrow['morf'], '",', date('d/m/Y', strtotime($memberrow['dob'])), ',"', date('d/m/Y', strtotime($memberrow['regdate'])), '","', $this->CSVSafeString($memberrow['education']), '","', $this->CSVSafeString($memberrow['profession']), '","', $this->CSVSafeString($memberrow['howheard']), '","', $memberrow['newsletter'] ? 'Yes' : 'No', '"', $nl;
		}
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
	
} // end of defn MembersCSV

$page = new MembersCSV();
$page->AdminBodyMain();
?>