<?php
include_once('sitedef.php');

class AjaxDiscountEdit extends AdminDiscountPage
{	var $discount;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function DiscountLoggedInConstruct()
	{	parent::DiscountLoggedInConstruct();
		$this->discount  = new AdminDiscountCode($_GET['id']);
		
		switch ($_GET['action'])
		{	case 'list': echo $this->ListOptions($_GET['ptype'], $_GET['prodid']);
						break;
			case 'select': echo $this->discount->ProductTypeDetails($_GET['ptype'], $_GET['prodid']);
						break;
		}
		
	} // end of fn DiscountLoggedInConstruct
	
	public function ListOptions($ptype = '', $prodid = 0)
	{	if ($options = $this->GetOptions($ptype))
		{	echo '<ul><li', $prodid ? '' : ' class="ptselected"', '><a onclick="PTypeProductSelect(', (int)$this->discount->id, ',0);">Apply to all ', $this->prodtypes[$prodid], '</a> ... or just</li>';
			foreach ($options as $option)
			{	$selected = $prodid == $option['option_id'];
				echo '<li', $selected ? ' class="ptselected"' : '', '>', $this->InputSafeString($option['option_text']);
				if (!$selected)
				{	echo ' - <a onclick="PTypeProductSelect(', (int)$this->discount->id, ',', (int)$option['option_id'], ');">apply discount to this</a>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}
	} // end of fn ListOptions
	
	public function GetOptions($ptype = '')
	{	switch ($ptype)
		{	case 'store':
				$sql = 'SELECT id AS option_id, title AS option_text FROM storeproducts ORDER BY title, id';
				break;
			case 'event':
				echo $sql = 'SELECT courses.cid AS option_id, coursecontent.ctitle AS option_text, courses.starttime FROM courses, coursecontent WHERE courses.ccid=coursecontent.ccid AND courses.starttime>="' . $this->datefn->SQLDate(strtotime('-3 weeks')) . '" AND coursecontent.ctype="event" ORDER BY courses.starttime DESC, courses.cid';
				break;
			case 'course':
				echo $sql = 'SELECT courses.cid AS option_id, coursecontent.ctitle AS option_text, courses.starttime FROM courses, coursecontent WHERE courses.ccid=coursecontent.ccid AND courses.starttime>="' . $this->datefn->SQLDate(strtotime('-3 weeks')) . '" AND coursecontent.ctype="course" ORDER BY courses.starttime DESC, courses.cid';
				break;
		}
		$options = array();
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($row['starttime'])
				{	$row['option_text'] .= ' - ' . date('j M Y', strtotime($row['starttime']));
				}
				$options[] = $row;
			}
		} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		return $options;
	} // end of fn GetOptions
	
} // end of defn AjaxDiscountEdit

$page = new AjaxDiscountEdit();
?>