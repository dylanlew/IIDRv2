<?php
class Gallery extends BlankItem
{	public $photos = array();
	
	public function __construct($id = null)
	{	parent::__construct($id, 'galleries', 'gid');
	} // fn __construct
	
	protected function ResetExtra()
	{	$this->photos = array();
	} // end of fn ResetExtra
	
	protected function GetExtra()
	{	$this->GetPhotos();
	} // end of fn GetExtra
	
	public function GetPhotos($liveonly = true)
	{	$this->photos = array();
		$where = array('gid = ' . (int)$this->id);
		if ($liveonly)
		{	$where[] = 'live=1';
		}
		$sql = 'SELECT * FROM galleryphotos WHERE ' . implode(' AND ', $where) . ' ORDER BY displayorder ASC';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->photos[$row['id']] = $row;	
			}
		}
		
	} // end of fn GetPhotos
	
	public function GetCover()
	{
		if ($this->details['cover'] && $this->photos && $this->photos[$this->details['cover']])
		{	return $this->photos[$this->details['cover']];
		}
		
		if ($this->photos)
		{	foreach ($this->photos as $photo)
			{	return $photo;
			}
		}
		
	} // end of fn GetCover
	
	public function HasCoverImage($size = 'default')
	{	if (($cover = $this->GetCover()) && ($cover_photo = new GalleryPhoto($cover)) && $cover_photo->id && ($src = $cover_photo->HasImage($size)))
		{	return $src;
		}
		return false;
	} // end of fn HasCoverImage
	
	public function FrontEndList()
	{	ob_start();
		echo '<div id="gallery_carousel"><ul class="elastislide-list">';
		foreach ($this->photos as $photo_row)
		{	$photo = new GalleryPhoto($photo_row);
			echo '<li><a onclick="OpenGalleryPhoto(', $photo->id, ');"><img src="', $photo->HasImage('medium'), '" alt="', $title = $this->InputSafeString($photo->details['title']), '" title="', $title, '" /><p>', $title, '</p></a></li>';
		}
		echo '</ul></div><!-- START gallery photo modal popup --><div id="gal_photo_modal_popup" class="jqmWindow"><a href="#" class="submit" onclick="CloseGalleryPhoto(); return false;">Close</a><div id="galPhotoModalInner"></div></div>';
		return ob_get_clean();
	} // end of fn FrontEndList
	
	public function FrontEndListLightbox()
	{	ob_start();
		echo '<div id="gallery_carousel_', $this->id, '"><ul class="elastislide-list">';
		foreach ($this->photos as $photo_row)
		{	$photo = new GalleryPhoto($photo_row);
			echo '<li><a href="', $photo->HasImage(), '" rel="lightbox"><img src="', $photo->HasImage('medium'), '" alt="', $title = $this->InputSafeString($photo->details['title']), '" title="', $title, '" /><p>', $title, '</p></a></li>';
		}
		echo '</ul></div><!-- START gallery photo modal popup --><div id="gal_photo_modal_popup" class="jqmWindow"><a href="#" class="submit" onclick="CloseGalleryPhoto(); return false;">Close</a><div id="galPhotoModalInner"></div></div>';
		return ob_get_clean();
	} // end of fn FrontEndListLightbox
	
} // end of class defn Gallery
?>