<?php
class ZipDownload
{	protected $zipdir = '';
	protected $tempdir = '';
	protected $filestozip = array();

	public function __construct($create_dir = true)
	{	$this->zipdir = CITDOC_ROOT . '/zipdl/';
		if ($create_dir)
		{	while (file_exists($this->tempdir = $this->zipdir . 'temp' . time() . ++$i)){}
			mkdir($this->tempdir);
		}
	//	chmod($this->tempdir);
	} // end of fn __construct
	
	public function AddToZipFiles($file = '', $filename = '')
	{	if ($this->tempdir)
		{	if (copy($file, $this->tempdir . '/' . $filename))
			{	$this->filestozip[] = $filename;
				return true;
			}
		}
	} // end of fn AddToZipFiles
	
	public function DownloadZipFile($filename = '')
	{	if ($this->tempdir)
		{	$zipfile = $this->tempdir . '/' . $filename;
			exec('zip -j ' . $zipfile . ' ' . $this->tempdir . '/*', $output);
			
			// delete all files
			foreach ($this->filestozip as $rawfilename)
			{	@unlink($this->tempdir . '/' . $rawfilename);
			}
			
			if ($fhandle = fopen($zipfile, 'r'))
			{	header("Pragma: ");
				header("Cache-Control: ");
				header("Content-Type: application/zip ");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-length: " . filesize($zipfile));
				set_time_limit(0);
				while(!feof($fhandle) and (connection_status()==0))
				{	print(fread($fhandle, 1024*8));
					flush();
				}
				fclose($fhandle);
				@unlink($zipfile);
				@rmdir($this->tempdir);
				return true;
			}
		}
	} // end of fn DownloadZipFile
	
} // end of defn ZipDownload
?>