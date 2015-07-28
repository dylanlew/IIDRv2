<?php
include_once('sitedef.php');

class AjaxGalleryChooser extends AdminPage
{	
	function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	
			switch ($_GET['action'])
			{	case 'list': echo $this->ListGalleries();
							break;
				case 'show': echo $this->ShowGallery($_GET['gid'], $_GET['picked']);
							break;
				case 'photo': $photo = new AdminGalleryPhoto($_GET['photoid']);
							echo $photo->AdminPhotoDisplay('default', 0, 100);
							break;
			}
			
		}
	} // end of fn LoggedInConstruct

	public function ListGalleries()
	{	ob_start();
		if ($galleries = $this->GetAllGalleries())
		{	echo '<ul>';
			foreach ($galleries as $gallery_row)
			{	$gallery = new Gallery($gallery_row);
				echo '<li>', $this->InputSafeString($gallery->details['title']), ' - <a onclick="ShowGallery(', $gallery->id, ');">view gallery</a></li>';
			}
			echo '</ul>';
		} else
		{	echo '<h3>No galleries found</h3>';
		}
		return ob_get_clean();
	} // end of fn ListGalleries
	
	public function GetAllGalleries()
	{	$galleries = array();
		$sql = 'SELECT galleries.* FROM galleries, galleryphotos WHERE galleries.gid=galleryphotos.gid AND galleries.live=1 AND galleryphotos.live=1 GROUP by galleries.gid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$galleries[] = $row;
			}
		}
		return $galleries;
	} // end of fn GetAllGalleries
	
	public function ShowGallery($gid = 0, $picked = 0)
	{	ob_start();
		$gallery = new Gallery($gid);
		echo '<p><a onclick="RefreshGalleries();">&laquo; back to list of galleries</a></p><ul class="showgallery">';
		foreach ($gallery->photos as $photo_row)
		{	$photo = new GalleryPhoto($photo_row);
			echo '<li><h4><a onclick="PickPhoto(', $photo->id, ');">', $this->InputSafeString($photo->details['title']), '</a></h4><a onclick="PickPhoto(', $photo->id, ');"><img height="100px" src="', $photo->HasImage('default'), '" /></a>', $picked == $photo->id ? '<p>this is the current image used</p>' : '', '</li>';
		}
		echo '</ul>';
		return ob_get_clean();
	} // end of fn ShowGallery
	
} // end of defn AjaxGalleryChooser

$page = new AjaxGalleryChooser();
?>