<?php
include_once('sitedef.php');

class OrdersDetailsCSV extends AccountsMenuPage
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
		$blank = ',,,,,,';
		header('Pragma: ');
		header('Cache-Control: ');
		header('Content-Type: application/csv;charset=UTF-8');
		header('Content-Disposition: attachment; filename="iidr_orders_details.csv"');
		if ($orders = $this->GetOrders())
		{	$students = array();
			$orderid = 0;
			echo 'orderid,order time,customer,customer id,order value,paid,delivered,item type,item,item price', $nl;
			foreach ($orders as $order_row)
			{	$order = new AdminStoreOrder($order_row);
				//$items = $order->GetItems();
				if (!$students[$order->details['sid']])
				{	$students[$order->details['sid']] = new Student($order->details['sid']);
				}
				$total_discounts = 0;
				foreach ($order->GetItems() as $item)
				{	if ($order->id == $orderid)
					{	echo $blank;
					} else
					{	echo $order->id, ',"', date('d-M-y @H:i', strtotime($order->details['orderdate'])), '","', $this->CSVSafeString($students[$order->details['sid']]->GetName()), '",', $students[$order->details['sid']]->id, ',', number_format($order->GetRealTotal(), 2), ',"', (int)$order->details['paiddate'] ? date('d/m/y @H:i', strtotime($order->details['paiddate'])) : '', '","', $order->details['delivered'] ? 'Yes' : '', '"';
						$orderid = $order->id;
					}
					echo ',"', $this->CSVSafeString($item['ptype']), '","', (int)$item['qty'], ' x ', $this->CSVSafeString($item['title']), '",', number_format($item['totalpricetax'], 2), $nl;
					foreach ($item['discounts'] as $item_discount)
					{	if (!$discounts[$item_discount['discid']])
						{	$discounts[$item_discount['discid']] = new DiscountCode($item_discount['discid']);
						}
						echo $blank, ',"discount","', $this->CSVSafeString($discounts[$item_discount['discid']]->details['discdesc']), '",-', number_format($item_discount['discamount'], 2), $nl;
						$total_discounts += $item_discount['discamount'];
					}
				}
				foreach ($order->GetAllReferrerRewards() as $reward)
				{	echo $blank, ',"reward","refer-a-friend",-', number_format($reward['amount'], 2), $nl;
				}
				foreach ($order->GetAllAffRewards() as $reward)
				{	echo $blank, ',"reward","affiliate scheme",-', number_format($reward['amount'], 2), $nl;
				}
				foreach ($order->GetBundles() as $bundle)
				{	echo $blank, ',"bundle","', (int)$bundle['qty'], ' x ', $this->CSVSafeString($bundle['bname']), '",-', number_format($bundle['totaldiscount'], 2), $nl;
				}
				if ($total_discounts)
				{	echo $blank, ',,"Total discounts",-', number_format($total_discounts, 2), $nl;
				}
				if ($order->details['delivery_price'] > 0)
				{	echo $blank, ',"delivery","', ($order->details['delivery_id'] && ($deloption = new DeliveryOption($order->details['delivery_id'])) && $deloption->id) ? $this->CSVSafeString($deloption->details['title']) : '','",', number_format($order->details['delivery_price'], 2), $nl;
				}
				if ($order->details['txfee'] > 0)
				{	echo $blank, ',,"Transaction fee",', number_format($order->details['txfee'], 2), $nl;
				}
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
	
} // end of defn OrdersDetailsCSV

$page = new OrdersDetailsCSV();
?>