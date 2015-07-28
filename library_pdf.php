<?php 
require_once('init.php');

class LessonPDF extends Base
{	
	
	public function __construct()
	{	parent::__construct();
		$this->OutPutPDF();
	} // end of fn __construct
	
	function OutPutPDF()
	{	$book = new Multimedia($_GET['id']);
		if ($book->id && $book->PDFExists() && ($filename = $book->PDFLocation()))
		{	if ($fhandle = fopen($filename, 'r'))
			{	header('Pragma: ');
				header('Cache-Control: ');
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $book->PDFViewName() . '"');
				fpassthru($fhandle);
				fclose($fhandle);
				exit;
			}
		}
		echo 'pdf not found';
	} // end of fn OutPutPDF
	
} // end of class LessonPDF

$page = new LessonPDF();
?>