<?php
include_once('sitedef.php');

class OrdersSummaryCSV extends AccountsMenuPage
{	var $showunpaid = 0;
	var $startdate = '';
	var $enddate = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->showunpaid = $_GET['showunpaid'];
		
		// set up dates
		if (($ys = (int)$_GET['ystart']) && ($ds = (int)$_GET['dstart']) && ($ms = (int)$_GET['mstart']))
		{	$this->startdate = $this->datefn->SQLDate(mktime(0,0,0,$ms, $ds, $ys));
		} else
		{	$this->startdate = $this->datefn->SQLDate(strtotime('-1 month'));
		}
		
		if (($ys = (int)$_GET['yend']) && ($ds = (int)$_GET['dend']) && ($ms = (int)$_GET['mend']))
		{	$this->enddate = $this->datefn->SQLDate(mktime(0,0,0,$ms, $ds, $ys));
		} else
		{	$this->enddate = $this->datefn->SQLDate();
		}
		
		echo $this->OrdersList();
		
	} // end of fn AccountsLoggedInConstruct
	
	private function OrdersList()
	{	ob_start();
		$nl = "\n";
		header('Pragma: ');
		header('Cache-Control: ');
		header('Content-Type: application/csv;charset=UTF-8');
		header('Content-Disposition: attachment; filename="iidr_orders_summary.csv"');
		if ($orders = $this->GetOrders())
		{	$students = array();
			echo 'orderid,order time,customer,customer id,order value,paid,delivered', $nl;
			foreach ($orders as $order_row)
			{	$order = new AdminStoreOrder($order_row);
				if (!$students[$order->details['sid']])
				{	$students[$order->details['sid']] = new Student($order->details['sid']);
				}
				echo $order->id, ',"', date('d-M-y @H:i', strtotime($order->details['orderdate'])), '","', $this->CSVSafeString($students[$order->details['sid']]->GetName()), '",', $students[$order->details['sid']]->id, ',', number_format($order->GetRealTotal(), 2), ',"', (int)$order->details['paiddate'] ? date('d/m/y @H:i', strtotime($order->details['paiddate'])) : '', '","', $order->details['delivered'] ? 'Yes' : '', '"', $nl;
			}
		}
		return ob_get_clean();
	} // end of fn OrdersList
	
	private function GetOrders()
	{	$orders = array();
		$where = array();
		
		if ($this->startdate)
		{	$where[] = 'orderdate>="' . $this->startdate . ' 00:00:00"';
		}
		if ($this->enddate)
		{	$where[] = 'orderdate<="' . $this->enddate . ' 23:59:59"';
		}
		if (!$this->showunpaid)
		{	$where[] = 'NOT paiddate="0000-00-00 00:00:00"';
		}
		
		$sql = 'SELECT * FROM  storeorders';
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY orderdate DESC';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$orders[$row['id']] = $row;
			}
		}
		
		return $orders;
	} // end of fn GetOrders
	
} // end of defn OrdersSummaryCSV

$page = new OrdersSummaryCSV();
?>