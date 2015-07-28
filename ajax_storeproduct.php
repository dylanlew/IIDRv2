<?php 
require_once('init.php');

class StoreProductAjax extends StorePage
{	public $product;
	
	function __construct($id = 0)
	{	parent::__construct('store');
	//	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');//Dont cache
	//	header('Pragma: no-cache');//Dont cache
	//	header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
		$this->product = $this->store->GetProduct($_GET['id'], 'store');
		switch ($_GET['action'])
		{	case 'image':
				ob_start();
				echo $this->product->MainImageDisplay($_GET['image']);
				$output = ob_get_clean();
			//	$this->LogOutput($output);
				echo $output;
				break;
			case 'fullimage':
				echo $this->product->ImageDisplay($_GET['image'], 'full');
				break;
		}
	} // end of fn __construct
	
	public function LogOutput($output = '')
	{	if ($f = fopen('test/test.txt', 'w'))
		{	fputs($f, $output);
			fclose($f);
		}
	} // end of fn LogOutput
	
} // end of defn StoreProductAjax

$page = new StoreProductAjax();
?>