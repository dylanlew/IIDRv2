<?php
include_once('sitedef.php');

class ATIViewPage extends AskTheImamPage
{	private $aticat;
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function ATIConstructor()
	{	parent::ATIConstructor();
		$this->aticat = new AdminATICat($_GET['id']);
		if (isset($_POST['catname']))
		{	$saved = $this->aticat->Save($_POST);
			$this->successmessage = $saved['successmessage'];
			$this->failmessage = $saved['failmessage'];
		}
		
		if ($_GET['delete'] && $_GET['confirm'])
		{	if ($this->aticat->Delete())
			{	header('location: aticatlist.php');
				exit;
			} else
			{	$this->failmessage = 'Delete failed';
			}
		}
		
		$this->breadcrumbs->AddCrumb('aticatlist.php', 'Categories');
		$this->breadcrumbs->AddCrumb('aticat.php?id=' . $this->aticat->id, $this->aticat->id ? $this->InputSafeString($this->aticat->details['catname']) : 'create new');
	} // end of fn ATIConstructor
	
	public function ATIMainContent()
	{	echo $this->aticat->InputForm();
	} // end of fn ATIMainContent

} // end of defn ATIViewPage

$page = new ATIViewPage();
$page->Page();
?>