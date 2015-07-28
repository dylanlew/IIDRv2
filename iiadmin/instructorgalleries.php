<?php
include_once('sitedef.php');

class InstructorGaleriesPage extends AdminInstructorPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function InstructorConstruct()
	{	parent::InstructorConstruct();
		$this->inst_option = 'galleries';
		
		$this->css[] = 'course_edit.css';
		$this->js[] = 'admin_instgallery.js';
		$this->css[] = 'course_mm.css';
		
		$this->breadcrumbs->AddCrumb('instructorgalleries.php?id' . $this->instructor->id, 'Galleries');
	} // end of fn InstructorConstruct
	
	function InstructorBody()
	{	return $this->instructor->GalleriesDisplay();
	} // end of fn InstructorBody
	
} // end of defn InstructorGaleriesPage

$page = new InstructorGaleriesPage();
$page->Page();
?>