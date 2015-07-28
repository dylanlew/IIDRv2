<?php
include_once('sitedef.php');

class AjaxBannerPicker extends CMSPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		
		echo $this->ListBanners();
		
	} // end of fn CMSLoggedInConstruct
	
	private function ListBanners()
	{	ob_start();
		if ($banners = $this->GetBanners())
		{	echo '<ul>';
			
			foreach ($banners as $banner_row)
			{	$banner = new BannerSet($banner_row);
				echo '<li>', $title = $this->InputSafeString($banner->details['title']), ' - ', $banner->id == $_GET['picked'] ? '{this is the current banner} ' : '', '<a onclick="BannerChoose(\'', $banner->id == $_GET['picked'] ? '0' : $banner->id, '\',\'', $banner->id == $_GET['picked'] ? 'none' : addslashes($banner->details['title']), '\');">', $banner->id == $_GET['picked'] ? 'remove' : 'add this', '</a></li>';
			}
			echo '</ul>';
		} else
		{	echo '<h3>No Banners Available</h3>';
		}
		return ob_get_clean();
	} // end of fn ListBanners
	
	private function GetBanners()
	{	$banners = array();
		$sql = 'SELECT * FROM bannersets ORDER BY title';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$banners[] = $row;
			}
		}
		return $banners;
	} // end of fn GetBanners
	
} // end of defn AjaxBannerPicker

$page = new AjaxBannerPicker();
?>