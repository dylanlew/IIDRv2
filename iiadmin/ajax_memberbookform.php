<?php
include_once("sitedef.php");

class AjaxPayBooking extends AdminCoursesPage
{	

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		echo "<!--bookform-->", $this->BookForm();
		
	} // end of fn CoursesLoggedInConstruct
	
	function BookForm()
	{	ob_start();
		//print_r($_GET);
		$course = new AdminCourse($_REQUEST["courseid"]);
		$user = new AdminStudent($_REQUEST["userid"]);
		$price = round($_REQUEST["price"], 2);
		if ($user->AlreadyBooked($course->id))
		{	
			echo "<div class='failmessage'>", $this->InputSafeString($user->details["firstname"]), " ", $this->InputSafeString($user->details["surname"]), " is already booked on this course (", $this->InputSafeString($course->content->details["ctitle"]), ", ", $course->OutputLocation(), " - ", $course->DateString(), ")</div>";
		} else
		{
			if ($_REQUEST["save"])
			{	// the try to save
				$postdata = array("pmt_option"=>$_POST["pmtmethod"]);
				
				$booking = new Booking();
				$saved = $booking->CreateNew($course, $user, $postdata);
				$this->failmessage = $saved["fail"];
				$this->successmessage = $saved["success"];
				if ($booking->id)
				{	$booking->ChangePrice($price);
					$booking->AddAdminNotes($_POST["adminnotes"]);
					$this->RecordAdminAction(array("tablename"=>"bookings", "tableid"=>$booking->id, "area"=>"bookings", "action"=>"created"));
					$booking->RecordAdminCreation();
					if (($pmtamount = round($_POST["pmtamount"], 2)) && ($balance = $booking->BalanceToPay()))
					{	if ($pmtamount > $balance)
						{	$pmtamount = $balance;
						}
						$sql = "INSERT INTO bookingpmts SET bookid={$booking->id}, amount=$pmtamount, currency='{$booking->details["currency"]}', paydate='" . $this->datefn->SQLDateTime() . "', paynotes='{recorded at other course with booking}'";
						if ($result = $this->db->Query($sql))
						{	if ($this->db->AffectedRows() && ($newid = $this->db->InsertID()))
							{	$this->RecordAdminAction(array("tablename"=>"bookingpmts", "tableid"=>$newid, "area"=>"booking payments", "action"=>"created (with booking)"));
							}
						}
						$booking->GetPayments();
					}
					$booking->SetExpectedFlag();
					$booking->SendEmail();
					echo "<div class='successmessage'>", $this->InputSafeString($user->details["firstname"]), " ", $this->InputSafeString($user->details["surname"]), " has been booked on course \"", $this->InputSafeString($course->content->details["ctitle"]), "\" in ", $course->OutputLocation(), " on ", $course->DateString(), "</div>";
					return;
				} else
				{	echo "<div class='failmessage'>", $this->failmessage, "</div>\n";
				}
			}
			echo "<form class='sb_form' onsubmit='jsSaveBooking();return false;'>\n<input type='hidden' id='sb_userid' value='", $user->id, "' /><input type='hidden' id='sb_courseid' value='", $course->id, "' />\n<label>Book member ...</label><span>", $this->InputSafeString($user->details["firstname"]), " ", $this->InputSafeString($user->details["surname"]), " (email: ", $user->details["username"], ")</span><br />\n<label>... on course</label><span>", $this->InputSafeString($course->content->details["ctitle"]), ", ", $course->OutputLocation(), "<br />", $course->DateString(), "</span><br />\n<label>Price ", $course->CurrencySymbol(), "</label><input type='text' class='short' id='sb_price' value='", number_format($price, 2), "' /><br />\n<label>Amount paid</label><input type='text' class='short' id='sb_paidamount' value='", number_format($_POST["pmtamount"], 2), "' /><br />\n<label>Payment method</label>\n<select id='sb_pmtmethod'>\n<option>-- select payment method --</option>\n";
			$pmtoptions = new PaymentOptions($course->details["country"]);
			foreach ($pmtoptions->options as $option)
			{	
				if (!$option->details["suppress"])
				{	
					echo "<option value='", $option->id, "'", $option->id == $_POST["pmtmethod"] ? " selected='selected'" : "", ">", $this->InputSafeString($option->details["optname"]), "</option>\n";
				}
			}
			echo "</select><br />\n<label>Notes</label>\n<textarea id='sb_adminnotes' cols='50' rows='5'>", stripslashes($_POST["adminnotes"]), "</textarea>\n<br />\n<label>&nbsp;</label><input type='submit' class='submit' value='Create Booking' /><br />\n</form>\n";
		}
		return ob_get_clean();
	} // end of fn BookForm
	
} // end of defn AjaxPayBooking

$page = new AjaxPayBooking();
?>