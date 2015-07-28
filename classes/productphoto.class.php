<?php
class ProductPhoto extends BlankItem
{	public $imagesizes = array('default'=>array(300, 300), 'thumbnail'=>array(120, 120), 'tiny'=>array(50, 50), 'full'=>array(600, 600));
	public $imagelocation = '';
	public $imagedir = '';
	
	public function __construct($id = 0)
	{	parent::__construct($id, 'storeproducts_photos', 'sppid');	
		$this->imagelocation = SITE_URL . 'img/product_photos/';
		$this->imagedir = CITDOC_ROOT . '/img/product_photos/';
		$this->Get($id);
	} // end of fn __construct
	
	public function HasImage($size = 'default')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	} // end of fn HasImage
	
	public function GetImageFile($size = 'default')
	{	return $this->ImageFileDirectory($size) . '/' . (int)$this->id .'.png';
	} // end of fn GetImageFile
	
	public function ImageFileDirectory($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size);
	} // end of fn FunctionName
	
	public function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id . '.png';
	} // end of fn GetImageSRC
	
} // end of class ProductPhoto
?>