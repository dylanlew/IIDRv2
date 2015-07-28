<?php
include_once('sitedef.php');

class FPTextListPage extends AdminFPTPage
{	var $fptexts = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function PagesConstruct()
	{	parent::PagesConstruct();
		if ($_GET['lchange'] && $this->user->CanUserAccess('technical'))
		{	$this->ChangeLanguageLive($_GET['lchange']);
		}
	} // end of fn PagesConstruct

	function ChangeLanguageLive($lcode = '')
	{	$lcode = $this->SQLSafe($lcode);
		$this->db->Query('UPDATE languages SET live=1-live WHERE langcode="' . $lcode . '"');
	} // end of fn ChangeLanguageLive
	
	function GetFPTextList()
	{	$labels = array();
		$sql = 'SELECT DISTINCT(fptlabel) FROM fptext ORDER BY fptlabel';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$labels[] = new AdminFPText($row['fptlabel']);
			}
		}
		
		return $labels;
	} // end of fn GetFPTextList
	
	function LangList()
	{	$langlist = array();
		if ($result = $this->db->Query('SELECT * FROM languages ORDER BY disporder'))
		{	while ($row = $this->db->FetchArray($result))
			{	$langlist[$row['langcode']] = $row;
			}
		}
		return $langlist;
	} // end of fn LangList
	
	function PagesContent()
	{	if ($langlist = $this->LangList())
		{	//$fptlist = $this->GetFPTextList();
			echo '<table>';
			if ($this->user->CanUserAccess('technical'))
			{	echo '<tr class="newlink"><th colspan="', count($langlist) + 2, '"><a href="fptcreate.php">Create new label</a></th></tr>';
			}
			echo '<tr><th>&nbsp;</th>';
			foreach ($langlist as $lcode=>$lname)
			{	echo '<th><a href="fptbylang.php?lang=', $lcode, '">', $lname['langname'], '</a><br />', $lname['live'] ? 'Live' : 'NOT Live';
				if ($this->user->CanUserAccess('technical') && ($lcode != $this->def_lang))
				{	echo '(<a href="fptlist.php?lchange=', $lcode, '">change this</a>)';
				}
				echo '</th>';
			}
			echo '<th>Actions</th></tr>';
			if ($fptlist = $this->GetFPTextList())
			{	foreach ($fptlist as $fpt)
				{	echo '<tr><td><a href="fptbylabel.php?name=', $fpt->name, '">', $fpt->name, '</a></td>';
					foreach ($langlist as $lcode=>$lname)
					{	echo '<td', $fpt->langused[$lcode] ? '' : ' class="fptempty"', '><a href="fptbylabellang.php?name=', $fpt->name, '&lang=', $lcode, '">', 
							$fpt->langused[$lcode] ? $this->InputSafeString($fpt->langused[$lcode]) : '&lt; Create &gt;', '</a></td>';
					}
					echo '<td><a href="fptbylabel.php?name=', $fpt->name, '">edit all</a>';
					if ($this->user->CanUserAccess('technical'))
					{	echo '&nbsp;|&nbsp;<a href="fptbylabel.php?name=', $fpt->name, '&delete=1">delete</a>';
					}
					echo '</td></tr>';
				}
			}
			echo '</table>';
		}
	} // end of fn PagesContent
	
} // end of defn FPTextListPage

$page = new FPTextListPage();
$page->Page();
?>