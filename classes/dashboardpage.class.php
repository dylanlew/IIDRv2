<?php 
class DashboardPage extends BasePage
{	
	function __construct($pageName = '')
	{	if (!$pageName)
		{	$pageName = 'my-account';
		}
		
		parent::__construct($pageName);
		
		if ($this->user->id)
		{	$this->LoggedInConstruct();
		} else
		{	//$this->Redirect('register.php');
		}
		
	} // end of fn __construct

	function LoggedInConstruct()
	{	
		$this->css[] = 'editprofile.css';
		if (isset($_POST['username']))
		{	$saved = $this->user->SaveDetails($_POST);
			$this->failmessage = $saved['fail'];
			$this->successmessage = $saved['success'];
			if (!$saved['fail'])
			{	$this->user->RecordDetailsConfirmed();
				$this->RecordConfirmScreenResult("success", false);
			}
		}

	} // end of fn LoggedInConstruct
	
	function MainBodyContent()
	{	if ($this->user->id)
		{	echo $this->LoggedInMainBody();
		} else
		{	parent::MainBodyContent();
		}
	} // end of fn MemberBody

	function MainBodyBanner()
	{	if ($this->user->id)
		{	return parent::MainBodyBanner();
		}
	} // end of fn MainBodyBanner
	
	function LoggedInMainBody()
	{	ob_start();
		$resources = array();
		$exams = array();
		$bookings = new Bookings($this->user->id, $future = true);
		$pastbookings = new Bookings($this->user->id, $future = false, $pastonly = true);
		foreach ($pastbookings->bookings as $bookingrow)
		{	$course = new Course($bookingrow['course']);
			if ($course->ResourcesAvailable($this->user))
			{	$resources[] = $bookingrow;
			}
			if ($course->ExamsAvailable($this->user))
			{	$exams[] = $bookingrow;
			}
		}
		foreach ($bookings->bookings as $bookingrow)
		{	$course = new Course($bookingrow['course']);
			if ($course->ResourcesAvailable($this->user))
			{	$resources[] = $bookingrow;
			}
		}
	//	$this->user->SendRegEmail();
	//	$this->user->ImportedRegEmail();
		echo '<div class="course-content-wrapper"><div class="course-sidebar"><ul class="vertical-tabs">';
		$tabs = array();
		if ($bookings->bookings)
		{	$tabs['future'] = $this->GetTranslatedText('smu_comingup');
		}
		$tabs['edit'] = $this->GetTranslatedText('smu_details');
		if ($pastbookings->bookings)
		{	$tabs['bookings'] = $this->GetTranslatedText('smu_previous');
		}
		if ($resources)
		{	$tabs['portal'] = $this->GetTranslatedText('smu_portal');
		}
		if ($exams)
		{	$tabs['exams'] = $this->GetTranslatedText('smu_exams');
		}
		$scholarships = $this->user->ScholarshipsUseable();
		if ($scholarships)
		{	$tabs['scholarships'] = $this->GetTranslatedText('smu_schol');
		}

		foreach ($tabs as $tab=>$text)
		{	echo '<li><a href="#tab-', $tab, '" id="tab-', $tab, '-selector"', $tab == $_GET['tab'] ? ' class="selected"' : '', '>', $text, '</a></li>';
		}

		echo '</ul></div><div class="course-content">';
		if ($bookings->bookings)
		{	echo '<div id="tab-future">';
			foreach ($bookings->bookings as $bookingrow)
			{	$booking = new Booking($bookingrow);
				if (!$heading++)
				{	$booking->UserListHeading();
				}
				$booking->UserListLine();
			}
			if ($heading)
			{	echo '</table>';
			}
			echo '</div>';
		}
		echo '<div id="tab-edit">', $this->user->EditDetailsForm('dashboard.php?tab=edit'), '</div>';
		if ($pastbookings->bookings)
		{	echo '<div id="tab-bookings">';
			foreach ($pastbookings->bookings as $bookingrow)
			{	$booking = new Booking($bookingrow);
				if (!$pbheading++)
				{	$booking->UserListHeading();
				}
				$booking->UserListLine();
			}
			if ($pbheading)
			{	echo '</table>';
			}
			echo '</div>';
		}
		if ($resources)
		{	
			echo "<div id='tab-portal'>\n<table class='course-booking'>\n<tr><th></th><th>", ucwords($this->GetTranslatedText("coursename_label")), "</th><th>", ucwords($this->GetTranslatedText("date_label")), "</th><th>", ucwords($this->GetTranslatedText("location_label")), "</th><th>", ucwords($this->GetTranslatedText("details_label")), "Details</th>\n</tr>\n";
			foreach ($resources as $bookingrow)
			{	$booking = new Booking($bookingrow);
				//$booking->UserListLine();
				echo "<tr>\n<td>";
				if (file_exists($booking->course->content->ThumbFile()))
				{	echo "<img src='", $booking->course->content->ThumbSRC(), "' width='40px' />";
				}
				echo "</td>\n<td><h4>", $this->InputSafeString($booking->course->content->details['ctitle']), "</h4>\n<p class='tagline'>", $this->InputSafeString($booking->course->content->details['tagline']), "</p>\n</td>\n<td>", $booking->course->DateString(), "</td>\n<td>", $booking->course->OutputLocation(), "</td>\n<td class='ts_enrol_link'><a href='booking-resources.php?id=", $booking->id, "'>", $this->GetTranslatedText("get_resources"), " &gt;</a></td></tr>\n";
			}
			echo "</table>\n</div>";
		}
		if ($exams)
		{	
			echo "<div id='tab-exams'>\n<table class='course-booking'>\n<tr><th></th><th>", ucwords($this->GetTranslatedText("coursename_label")), "</th><th>", ucwords($this->GetTranslatedText("date_label")), "</th><th>", ucwords($this->GetTranslatedText("location_label")), "</th><th>", ucwords($this->GetTranslatedText("details_label")), "</th>\n</tr>\n";
			foreach ($exams as $bookingrow)
			{	$booking = new Booking($bookingrow);
				echo "<tr>\n<td>";
				if (file_exists($booking->course->content->ThumbFile()))
				{	echo "<img src='", $booking->course->content->ThumbSRC(), "' width='40px' />";
				}
				echo "</td>\n<td><h4>", $this->InputSafeString($booking->course->content->details['ctitle']), "</h4>\n<p class='tagline'>", $this->InputSafeString($booking->course->content->details['tagline']), "</p>\n</td>\n<td>", $booking->course->DateString(), "</td>\n<td>", $booking->course->OutputLocation(), "</td>\n<td class='ts_enrol_link'><a href='booking-exams.php?id=", $booking->id, "'>", $this->GetTranslatedText("see_exam_details"), " &gt;</a></td></tr>\n";
			}
			echo "</table>\n</div>";
		}
		if ($scholarships)
		{	
			echo "<div id='tab-scholarships'>\n<table class='course-booking'>\n<tr><th></th><th>", ucwords($this->GetTranslatedText("coursename_label")), "</th><th>", ucwords($this->GetTranslatedText("date_label")), "</th><th>", ucwords($this->GetTranslatedText("location_label")), "</th><th>", ucwords($this->GetTranslatedText("details_label")), "</th>\n</tr>\n";
			foreach ($scholarships as $course)
			{	echo "<tr>\n<td>";
				if (file_exists($course->content->ThumbFile()))
				{	echo "<img src='", $course->content->ThumbSRC(), "' width='40px' />";
				}
				echo "</td>\n<td><h4>", $this->InputSafeString($course->content->details['ctitle']), "</h4>\n<p class='tagline'>", $this->InputSafeString($course->content->details['tagline']), "</p>\n</td>\n<td>", $course->DateString(), "</td>\n<td>", $course->OutputLocation(), "</td>\n<td class='ts_enrol_link'><a href='course_enrol.php?course=", $course->id, "'>", $this->GetTranslatedText("enrol_now_button"), " &gt;</a></td></tr>\n";
			}
			echo "</table>\n</div>";
		}
		echo "<script type='text/javascript'>\nmy_id_tabs = $('.course-sidebar').idTabs();\n</script>\n</div>\n<div class='clear'></div>\n</div>";
		return ob_get_clean();
	} // end of fn LoggedInMainBody
	
	function UserDetails()
	{	ob_start();
		print_r($this->user->details);
		echo "<div id='db_details'>\n<p><a href='editprofile.php'>edit your details</a></p>\n</div>\n";
		return ob_get_clean();
	} // end of fn UserDetails
	
	function BookingList($limit = 10)
	{	ob_start();
		$bookings = new Bookings($this->user->id, $future = true);
		$allbookings = new Bookings($this->user->id, $future = false);
		if ($bookings->bookings)
		{	foreach ($bookings->bookings as $bookingrow)
			{	if (++$count > $limit)
				{	break;
				}
				$booking = new Booking($bookingrow);
				if (!$heading++)
				{	$booking->UserListHeading();
				}
				$booking->UserListLine();
			}
			if ($heading)
			{	echo "</table>\n";
			}
			if ($count > $limit)
			{	echo "<p><a href='bookings.php'>", $this->GetTranslatedText("user_view_all"), "</a></p>\n";
			} else
			{	if (count($bookings->bookings) != count($allbookings->bookings))
				{	echo "<p><a href='bookings.php?past=1'>", $this->GetTranslatedText("user_view_previous"), "</a></p>\n";
				}
			}
		} else
		{	echo "<p>", $this->GetTranslatedText("user_nonecomingup"), "</p>\n";
			if ($allbookings->bookings)
			{	echo "<p><a href='bookings.php?past=1'>", $this->GetTranslatedText("user_view_previous"), "</a></p>\n";
			}
		}
		return ob_get_clean();
	} // end of fn BookingList

//	function MainBodyBanner()
//	{	ob_start();
//		echo "<p class='page-banner'><img src='img/banners/myaccount-banner.jpg' alt='' /></p>";
//		return ob_get_clean();
//	} // end of fn MainBodyBanner
	
} // end of defn DashboardPage
?>