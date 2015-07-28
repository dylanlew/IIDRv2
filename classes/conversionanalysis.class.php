<?php
class ConversionAnalysis extends Base
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function GetCarts($startdate = '', $enddate = '')
	{	$carts = array();
		
		$where = array();
		
		if ((int)$startdate)
		{	$where[] = 'carts.created>="' . $startdate . ' 00:00:00"';
		}
		if ((int)$enddate)
		{	$where[] = 'carts.created<="' . $enddate . ' 23:59:59"';
		}
		
		$sql = 'SELECT carts.*, storeorders.paiddate FROM carts LEFT JOIN storeorders ON carts.orderid=storeorders.id';
		if ($where)
		{	$sql .= ' WHERE ' . implode(' AND ', $where);
		}
		$sql .= ' ORDER BY carts.created, carts.cartid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$carts[] = $row;
			}
		}
		
		return $carts;
	} // end of fn GetCarts
	
	public function CartsPerMonth($mstart = 0, $ystart = 0, $mend = 0, $yend = 0)
	{	$months = array();
		$endsql = date('Y-m-t', mktime(0, 0, 0,$mend, 1, $yend));
		$my = $startsql = date('Y-m-d', mktime(0, 0, 0,$mstart, 1, $ystart));
		
		while ($my < $endsql)
		{	$mystamp = strtotime($my);
			$months[substr($my, 0, 7)] = array('disp'=>date('M-y', $mystamp), 'stamp'=>$mystamp, 'carts'=>0, 'orders'=>0, 'paid'=>0);
			$my = $this->datefn->SQLDateTime(strtotime($my . ' +1 month'));
		}
		
		$orderid = 0;
		if ($carts = $this->GetCarts($startsql, $endsql))
		{	foreach ($carts as $cart)
			{	$months[$month = substr($cart['created'], 0, 7)]['carts']++;
				if ($cart['orderid'])
				{	$months[$month]['orders']++;
				}
				if ((int)$cart['paiddate'])
				{	$months[$month]['paid']++;
				}
				
			}
		}
		
		return $months;
	} // end of fn CartsPerMonth
	
	function CartsPerDayForMonth($month = 0, $year = 0)
	{	$days = array();
		$endsql = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
		$day = $startsql = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
		while ($day <= $endsql)
		{	$daystamp = strtotime($day);
			$days[$day] = array('disp'=>date('d-m-y', $daystamp), 'stamp'=>$daystamp, 'carts'=>0, 'orders'=>0, 'paid'=>0);
			$day = $this->datefn->SQLDate(strtotime($day . ' +1 day'));
		}
		
		$orderid = 0;
		if ($carts = $this->GetCarts($startsql, $endsql))
		{	foreach ($carts as $cart)
			{	$days[$day = substr($cart['created'], 0, 10)]['carts']++;
				if ($cart['orderid'])
				{	$days[$day]['orders']++;
				}
				if ((int)$cart['paiddate'])
				{	$days[$day]['paid']++;
				}
			}
		}
		
		return $days;
	} // end of fn CartsPerDayForMonth
	
} // end of defn ConversionAnalysis
?>