<?php
include_once('sitedef.php');

class MemberDetailsPage extends MemberPage
{	var $member;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$this->member_option = 'refer';
		
		$this->breadcrumbs->AddCrumb('memberreferrals.php?id=' . $this->member->id, 'Refer-a-Friend');

	} // end of fn AKMembersLoggedInConstruct
	
	public function MemberViewBody()
	{	parent::MemberViewBody();
		$this->ReferralsList();
	} // end of fn MemberViewBody
	
	function ReferralsList()
	{	if ($referrals = $this->member->GetReferrals())
		{	echo '<table class="myacList"><tr><th>Sent to</th><th>Referred</th><th>Reward</th><th>Created</th><th>Used by referrer</th></tr>';
		
			foreach ($referrals as $referral_row)
			{	
				$referral = new ReferAFriend($referral_row);
				//$this->VarDump($referral);
				echo '<tr><td>', $this->InputSafeString($referral->details['refername']), ' (', $this->InputSafeString($referral->details['referemail']), ')</td><td>', date('d M Y', strtotime($referral->details['refertime'])), '</td>';
				if ($reward_row = $referral->GetRewardForUser($this->member->id))
				{	$reward = new ReferAFriendReward($reward_row);
					echo '<td>&pound;', number_format($reward->details['amount'], 2), '</td><td>', date('d M Y', strtotime($reward->details['created'])), '</td><td>';
					$used_amount = 0;
					$lines = array();
					if ($used = $reward->GetUsed())
					{	foreach ($used as $use)
						{	$lines[] = '&pound;' . number_format($use['usedamount'], 2) . ' on ' . date('d M Y', strtotime($use['usedtime']));
						}
					}
					if ($used_amount < $reward->details['amount'])
					{	$lines[] = 'use by ' . date('d M Y', strtotime($reward->details['expires']));
					}
					echo implode('<br />', $lines), '</td>';
				} else
				{	echo '<td class="refTableNoReward">not generated</td><td></td><td></td>';
				}
				echo '</tr>';
			}

			echo '</table>';
		}
	} // end of fn ReferralsList
	
} // end of defn MemberDetailsPage

$page = new MemberDetailsPage();
$page->Page();
?>