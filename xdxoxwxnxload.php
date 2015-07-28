<?php 
require_once('init.php');

class DownloadPage extends AccountPage
{	
	public $download;
	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	
		//$this->AddBreadcrumb("My Account", $this->link->GetLink('account.php'));
		//$this->AddBreadcrumb("Orders");
		
		$this->download = new StoreDownload($_GET['id']);
		
		if($this->download->id && $this->download->CanDownload($this->user->id))
		{
			$this->download->Download();
		}
		else
		{
			$this->Redirect('register.php');	
		}
		
	} // end of fn LoggedInConstruct
	

	function LoggedInMainBody()
	{	
		echo $this->download->GetFilename();
	
	} // end of fn LoggedInMainBody

	
} // end of defn DownloadPage

$page = new DownloadPage();
$page->Page();
?>