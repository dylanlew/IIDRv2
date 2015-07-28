<?php
class IncomeAnalysis extends Base
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function GetOrderItems($startdate = '', $enddate = '', $ptype = '')
	{	$items = array();
		
		$tables = array('storeorders', 'storeorderitems');
		$where = array('storeorders.id=storeorderitems.orderid', 'NOT storeorders.paiddate="0000-00-00 00:00:00"');
		$fields = array('storeorderitems.orderid', 'storeorders.paiddate', 'storeorders.delivery_price', 'storeorderitems.ptype', 'storeorderitems.totalprice', 'storeorderitems.totalpricetax', 'storeorderitems.discount_total', 'storeorderitems.qty');
		
		if ((int)$startdate)
		{	$where[] = 'storeorders.paiddate>="' . $startdate . ' 00:00:00"';
		}
		if ((int)$enddate)
		{	$where[] = 'storeorders.paiddate<="' . $enddate . ' 23:59:59"';
		}
		if ($ptype)
		{	$where[] = 'storeorderitems.ptype="' . $ptype . '"';
		}
		
		$sql = 'SELECT ' . implode(',', $fields) . ' FROM ' . implode(', ', $tables) . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY storeorders.orderdate, storeorders.id, storeorderitems.id';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$items[] = $row;
			}
		}
		
		return $items;
	} // end of fn GetOrderItems
	
	public function IncomePerMonth($mstart = 0, $ystart = 0, $mend = 0, $yend = 0, $ptype = '')
	{	$months = array();
		$endsql = date('Y-m-t', mktime(0, 0, 0,$mend, 1, $yend));
		$my = $startsql = date('Y-m-d', mktime(0, 0, 0,$mstart, 1, $ystart));
		
		while ($my < $endsql)
		{	$mystamp = strtotime($my);
			$months[substr($my, 0, 7)] = array('disp'=>date('M-y', $mystamp), 'stamp'=>$mystamp, 'income'=>0, 'discount'=>0, 'delivery'=>0, 'orders'=>0, 'items'=>0);
			$my = $this->datefn->SQLDateTime(strtotime($my . ' +1 month'));
		}
		
		$orderid = 0;
		if ($items = $this->GetOrderItems($startsql, $endsql, $ptype))
		{	foreach ($items as $item)
			{	$months[$month = substr($item['paiddate'], 0, 7)]['items'] += $item['qty'];
				$months[$month]['income'] += $item['totalpricetax'] - $item['discount_total'];
				$months[$month]['discount'] += $item['discount_total'];
				if ($orderid != $item['orderid'])
				{	$orderid = $item['orderid'];
					$months[$month]['delivery'] += $item['delivery_price'];
					$months[$month]['income'] += $item['delivery_price'];
					$months[$month]['orders']++;
				}
			}
		}
		
		return $months;
	} // end of fn IncomePerMonth
	
	function IncomePerDayForMonth($month = 0, $year = 0)
	{	$days = array();
		$endsql = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
		$day = $startsql = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
		while ($day <= $endsql)
		{	$daystamp = strtotime($day);
			$days[$day] = array('disp'=>date('d-m-y', $daystamp), 'stamp'=>$daystamp, 'income'=>0, 'discount'=>0, 'delivery'=>0, 'orders'=>0, 'items'=>0);
			$day = $this->datefn->SQLDate(strtotime($day . ' +1 day'));
		}
		
		$orderid = 0;
		if ($items = $this->GetOrderItems($startsql, $endsql, $ptype))
		{	foreach ($items as $item)
			{	$days[$day = substr($item['paiddate'], 0, 10)]['items'] += $item['qty'];
				$days[$day]['income'] += $item['totalpricetax'] - $item['discount_total'];
				$days[$day]['discount'] += $item['discount_total'];
				if ($orderid != $item['orderid'])
				{	$orderid = $item['orderid'];
					$days[$day]['delivery'] += $item['delivery_price'];
					$days[$day]['income'] += $item['delivery_price'];
					$days[$day]['orders']++;
				}
			}
		}
		
		return $days;
	} // end of fn IncomePerDayForMonth
	
} // end of defn IncomeAnalysis
?>