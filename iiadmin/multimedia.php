<?php
include_once('sitedef.php');

class MultimediaEditPage extends AdminMultimediaPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function MMLoggedinConstruct()
	{	parent::MMLoggedinConstruct('edit');
		$this->css[] = 'datepicker.css';
		$this->js[] = 'datepicker.js';
		$this->js[] = 'tiny_mce/jquery.tinymce.js';
		$this->js[] = 'pageedit_tiny_mce.js';
		
		
		if (!$this->multimedia->id)
		{	$this->breadcrumbs->AddCrumb('multimedia.php', 'Creating new');
		}
	} // end of fn MMLoggedinConstruct
	
	protected function MMConstructFunctions()
	{	if (isset($_POST['mmname']))
		{	$saved = $this->multimedia->Save($_POST, $_FILES['pdfdownload'], $_FILES['poster'], $_FILES['mp3download']);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($this->multimedia->id && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->multimedia->Delete())
			{	$this->Redirect('multimedialist.php');
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
	} // end of fn MMConstructFunctions
	
	protected function MMBodyMain()
	{	parent::MMBodyMain();
		echo $this->multimedia->InputForm(), $this->multimedia->ViewedTable(), $this->multimedia->ListInstructorsUsing(), $this->multimedia->ListCoursesUsing();
	} // end of fn MMBodyMain
	
} // end of defn MultimediaEditPage

$page = new MultimediaEditPage();
$page->Page();
?>