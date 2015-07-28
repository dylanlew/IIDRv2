<?php
include_once('sitedef.php');

class CountriesListPage extends CountriesPage
{	
	function __construct()
	{	parent::__construct();
		$this->css[] = 'adminctry.css';
	} //  end of fn __construct
	
	function CountriesLoggedInConstruct()
	{	
	} // end of fn CountriesLoggedInConstruct
	
	function CountriesBodyMain()
	{	$this->FilterForm();
		$this->CtryList();
	} // end of fn CountriesBodyMain
	
	function ContinentList()
	{	$continents = array();
		if ($result = $this->db->Query('SELECT * FROM continents ORDER BY contorder, dispname'))
		{	while ($row = $this->db->FetchArray($result))
			{	$continents[$row['continent']] = $row['dispname'];
			}
		}
		return $continents;
	} // end of fn ContinentList
	
	function FilterForm()
	{	class_exists('Form');
		$contselect = new FormLineSelect('', 'continent', $_GET['continent'], '', $this->ContinentList(), true, 0, '', '-- all --');
		echo '<form id="orderSelectForm" action="', $_SERVER['SCRIPT_NAME'], '" method="get"><label for="continent" class="from">Continent</label>';
		$contselect->OutputField();
		echo '<input type="submit" class="submit" value="Get" /><div class="clear"></div></form>';
	} // end of fn FilterForm
	
	function CtryList()
	{	echo '<table><tr><th>Country</th><th>Code</th><th>Region</th><th>Cont.</th><th>Actions</th></tr>';
		$regions = array();
		foreach ($this->Countries() as $ctry_row)
		{	$ctry = new AdminCountry($ctry_row);
			if (!isset($regions[$ctry->details['region']]))
			{	$regions[$ctry->details['region']] = $ctry->GetRegionName();
			}
		
			echo '<tr class="stripe', $i++ % 2, '" id="tr', $ctry->code, '"><td>', $this->InputSafeString($ctry->details['shortname']), '</td><td>', $ctry->details['shortcode'], '</td><td>',  $this->InputSafeString($regions[$ctry->details['region']]), '</td><td>', $ctry->details['continent'], '</td><td><a href="ctryedit.php?ctry=', $ctry->code, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('countries', $ctry->code))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($ctry->CanDelete($ctry->details['toplist']))
			{	echo '&nbsp;|&nbsp;<a href="ctryedit.php?ctry=', $ctry->code, '&delete=1">delete</a>';
			}
			
			echo '</td></tr>';
		}
		echo '</table>';
		if ($this->CanAdminUser('admin'))
		{	echo '<p><a href="ctryedit.php">Create new country</a></p>';
		}
	} // end of fn CtryList
	
	function Countries()
	{	$countries = array();
		$where = array();
		if ($_GET['continent'])
		{	$where[] = 'continent="' . $_GET['continent'] . '"';
		}
		
		$sql = 'SELECT countries.* FROM countries';
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY shortname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[] = $row;
			}
		}
		
		return $countries;
	} // end of fn Countries
	
} // end of defn CountriesListPage

$page = new CountriesListPage();
$page->Page();
?>