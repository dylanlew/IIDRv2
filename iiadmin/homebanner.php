<?php
include_once("sitedef.php");

class HomeBannerPage extends CMSPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("homebanner.php", "Homepage banner items");
	} // end of fn CMSLoggedInConstruct
	
	function CMSBodyMain()
	{	$this->ItemsList();
	} // end of fn CMSBodyMain
	
	function ItemsList()
	{	echo '<table><tr class="newlink"><th colspan="9"><a href="homebanneritem.php">new item</a></th></tr><tr><th></th><th>Title</th><th>Link (if any)</th><th>Order</th><th>Live</th><th>Languages</th><th>Actions</th></tr>';
		$homebanner = new AdminHomeBanner();
		foreach ($homebanner->items as $hbitem)
		{	
			echo '<tr class="stripe', $i++ % 2, '"><td>';
			if (file_exists($hbitem->ThumbFile()))
			{	echo '<img src="', $hbitem->ThumbSRC(), '" />';
			} else
			{	echo 'no<br />image';
			}
			echo '</td><td>', $this->InputSafeString($hbitem->details['hbtitle']), '</td><td>', $hbitem->Link(), '</td><td>', (int)$hbitem->details['hborder'], '</td><td>', $hbitem->details['live'] ? 'Yes' : 'No', '</td><td>', $hbitem->LangUsedString(), '</td><td><a href="homebanneritem.php?id=', $hbitem->id, '">edit</a>';
			if ($histlink = $this->DisplayHistoryLink('homebanner', $hbitem->id))
			{	echo '&nbsp;|&nbsp;', $histlink;
			}
			if ($hbitem->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="homebanneritem.php?id=', $hbitem->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	} // end of fn ItemsList
	
} // end of defn HomeBannerPage

$page = new HomeBannerPage();
$page->Page();
?>