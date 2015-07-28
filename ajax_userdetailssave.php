<?php 
require_once('init.php');

class AjaxUserSavePage extends DashboardPage
{	var $data = array();

	function __construct($data)
	{	$this->data = $data;
		parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	
		$saved = $this->user->SaveDetails($_POST);
		if ($saved['fail'])
		{	echo '<!--fail--><div class="failmessage">', $this->InputSafeString($saved['fail']), '</div>';
		} else
		{	echo '<!--success-->', $this->InputSafeString($fail['success']);
			$this->user->RecordDetailsConfirmed();
			$this->RecordConfirmScreenResult('success', false);
		}

	} // end of fn LoggedInConstruct
	
	protected function RecordFailedConfirmSCreen(){}
	
} // end of class AjaxUserSavePage

$page = new AjaxUserSavePage($_POST);
?>