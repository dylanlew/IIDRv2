<?php
include_once('sitedef.php');

class OrderDetailsPage extends AccountsMenuPage
{	private $order;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->order = new AdminStoreOrder($_GET['id']);
		switch ($_GET['action'])
		{	case 'savedel':
				if (isset($_POST['delnotes']))
				{	$saved = $this->order->SaveDelivery($_POST);
					$this->failmessage = $saved['failmessage'];
					$this->successmessage = $saved['successmessage'];
					$this->Messages();
					echo $this->order->DisplayDeliveryContents();
				}
				break;
			case 'savepay':
				
				if (isset($_POST['pmtnotes']))
				{	$success = array();
					$fail = array();
					$saved = $this->order->SavePaymentNotes($_POST);
					if ($saved['failmessage'])
					{	$fail[] = $saved['failmessage'];
					}
					if ($saved['successmessage'])
					{	$success[] = $saved['successmessage'];
					}
					
					if ($_POST['paid'])
					{	$saved = $this->order->RecordManualPayment();
						if ($saved['failmessage'])
						{	$fail[] = $saved['failmessage'];
						}
						if ($saved['successmessage'])
						{	$success[] = $saved['successmessage'];
						}
					}
					
					$this->order->Refresh();
					
					if ($_POST['cancel'])
					{	if ($this->order->CancelOrder())
						{	$success[] = 'Order cancelled';
						}
					}
					
					$this->failmessage = implode(', ', $fail);
					$this->successmessage = implode(', ', $success);
					$this->Messages();
					echo $this->order->DisplayPaymentContents();
				}
				break;
		}
		
	} // end of fn AccountsLoggedInConstruct
	
} // end of defn OrderDetailsPage

$page = new OrderDetailsPage();
?>