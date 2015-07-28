<?php
class AdminAKDuplicatesPage extends AdminAKMembersPage
{	
	function __construct()
	{	parent::__construct("MEMBERS");
	} //  end of fn __construct
	
	function UserDetailsArrayFromUser($user)
	{	if (is_a($user, 'Student'))
		{	return $user->details;
		} else
		{	if (is_array($user))
			{	return $user;
			}
		}
		return array();
	} // end of fn UserDetailsArrayFromUser
	
	function UserAddressText($user)
	{	$address = array();
		$userdetails = $this->UserDetailsArrayFromUser($user);
		if ($userdetails['address'])
		{	$address[] = nl2br($this->InputSafeString($userdetails['address']));
		}
		if ($userdetails['city'])
		{	$address[] = $this->InputSafeString($userdetails['city']);
		}
		if ($userdetails['phone'])
		{	$address[] = 'phone: ' . $this->InputSafeString($userdetails['phone']);
		}
		if ($userdetails['phone2'])
		{	$address[] = 'phone: ' . $this->InputSafeString($userdetails['phone2']);
		}
		return implode('<br />', $address);
	} // end of fn UserAddressText
	
	function UserBookingList($user)
	{	$booklist = array();
		$userdetails = $this->UserDetailsArrayFromUser($user);
		$bookings = new Bookings($userdetails['userid']);
		foreach ($bookings->bookings as $bookingrow)
		{	$booking = new AdminBooking($bookingrow, false);
			$booklist[] = $this->InputSafeString($booking->course->content->details["ctitle"]) . ' - ' . date("d/m/y", strtotime($booking->course->details["starttime"])) . ', ' . $this->InputSafeString($this->CityString($booking->course->details["city"]));
		}
		return implode('<br />', $booklist);
	} // end of fn UserBookingList
	
} // end of defn AdminAKDuplicatesPage
?>