<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		$booking = new AdminBooking($_GET["id"]);
		$balance = round($booking->Price() - $booking->AmountPaid(), 2);
		if ($booking->id && !$booking->GoesToPayPal() && ($balance > 0))
		{	
			$sql = "INSERT INTO bookingpmts SET bookid={$booking->id}, amount=$balance, currency='{$booking->details["currency"]}', paydate='" . $this->datefn->SQLDateTime() . "', paynotes='{recorded by admin from booking list}'";
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows() && ($newid = $this->db->InsertID()))
				{	$booking->GetPayments();
					$booking->SetExpectedFlag();
					$this->RecordAdminAction(array("tablename"=>"bookingpmts", "tableid"=>$newid, "area"=>"booking payments", "action"=>"created (one touch)"));
				}
				echo "<!--paybooking-->", $booking->BookingListPaidUpContents();
			}//else echo "<p>", $this->db->Error(), "</p>\n";
			
		} else echo $booking->Price(), " - ", $booking->AmountPaid();
		
	} // end of fn CoursesLoggedInConstruct
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>