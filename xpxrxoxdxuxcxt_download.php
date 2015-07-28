<?php 
require_once('init.php');

class ProductDownloader extends Base
{	
	public function __construct()
	{	parent::__construct();
		$this->OutPutDownload();
	} // end of fn __construct
	
	function OutPutDownload()
	{	
		$download = new StoreProductDownload($_GET['id']);
		if ($download->id && $download->FileExists() && ($filename = $download->FileLocation()) && $download->CanDownload(new Student($_SESSION['stuserid'])))
		{	if ($fhandle = fopen($filename, 'r'))
			{	//echo $download->valid_types[$download->details['filetype']]['Content-Type'];
				//exit;
				header('Pragma: ');
				header('Cache-Control: ');
				header('Content-Type: ' . $download->valid_types[$download->details['filetype']]['Content-Type']);
				header('Content-Disposition: attachment; filename="' . $download->DownloadName() . '"');
				header('Content-length: ' . filesize($filename));
				set_time_limit(0);
				while(!feof($fhandle) and (connection_status()==0))
				{	print(fread($fhandle, 1024*8));
					flush();
				}
			//	fpassthru($fhandle);
				fclose($fhandle);
				exit;
			}
		}
		echo 'file not found';
	} // end of fn OutPutDownload
	
} // end of class ProductDownloader

$page = new ProductDownloader();
?>