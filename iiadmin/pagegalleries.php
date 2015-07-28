<?php
include_once('sitedef.php');

class PageGalleriesPage extends AdminPageEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	protected function PageEditConstruct()
	{	parent::PageEditConstruct('galleries');
		$this->js[] = 'admin_pagegallery.js';
		$this->css[] = 'course_mm.css';
		$this->breadcrumbs->AddCrumb('pageedit.php?id=' . $this->page->id, 'galleries');
	} // end of fn PageEditConstruct
	
	public function ConstructFunctions()
	{	
	} // end of fn ConstructFunctions
	
	protected function PageEditMainContent()
	{	parent::PageEditMainContent();
		echo $this->page->GalleriesDisplay();
	} // end of fn PageEditMainContent
	
} // end of defn PageGalleriesPage

$page = new PageGalleriesPage();
$page->Page();
?>