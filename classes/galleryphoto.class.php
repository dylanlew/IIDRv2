<?php
class GalleryPhoto extends BlankItem
{
	public $imagelocation = '';
	public $imagedir = '';
	public $imagesizes = array('default'=>array(0, 0), 'thumbnail'=>array(100, 100), 'medium'=>array(250, 200));
	
	public function __construct($id = null)
	{
		parent::__construct($id, 'galleryphotos', 'id');
		
		$this->imagelocation = SITE_URL . 'img/galleries/';
		$this->imagedir = CITDOC_ROOT . '/img/galleries/';
		
	} // end of fn __construct
	
	public function HasImage($size = 'default')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	} // end of fn HasImage
	
	public function GetImageFile($size = 'default')
	{	return $this->ImageFileDirectory() . '/' . $this->InputSafeString($size) . '.png';
	} // end of fn GetImageFile
	
	public function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->id . '/' . $this->InputSafeString($size) . '.png';
	} // end of fn GetImageSRC
	
	public function ImageFileDirectory()
	{	return $this->imagedir . $this->id;
	} // end of fn ImageFileDirectory
	
} // end of defn GalleryPhoto
?>