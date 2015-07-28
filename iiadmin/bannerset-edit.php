<?php
include_once('sitedef.php');

class BannerSetsPage extends AdminPage
{	private $set;
	
	function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	
			$this->set = new AdminBannerSet($_GET['id']);
			
			if($this->set->id && $_GET['delete'])
			{	if ($this->set->Delete())
				{	$this->RedirectBack('bannersets.php');
				} else
				{	$this->failmessage = 'delete failed';
				}
			}
		
			if ($_POST['title'])
			{	$saved = $this->set->Save($_POST);
				$this->failmessage = $saved['failmessage'];
				$this->successmessage = $saved['successmessage'];
			}
			
			$this->breadcrumbs->AddCrumb('bannersets.php', 'Banners');
			$this->breadcrumbs->AddCrumb('bannerset-edit.php?id=' . $this->set->id, $this->set->id ? $this->InputSafeString($this->set->details['title']) : 'New banner');
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("web content"))
		{	echo $this->CanAdminUser('administration') ? $this->set->InputForm() : '', $this->set->ListItems();
		}
	} // end of fn AdminBodyMain

} // end of defn BannerSetsPage

$page = new BannerSetsPage();
$page->Page();
?>