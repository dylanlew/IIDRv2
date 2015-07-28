<?php 
require_once('init.php');

class DownloadsPage extends AccountPage
{	
	public $downloads;
	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct('downloads');
		$this->AddBreadcrumb('Downloads');
	} // end of fn LoggedInConstruct
	
	
	
	function LoggedInMainBody()
	{	echo '<div id="myDownloadsContainer"><ul>';
		foreach ($this->user->GetDownloads() as $download_row)
		{	$download = new StoreProductDownload($download_row);
			echo '<li>', $this->InputSafeString($download->details['filetitle']), ' - <a href="', $download->DownloadLink(), '" target="_blank">download now</a>';
			if ($download->details['filepass'])
			{	echo ' <span class="passwsord">you will need this password to open the file:', $this->InputSafeString($download->details['filepass']), '</span>';
			}
			echo '</li>';
		}
		echo '</ul></div>';
	} // end of fn LoggedInMainBody
	

	
} // end of defn OrdersPage

$page = new DownloadsPage();
$page->Page();
?>