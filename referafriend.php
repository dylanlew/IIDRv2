<?php
require_once('init.php');
header('location:' . CIT_FULLLINK . 'referrals.php');
exit;

class ReferAFriendPage extends AccountPage
{	private $referafriend;

	function __construct()
	{	parent::__construct();
		$this->AddBreadcrumb('Refer a friend');
		$this->css[] = 'page.css';
	} // end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct('refer');
		
		$this->referafriend = new ReferAFriend();
		
		if (isset($_POST['referemail']))
		{	$saved = $this->referafriend->Create($this->user, $_POST);
			$this->failmessage = $saved['failmessage'];
			$this->successmessage = $saved['successmessage'];
		}
		
	} // end of fn LoggedInConstruct

	public function RedirectToRegister()
	{	$this->Redirect('register.php');
	} // end of fn RedirectToRegister
	
	function LoggedInMainBody()
	{	$page = new PageContent('refer-a-friend-form');
		if ($html = $page->HTMLMainContent())
		{	echo '<div class="the-content">', $html, '</div>';	
		}
		echo '<div id="referForm">', $this->referafriend->InputForm($_POST), '<div class="clear"></div></div>';
	} // end of fn LoggedInMainBody
	
} // end of defn ReferAFriendPage

$page = new ReferAFriendPage();
$page->Page();
?>