<?php
include_once('sitedef.php');

class ProductInstructorsAjax extends AdminProductPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		switch ($_GET['action'])
		{	case 'popup':
				echo $this->ListPossibleInstructors();
				break;
			case 'refresh':
				echo $this->product->PeopleListTable();
				break;
			case 'add':
				if ($this->product->AddInstructor($_GET['inid']))
				{	echo '<div class="successmessage">New person added</div>';
				}
				echo $this->ListPossibleInstructors();
				break;
			case 'remove':
				$this->product->RemoveInstructor($_GET['inid']);
				echo $this->product->PeopleListTable();
				break;
		}
	} // end of fn ProductsLoggedInConstruct
	
	public function ListPossibleInstructors()
	{	ob_start();
		if ($instructors = $this->GetPossibleInstructors())
		{	echo '<table>';
			foreach ($instructors as $instructor_row)
			{	$instructor = new Instructor($instructor_row);
				echo '<tr><td>';
				if (file_exists($instructor->GetImageFile('thumbnail')))
				{	echo '<img height="50px" src="', $instructor->GetImageSRC('thumbnail'), '" />';
				}
				echo '</td><td>', $this->InputSafeString($instructor->GetFullName()), '</td><td><a onclick="InstructorAdd(', $this->product->id, ',', $instructor->id, ')">add</a></td></tr>';
			}
			echo '</table>';
		} else
		{	echo '<h3>No more possible isntructors</h3>';
		}
		return ob_get_clean();
	} // end of fn ListPossibleInstructors
	
	public function GetPossibleInstructors()
	{	$instructors = array();
		$existing = $this->product->GetPeople();
		$sql = 'SELECT * FROM instructors ORDER BY instname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$existing[$row['inid']])
				{	$instructors[$row['inid']] = $row;
				}
			}
		}
		return $instructors;
	} // end of fn GetPossibleInstructors
	
} // end of defn ProductInstructorsAjax

$ajax = new ProductInstructorsAjax();
?>