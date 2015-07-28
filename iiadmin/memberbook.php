<?php
include_once("sitedef.php");

class MemberBookPage extends MemberPage
{	var $booking;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	parent::AKMembersLoggedInConstruct();
		$this->member_option = 'bookings';

		$this->booking = new CourseBooking();
		
		if (!$this->member->details['country'] || $this->user->CanAccessCountry($this->member->details['country']))
		{	if (isset($_POST['pmt_option']))
			{	$course = new Course($_POST['courseid']);
				if ($course->id)
				{	$postdata = $_POST;
					if ($postdata['discount_code'])
					{	$discount = new Discount();
						$discount->GetFromCode($postdata['discount_code']);
						$postdata['bdiscount'] = $discount->id;
					}
					$saved = $this->booking->CreateNew($course, $this->member, $postdata);
					$this->failmessage = $saved['fail'];
					$this->successmessage = $saved['success'];
					
					if ($this->booking->id)
					{	// send email to member
						$this->booking->RecordAdminCreation();
						$this->booking->SendEmail();
						$this->RecordAdminAction(array('tablename'=>'bookings', 'tableid'=>$this->booking->id, 'area'=>'bookings', 'action'=>'created'));
						$this->RedirectBack('member.php?id=' . $this->member->id);
					} // if booking succeeded
				} else
				{	$this->failmessage = 'course not selected';
				}
			} // if booking attempted
		}
		
		$this->js[] = 'admin_newbooking.js';
		
		$this->breadcrumbs->AddCrumb('memberbookings.php?id=' . $this->member->id, 'bookings');
		$this->breadcrumbs->AddCrumb('memberbook.php?id=' . $this->member->id, 'creating new booking');
	} // end of fn AKMembersLoggedInConstruct
	
	public function MemberViewBody()
	{	parent::MemberViewBody();
		echo $this->member->NewBookingForm();
	} // end of fn MemberViewBody
	
} // end of defn MemberBookPage

$page = new MemberBookPage();
$page->Page();
?>