<?php 
require_once('init.php');

class LessonMP3 extends Base
{	
	
	public function __construct()
	{	parent::__construct();
		$this->OutPutMP3();
	} // end of fn __construct
	
	function OutPutMP3()
	{	$book = new Multimedia($_GET['id']);
		if ($book->id && $filename = $book->MP3Location())
		{	if ($fhandle = fopen($filename, 'r'))
			{	header('Pragma: ');
				header('Cache-Control: ');
				header('Content-Type: ' . ($_GET['fd'] ? 'application/octet-stream' : 'audio/mpeg'));
				header('Content-Disposition: attachment; filename="' . $book->ViewName('mp3') . '"');
				header('Content-length: ' . filesize($filename));
				set_time_limit(0);
				while(!feof($fhandle) and (connection_status()==0))
				{	print(fread($fhandle, 1024*8));
					flush();
				}
		//		fpassthru($fhandle);
				fclose($fhandle);
				exit;
			}
		}
		echo 'mp3 not found';
	} // end of fn OutPutMP3
	
} // end of class LessonMP3

$page = new LessonMP3();
?>