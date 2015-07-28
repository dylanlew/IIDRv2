<?php
include_once('sitedef.php');

class FPTextByLabelPage extends AdminFPTPage
{	var $fptext = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function PagesConstruct()
	{	parent::PagesConstruct();
		$this->fptext = new AdminFPText($_GET['name']);
		if (is_array($_POST['content']))
		{	$saved = $this->fptext->Save($_POST['content']);
			if ($saved['failmessage'])
			{	$this->failmessage = $saved['failmessage'];
			}
			if ($saved['successmessage'])
			{	$this->successmessage = $saved['successmessage'];
			}
			if ($this->successmessage && !$this->failmessage)
			{	header('location: fptlist.php');
				exit;
			}
		}
		
		if ($this->user->CanUserAccess('technical') && $_GET['delete'] && $_GET['confirm'])
		{	if ($this->fptext->Delete())
			{	header('location: fptlist.php');
			}
		}
		
		$this->breadcrumbs->AddCrumb('fptbylabel.php?name=' . $this->fptext->name, $this->fptext->name);
	} // end of fn PagesConstruct

	function PagesContent()
	{	$this->fptext->UpdateForm();
		if ($this->user->CanUserAccess('technical'))
		{	echo '<p><a href="fptbylabel.php?name=', $this->fptext->name, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you want to ' : '', 'delete this label (and all translations)</a></p>';
		}
	} // end of fn PagesContent
	
} // end of defn FPTextByLabelPage

$page = new FPTextByLabelPage();
$page->Page();
?>