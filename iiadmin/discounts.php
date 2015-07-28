<?php
include_once("sitedef.php");

class DiscountsListPage extends AdminDiscountPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function DiscountBody()
	{	//$this->FilterForm();
		$this->DiscountsList();
	} // end of fn DiscountBody
	
	function FilterForm()
	{	class_exists("Form");
	//	$startfield = new FormLineDate("", "start", $this->startdate, $this->datefn->GetYearList(date("Y")+5, -10), 0, 0, 1);
	//	$endfield = new FormLineDate("", "end", $this->enddate, $this->datefn->GetYearList(date("Y")+5, -10), 0, 0, 1);
		echo "<form id='filterSelectForm' action='", $_SERVER["SCRIPT_NAME"], 
					"' method='get'>\n";
	//	echo "<label for='mstart' class='from'>From</label>";
	//	$startfield->OutputField();
	//	echo "<label for='dend' class='from'>to</label>";
	//	$endfield->OutputField();
		if ($countries = $this->GetDiscountCountries())
		{	$ctryselect = new FormLineSelect("", "ctry", $_GET["ctry"], "", $countries, true);
			echo "<label for='ctry' class='from'>in</label>";
			$ctryselect->OutputField();
		}
		echo "<input type='submit' class='submit' value='Get' /><div class='clear'></div></form>";
	} // end of fn FilterForm
	
	function DiscountsList()
	{	echo '<table><tr class="newlink"><th colspan="9"><a href="discountedit.php">Create new discount</a></th></tr><tr><th>Code</th><th>Description</th><th>For</th><th>Discount</th><th>Used</th><th>Dates</th><th>Once per user</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetDiscounts() as $discount_row)
		{	$discount = new AdminDiscountCode($discount_row);
			
			echo '<tr class="stripe', $i++ % 2, '"><td>', $this->InputSafeString($discount->details['disccode']), '</td><td>', $this->InputSafeString($discount->details['discdesc']), '</td><td>', $discount->ProductTypeDetails(false, false, false), '</td><td>';
			if ($discount->details['discpc'])
			{	echo round($discount->details['discpc'], 2), '%';
			} else
			{	echo '&pound;', number_format($discount->details['discamount'], 2);
			}
			echo '</td><td>', (int)$discount->details['usecount'], ' / ', $discount->details['uselimit'] ? (int)$discount->details['uselimit'] : 'unlimited', '</td><td>';
			if ((int)$discount->details['startdate'])
			{	echo 'from ', date('d/m/Y', strtotime($discount->details['startdate']));
			}
			if ((int)$discount->details['enddate'])
			{	echo (int)$discount->details['startdate'] ? ' to ' : 'up to', date('d/m/Y', strtotime($discount->details['enddate']));
			}
			echo '</td><td>', $discount->details['oneuseperuser'] ? 'one-use' : '', '</td><td>', $discount->details['live'] ? 'Live' : '', '</td><td><a href="discountedit.php?id=', $discount->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('discounts', $discount->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($discount->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="discountedit.php?id=', $discount->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	} // end of fn DiscountsList
	
	function GetDiscounts()
	{	$discounts = array();
		$where = array();
		$sql = "SELECT * FROM discountcodes";
		if ($where)
		{	$sql .= " WHERE " . implode(" AND ", $where);
		}
		$sql .= " ORDER BY disccode";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$discounts[] = $row;
			}
		}
		
		return $discounts;
	} // end of fn GetDiscounts
	
} // end of defn DiscountsListPage

$page = new DiscountsListPage();
$page->Page();
?>