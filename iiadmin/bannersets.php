<?php
include_once('sitedef.php');

class BannerSetsPage extends AdminPage
{	
	private $set;
	private $sets = array();
	
	function __construct()
	{	parent::__construct('CONTENT');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->breadcrumbs->AddCrumb('bannersets.php', 'Banners');
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("web content"))
		{	echo $this->Listing();
		}
	} // end of fn AdminBodyMain
	
	function Listing()
	{	ob_start();
		
		echo '<table>';
		if ($this->CanAdminUser('administration'))
		{	echo '<tr class="newlink"><th colspan="3"><a href="bannerset-edit.php">new banner set</a></th></tr>';
		}
		echo '<tr><th>Title</th><th>Items</th><th>Actions</th></tr>';
		
		foreach($this->GetBannerSets() as $set_row)
		{	$set = new AdminBannerSet($set_row);
			echo '<tr><td>', $this->InputSafeString($set->details['title']), '</td><td>', count($set->items), '</td><td><a href="bannerset-edit.php?id=', $set->id, '">Edit</a>';
			if ($set->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="bannerset-edit.php?id=', $set->id, '&delete=1">Delete</a>';
			}
			echo '</td></tr>';
		}
		
		echo '</table>';
		return ob_get_clean();
	} // end of fn AdminBodyMain

	public function GetBannerSets()
	{	$sets = array();
		if ($result = $this->db->Query('SELECT * FROM bannersets'))
		{	while ($row = $this->db->FetchArray($result))
			{	$sets[] = $row;
			}
		}
		return $sets;
	} // end of fn GetBannerSets
	
} // end of defn BannerSetsPage

$page = new BannerSetsPage();
$page->Page();
?>