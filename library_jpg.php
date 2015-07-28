<?php 
require_once('init.php');

class LessonJPG extends Base
{	
	
	public function __construct()
	{	parent::__construct();
		$this->OutPutPDF();
	} // end of fn __construct
	
	function OutPutPDF()
	{	$book = new Multimedia($_GET['id']);
		if ($book->id && $book->HasImage() && ($filename = $book->ImageFile()))
		{	if ($fhandle = fopen($filename, 'r'))
			{	header('Pragma: ');
				header('Cache-Control: ');
				header('Content-Type: ' . ($_GET['fd'] ? 'application/octet-stream' : 'image/jpeg'));
				header('Content-Disposition: attachment; filename="' . $book->JPGViewName() . '"');
				fpassthru($fhandle);
				fclose($fhandle);
				exit;
			}
		}
		echo 'pdf not found';
	} // end of fn OutPutPDF
	
} // end of class LessonJPG

$page = new LessonJPG();
?>