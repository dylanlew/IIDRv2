<?php
include_once('sitedef.php');

class AdminGalleryPage extends AdminPage
{	
	public function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	public function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->GalleryConstruct();
		}
	} // end of fn LoggedInConstruct
	
	protected function GalleryConstruct()
	{	$this->breadcrumbs->AddCrumb('galleries.php', 'Galleries');
	} // end of fn GalleryConstruct
	
	public function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	echo $this->GalleryMainContent();
		}
	} // end of fn AdminBodyMain
	
	protected function GalleryMainContent(){}

} // end of defn AdminGalleryPage
?>