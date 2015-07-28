<?php
class Currencies extends Base
{	var $currencies = array();

	function __construct($currency = "") // constructor
	{	parent::__construct();
		
		$this->GetCurrencies();
	} // end of fn __construct
	
	function GetCurrencies()
	{	
		$this->Reset();
		
		//echo $sql;
		if ($result = $this->db->Query("SELECT *, IF(curorder=0, 1000, curorder) AS listorder FROM currencies ORDER BY listorder, curcode"))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->currencies[$row["curcode"]] = new Currency($row);
			}
		}
		
	} // end of fn GetMessages
	
	function Reset()
	{	$this->currencies = array();
	} // end of fn Reset
	
	function UpdateAllRates()
	{	
		foreach ($this->currencies as $currency)
		{	$currency->GoogleUpdateRate();
		}
	
	} // end of fn UpdateAllRates
	
	function AdminList()
	{	echo "<div id='currencies'>";
		if ($this->currencies)
		{	echo "<table>\n<tr>\n<th>Code</th>\n<th>Name</th>\n<th>Symbol</th>\n<th>Order<br />listed</th>\n",
				//	"<th class='num'>Conversion<br />rate</th>\n<th>\nRate updated<br /><a href='", $_SERVER["SCRIPT_NAME"], "?updateall=1'>update all now</a>\n</th>\n",
					"<th>Actions</th>\n</tr>\n";
			foreach ($this->currencies as $currency)
			{	
				echo "<tr class='stripe", $i++ % 2, "'>\n<td>", $currency->code, "</td>\n<td>", $currency->details["curname"], "</td>\n<td>", $currency->details["cursymbol"], " (" , htmlentities($currency->details["cursymbol"]), ")</td>\n<td class='num'>", $currency->details["curorder"] ? $currency->details["curorder"] : "", "</td>\n",
				/*		"<td class='num'>", number_format($currency->details["convertrate"], 4), "</td>\n<td>";
				if ($currency->history)
				{	echo date("d/m/y @H:i", strtotime($currency->history[0]["when"]));
				}
				echo "</td>\n",*/
						"<td><a href='currency.php?code=", $currency->code, "'>edit</a>";
				if ($histlink = $this->DisplayHistoryLink("currencies", $currency->code))
				{	echo "&nbsp;|&nbsp;", $histlink;
				}
				echo "</td>\n</tr>\n";
			}
			echo "</table>\n";
		}
		echo //"<p><a href='currency.php'>Add new currency</a></p>\n",
				"</div>\n";
	} // end of fn AdminList
	
} // end of defn Currencies
?>