<?php
include_once('sitedef.php');

class DelOptionsListPage extends AdminDelOptionsPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function DelOptionsBody()
	{	
		echo '<table><tr class="newlink"><th colspan="7"><a href="deliveryedit.php">Create new delivery option</a></th></tr><tr><th>Option</th><th>Description</th><th>Region</th><th>Price</th><th>List order</th><th>Live</th><th>Actions</th></tr>';
		$regions = array();
		foreach ($this->GetOptions() as $deloption_row)
		{	$deloption = new AdminDeliveryOption($deloption_row);
			if (!isset($regions[$deloption->details['region']]))
			{	$regions[$deloption->details['region']] = $deloption->GetRegionName();
			}
			echo '<tr class="stripe', $i++ % 2, '"><td>', $this->InputSafeString($deloption->details['title']), '</td><td>', $this->InputSafeString($deloption->details['description']), '</td><td>', $this->InputSafeString($regions[$deloption->details['region']]), '</td><td>', number_format($deloption->details['price'], 2), '</td><td>', (int)$deloption->details['listorder'], '</td><td>', $deloption->details['live'] ? 'Live' : '', '</td><td><a href="deliveryedit.php?id=', $deloption->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('delregions', $deloption->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($deloption->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="deliveryedit.php?id=', $deloption->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	} // end of fn DelOptionsBody
	
	function GetOptions()
	{	$options = array();
		$sql = 'SELECT deliveryoptions.* FROM deliveryoptions LEFT JOIN delregions on deliveryoptions.region=delregions.drid ORDER BY delregions.drname, deliveryoptions.listorder, deliveryoptions.id';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$options[] = $row;
			}
		}
		return $options;
	} // end of fn GetOptions
	
} // end of defn DelOptionsListPage

$page = new DelOptionsListPage();
$page->Page();
?>