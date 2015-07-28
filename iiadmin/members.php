<?php
include_once('sitedef.php');

class MembersListPage extends AdminAKMembersPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$this->css[] = 'adminctry.css';
		$this->css[] = 'adminmembers.css';
		$this->js[] = 'admin_member_nonemail.js';
	} // end of fn AKMembersLoggedInConstruct
	
	function AKMembersBody()
	{	$members = $this->GetMembers();
		echo $this->FilterForm(), $this->OptionsList(count($members)), $this->ListMembers($members);
	} // end of fn AKMembersBody
	
	function FilterForm()
	{	ob_start();
		echo "<form class='akFilterForm' method='get' action='", $_SERVER["SCRIPT_NAME"], "'>\n<span>Male or Female</span>\n<select name='morf'>\n";
		foreach (array(""=>"all", "M"=>"Male", "F"=>"Female") as $option=>$text)
		{	echo "<option value='", $option, "'", $option == $_GET["morf"] ? " selected='selected'" : "", ">", $text, "</option>\n";
		}
		echo "</select>\n<span>Country</span>\n<select name='ctry' id='ctry_select' onchange='MemberFilterCountryList();'>\n<option value=''>all</option>";
		foreach ($this->GetCountries("shortname", true) as $option=>$text)
		{	echo "<option value='", $option, "'", $option == $_GET["ctry"] ? " selected='selected'" : "", ">", $this->InputSafeString($text), "</option>\n";
		}
		echo "</select><span>Name (part)</span><input type='text' name='name' value='", $this->InputSafeString($_GET["name"]), "' />\n<input type='submit' class='submit' value='Apply Filter' />\n<div class='clear'></div></form><div class='clear'></div>";
		return ob_get_clean();
	} // end of fn FilterForm
	
	function OptionsList($membercount = 0)
	{	ob_start();
	
		// build list of filter options
		$filter_applied = array();
		$link_paras = array();
		
		if ($_GET['morf'])
		{	switch ($_GET['morf'])
			{	case 'M': $filter_applied[] = '<strong>Male only</strong>'; break;
				case 'F': $filter_applied[] = '<strong>Female only</strong>'; break;
			}
			$link_paras[] = 'morf=' . $_GET['morf'];
		}

		if ($_GET['ctry'])
		{	$filter_applied[] = 'from ' . $this->GetCountry($_GET['ctry']);
			$link_paras[] = 'ctry=' . $_GET['ctry'];
		}

		if ($_GET['name'])
		{	$filter_applied[] = 'in name or email <strong>"' . $this->InputSafeString($_GET['name']) . '"</strong>';
			$link_paras[] = 'name=' . $_GET['name'];
		}
		
		echo '<div class="cblFilterInfo"><div class="cblFilterInfoFilter">filter applied: ';
		if ($filter_applied)
		{	echo implode('; ', $filter_applied);
			$link_para_string = '?' . implode('&', $link_paras);
		} else
		{	echo 'none';
		}
		echo ' ... ', $membercount, ' members found</div>';
		if ($membercount)
		{	echo '<ul><li><a href="members_csv.php', $link_para_string, '" target="_blank">download csv of these members</a></li>';
			if ($this->CanAdminUser('site-emails'))
			{
				echo '<li><a href="members_setmaillist.php', $link_para_string, '" target="_blank">send email to these members</a></li>';
			}
			echo '</ul>';
		}
		echo '<div class="clear"></div></div>';
		return ob_get_clean();
	} // end of fn OptionsList
	
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
		$sql .= ' GROUP BY students.userid ORDER BY students.surname, students.firstname LIMIT 0, 1000';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$row["country"] || $this->user->CanAccessCountry($row["country"]))
				{	$members[] = $row;
				}
			}
		}
		
		return $members;
	} // end of fn GetMembers
	
} // end of defn MembersListPage

$page = new MembersListPage();
$page->Page();
?>