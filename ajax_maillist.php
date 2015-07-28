<?php
require_once('init.php');

class AJAXMailList extends Base
{
	public function __construct()
	{
		parent::__construct();	
		
		$json = array();
		
		$m = new MailList;
		
		$saved = $m->Save($_POST);
		
		if($saved['successmessage'])
		{
			$json['status'] = 1;
			$json['message'] = $saved['successmessage'];
		}
		else
		{
			$json['status'] = 0;
			$json['message'] = $saved['failmessage'];
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
}

$page = new AJAXMailList;

?>