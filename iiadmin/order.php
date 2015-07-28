<?php
include_once('sitedef.php');

class OrderDetailsPage extends AccountsMenuPage
{	private $order;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->css[] = 'adminorders.css';
		$this->js[] = 'adminorders.js';
		$this->order = new AdminStoreOrder($_GET['id']);
		
		if ($_GET['delete'] && $_GET['confirm'])
		{	if ($this->order->Delete())
			{	header('location: orders.php');
			} else
			{	$this->failmessage = 'delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('orders.php', 'Orders');
		$this->breadcrumbs->AddCrumb('order.php?id=' . $this->order->id, '#' . $this->order->id);
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	echo $this->DeleteLink(), $this->order->DisplayDetails(), $this->order->DisplayItems(), $this->order->DisplayDelivery(), $this->order->DisplayPayment(), $this->order->DisplayCart();
	} // end of fn AccountsBody
	
	public function DeleteLink()
	{	ob_start();
		if ($this->order->CanDelete())
		{	echo '<a href="order.php?id=', $this->order->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm that you want to ' : '', 'delete this order</a>';
		}
		return ob_get_clean();
	} // end of fn DeleteLink
	
} // end of defn OrderDetailsPage

$page = new OrderDetailsPage();
$page->Page();
?>