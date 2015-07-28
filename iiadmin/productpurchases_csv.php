<?php
include_once('sitedef.php');

class ProductPurchasesCSV extends AdminProductPage
{	var $startdate = '';
	var $enddate = '';
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct('purchases');
		
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
		
		echo $this->PurchasesList($this->product->GetPurchases($this->startdate, $this->enddate));
		
	} // end of fn ProductsLoggedInConstruct
	
	public function PurchasesList($purchases = array())
	{	ob_start();
		$nl = "\n";
		header('Pragma: ');
		header('Cache-Control: ');
		header('Content-Type: application/csv;charset=UTF-8');
		header('Content-Disposition: attachment; filename="iidr_purchases_product_' . $this->product->id . '.csv"');
		if ($purchases)
		{	$students = array();
			echo 'order id,date / time,customer,user id,item qty,item value,item discount,order value,paid,delivered', $nl;
			foreach ($purchases as $order_row)
			{	$order = new AdminStoreOrder($order_row);
				if (!$students[$order->details['sid']])
				{	$students[$order->details['sid']] = new Student($order->details['sid']);
				}
				echo $order->id, ',"', date('d-M-y @H:i', strtotime($order->details['orderdate'])), '","', $this->CSVSafeString($students[$order->details['sid']]->GetName()), '",', $students[$order->details['sid']]->id, ',', $order->details['itemqty'], ',', number_format($order->details['itemprice'], 2), ',', number_format($order->details['itemdiscount'], 2), ',', number_format($order->GetRealTotal(), 2), ',"', date('d/m/y @H:i', strtotime($order->details['paiddate'])), '","', $order->details['delivered'] ? 'Yes' : '', '"', $nl;
			}
		}
		return ob_get_clean();
	} // end of fn PurchasesList
	
} // end of defn ProductPurchasesCSV

$page = new ProductPurchasesCSV();
?>