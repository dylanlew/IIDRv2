<?php
include_once('sitedef.php');

class BannerItemPage extends AdminPage
{	private $item;
	private $bannerset;
	
	function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->css[] = 'adminbanneritem.css';
			$this->js[] = 'adminbanneritem.js';
			$this->item = new AdminBannerItem($_GET['id']);
			if ($this->item->id)
			{	$this->bannerset = new AdminBannerSet($this->item->details['setid']);
			} else
			{	if ($_GET['bannerid'])
				{	$this->bannerset = new AdminBannerSet($_GET['bannerid']);
				} else
				{	$this->bannerset = new AdminBannerSet($_POST['setid']);
				}
			}
			
			if ($_POST['disptitle'])
			{	$saved = $this->item->Save($_POST, $this->bannerset->id);
				$this->failmessage = $saved['failmessage'];
				$this->successmessage = $saved['successmessage'];
			}
			
			if ($_GET['delete'] && $_GET['confirm'])
			{	if ($this->item->Delete())
				{	header('location: bannerset-edit.php?id=' . $this->bannerset->id);
					exit;
				} else
				{	$fail = 'delete failed';
				}
			}

			$this->breadcrumbs->AddCrumb('bannersets.php', 'Banners');
			$this->breadcrumbs->AddCrumb('bannerset-edit.php?id=' . $this->bannerset->id, $this->InputSafeString($this->bannerset->details['title']));
			$this->breadcrumbs->AddCrumb('banneritem.php', $this->item->id ? $this->InputSafeString($this->item->details['disptitle']) : 'New item');
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	echo $this->item->InputForm($this->bannerset->id);
		}
	} // end of fn AdminBodyMain

} // end of defn BannerSetsPage

$page = new BannerItemPage();
$page->Page();
?>