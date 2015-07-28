<?php
include_once('sitedef.php');

class AjaxInstructorGalleries extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function InstructorConstruct()
	{	parent::InstructorConstruct();
		
		if ($this->instructor->id)
		{	switch ($_GET['action'])
			{	case 'refresh':
					echo $this->instructor->GalleriesTable();
					break;
				case 'add':
					$this->instructor->AddGallery($_GET['gid']);
					break;
				case 'remove':
					$this->instructor->RemoveGallery($_GET['gid']);
					break;
				case 'popup':
					echo $this->GalleriesList();
					break;
				default: echo 'action not found: ', $this->InputSafeString($_GET['action']);
			}
		} else echo 'no course';
	} // end of fn InstructorConstruct
	
	private function GalleriesList()
	{	ob_start();
		if ($galleries = $this->GetGalleries())
		{	echo '<ul class="coursemmList">';
			foreach ($galleries as $gid=>$gallery_row)
			{	echo '<li class="mmitem_', $gid, '"><a onclick="GalleryAdd(', $this->instructor->id, ',', $gid, ');">', $this->InputSafeString($gallery_row['title']), '</a></li>';
			}
			echo '<ul>';
		//	$this->VarDump($galleries);
		} else
		{	echo 'no galleries available';
		}
		return ob_get_clean();
	} // end of fn GalleriesList
	
	private function GetGalleries()
	{	$galleries = array();
		$existing = $this->instructor->GetGalleries();
		$sql = 'SELECT * FROM galleries ORDER BY title, gid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$existing[$row['gid']])
				{	$galleries[$row['gid']] = $row;
				}
			}
		}
		
		return $galleries;
	} // end of fn GetGalleries
	
} // end of defn AjaxInstructorGalleries

$page = new AjaxInstructorGalleries();
?>