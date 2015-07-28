<?php
include_once('sitedef.php');

class PageEditPage extends AdminPageEditPage
{
	function __construct()
	{	parent::__construct('CMS');
	} //  end of fn __construct
	
	protected function PageEditConstruct()
	{	parent::PageEditConstruct('edit');
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'pageedit_tiny_mce.js';
		$this->js[] = 'admin_bannerselect.js';
		
		if (!$this->page->id)
		{	$this->breadcrumbs->AddCrumb('pageedit.php', 'New Page');
		}
	} // end of fn PageEditConstruct
	
	public function ConstructFunctions()
	{	if ($_POST['pagetitle'])
		{	$saved = $this->page->Save($_POST, $_FILES['imagefile']);
			if ($saved['failmessage'])
			{	$this->failmessage = $saved['failmessage'];
			}
			if ($saved['successmessage'])
			{	$this->successmessage = $saved['successmessage'];
			}
		}
	} // end of fn ConstructFunctions
	
	protected function PageEditMainContent()
	{	parent::PageEditMainContent();
		$this->page->InputForm();
	} // end of fn PageEditMainContent
	
} // end of defn PageEditPage

$page = new PageEditPage();
$page->Page();
?>