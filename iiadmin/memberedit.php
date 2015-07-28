<?php
include_once('sitedef.php');

class MemberDetailsPage extends MemberPage
{	var $member;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$this->member_option = 'edit';
		
		if (!$this->member->details['country'] || $this->user->CanAccessCountry($this->member->details['country']))
		{	if (isset($_POST['username']))
			{	$saved = $this->member->SaveDetails($_POST, $adminactions = true);
				$this->successmessage = $saved['success'];
				$this->failmessage = $saved['fail'];
			}
		}
		
		if ($this->member->id)
		{	$this->breadcrumbs->AddCrumb('memberedit.php?id=' . $this->member->id, 'editing profile');
		} else
		{	$this->breadcrumbs->AddCrumb('memberedit.php?id=' . $this->member->id, 'creating new member');
		}
	} // end of fn AKMembersLoggedInConstruct
	
	public function MemberViewBody()
	{	parent::MemberViewBody();
		echo $this->member->AdminInputForm();
	} // end of fn MemberViewBody
	
} // end of defn MemberDetailsPage

$page = new MemberDetailsPage();
$page->Page();
?>