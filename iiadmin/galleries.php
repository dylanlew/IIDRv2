<?php
include_once("sitedef.php");

class GalleriesPage extends AdminGalleryPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function GalleryConstruct()
	{	parent::GalleryConstruct();
	} // end of fn GalleryConstruct
	
	protected function GalleryMainContent()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="6"><a href="gallery.php">New Gallery</a></th></tr><tr><th></th><th>Title</th><th>Description</th><th>Photos</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetGalleries() as $gallery_row)
		{	$gallery = new AdminGallery($gallery_row);
			echo '<tr><td>';
			if ($cover = $gallery->HasCoverImage('thumbnail'))
			{	echo '<img src="', $cover, '" />';
			}
			echo '</td><td>', $this->InputSafeString($gallery->details['title']), '</td><td>', $this->InputSafeString($gallery->details['description']), '</td><td>', count($gallery->photos), '</td><td>', $gallery->details['live'] ? 'Yes' : '', '</td><td><a href="gallery.php?id=', $gallery->id, '">edit</a>';
			if ($gallery->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="gallery.php?id=', $gallery->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn GalleryMainContent

	private function GetGalleries()
	{	$galleries = array();
		$sql = 'SELECT * FROM galleries ORDER BY gid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$galleries[] = $row;
			}
		}
		return $galleries;
	} // end of fn GetGalleries
	
} // end of defn GalleriesPage

$page = new GalleriesPage();
$page->Page();
?>