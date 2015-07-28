<?php 
require_once('init.php');

class AjaxReferrals extends AccountPage
{	
	function __construct()
	{	parent::__construct();
	} // end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		
		switch($_GET['action'])
		{	case 'sharepopupfill':
				switch ($_GET['filltype'])
				{	case 'email':
						echo $this->user->GetAffilateRecord()->SharePopupEmailForm();
						break;
					case 'sharelink':
						echo $this->user->GetAffilateRecord()->SharePopupLinkForm();
						break;
					case 'list':
					default:
						echo $this->user->GetAffilateRecord()->SharePopupList();
				}
				break;
			case 'emailsend':
				$alreadyRegistered = $this->user->GetByEmail($_POST['email']);
				
				if(count($alreadyRegistered)>0){
					echo '<div class="spListFailMessage">Given friend already registered.</div>', $this->user->GetAffilateRecord()->SharePopupEmailForm($_POST);
				}else{
					$aff = $this->user->GetAffilateRecord();
					$save = $aff->SendToEmail($_POST);
					if ($save['success'])
					{	echo $aff->ShareListBackLink(), '<div class="spListSuccessMessage">', $save['success'], '</div>';
					} else
					{	if ($save['fail'])
						{	echo '<div class="spListFailMessage">', $save['fail'], '</div>', $aff->SharePopupEmailForm($_POST);
						}
					}
				}
				break;
		}
		
	} // end of fn LoggedInConstruct
	
} // end of defn AjaxReferrals

$page = new AjaxReferrals();
?>