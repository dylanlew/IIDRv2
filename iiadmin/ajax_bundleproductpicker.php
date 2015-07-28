<?php
include_once('sitedef.php');

class BundleProductPicker extends AdminProductsPage
{	private $bundle;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
	//	$this->VarDump($_GET);
		$this->bundle = new AdminBundle($_GET['bundle']);
	//	$this->VarDump($this->bundle->details);
		
		switch ($_GET['action'])
		{	case 'list': echo $this->OutputList($_GET['ptype']);
						break;
			case 'table': echo $this->bundle->ProductsTable();
						break;
			case 'add': $saved = $this->bundle->AddProduct($_GET['product'], $_GET['ptype']);
						$this->failmessage = $saved['failmessage'];
						$this->successmessage = $saved['successmessage'];
						$this->Messages();
						echo $this->OutputList($_GET['ptype']);
						break;
			case 'remove': $saved = $this->bundle->RemoveProduct($_GET['bpid']);
						$this->failmessage = $saved['failmessage'];
						$this->successmessage = $saved['successmessage'];
						$this->Messages();
						echo $this->bundle->ProductsTable();
						break;
		}
		
	} // end of fn ProductsLoggedInConstruct
	
	public function OutputList($ptype = '')
	{	ob_start();
		switch ($_GET['ptype'])
		{	case 'store': echo $this->StoreList();
						break;
			case 'course': echo $this->CourseList();
						break;
		}
		return ob_get_clean();
	} // end of fn OutputList
	
	public function StoreList()
	{	ob_start();
		if ($cats = $this->GetStoreProducts())
		{	echo '<ul>';
			foreach ($cats as $catid=>$products)
			{	$cat = new StoreCategory($catid);
				echo '<li><h3>', $this->InputSafeString($cat->details['ctitle']), '</h3><ul>';
				foreach ($products as $product)
				{	echo '<li>', $this->InputSafeString($product['title']), ' - ';
					if ($this->bundle->IsProductBundled($product['id'], 'store'))
					{	echo 'already added';
					} else
					{	echo '<a onclick="AddProduct(', $this->bundle->id, ',\'store\',', $product['id'], ');">add this product</a>';
					}
					echo '</li>';
				}
				echo '</ul></li>';
			}
			echo '</ul>';
		}
		return ob_get_clean();
	} // end of fn StoreList
	
	private function GetStoreProducts()
	{	$cats = array();
		
		$sql = 'SELECT * FROM storeproducts ORDER BY id';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$cats[$row['category']])
				{	$cats[$row['category']] = array();
				}
				$cats[$row['category']][$row['id']] = $row;
			}
		}
		
		return $cats;
	} // end of fn GetStoreProducts
	
	public function CourseList()
	{	ob_start();
		if ($courses = $this->GetCourses())
		{	echo '<ul>';
			foreach ($courses as $cid=>$tickets)
			{	$course = new Course($cid);
				echo '<li><h3>', $this->InputSafeString($course->content['ctitle']), '</h3><ul>';
				foreach ($tickets as $ticket)
				{	echo '<li>', $this->InputSafeString($ticket['tname']), ' - ';
					if ($this->bundle->IsProductBundled($ticket['tid'], 'course'))
					{	echo 'already added';
					} else
					{	echo '<a onclick="AddProduct(', $this->bundle->id, ',\'course\',', $ticket['tid'], ');">add this ticket</a>';
					}
					echo '</li>';
				}
				echo '</ul></li>';
			}
			echo '</ul>';
		}
		
		return ob_get_clean();
	} // end of fn CourseList
	
	private function GetCourses()
	{	$courses = array();
		
		$sql = 'SELECT coursetickets.* FROM coursetickets, courses WHERE coursetickets.cid=courses.cid ORDER BY courses.starttime DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$courses[$row['cid']])
				{	$courses[$row['cid']] = array();
				}
				$courses[$row['cid']][$row['tid']] = $row;
			}
		}
		
		return $courses;
	} // end of fn GetCourses
	
} // end of defn BundleProductPicker

$page = new BundleProductPicker();
?>