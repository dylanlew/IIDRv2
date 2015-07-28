<?php 
require_once('init.php');
$course_id = $_GET['course_id'];
$course = new Course($course_id);
function getIcalDate($time, $incl_time = true)
{
    return $incl_time ? date('Ymd\THis\Z', $time) : date('Ymd', $time);
}

	header("Content-Type: text/Calendar");
	header("Content-Disposition: inline; filename=".$course->details['cslug']. "-" .getIcalDate(strtotime($course->details['starttime']), false) .".ics");
	echo "BEGIN:VCALENDAR\n";
	echo "VERSION:2.0\n";
	echo "PRODID:-//Islamic Institute For Development & Research //NONSGML IIDR//EN\n";
	echo "X-WR-TIMEZONE:Europe/London\n";
	echo "METHOD:REQUEST\n"; // requied by Outlook
	echo "BEGIN:VEVENT\n";
	echo "UID:".date('Ymd').'T'.date('His')."-".rand()."-iidr.org\n"; // required by Outlok
	echo "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
	echo "DTSTART:".getIcalDate(strtotime($course->details['starttime']), false)."\n";
	echo "DTEND:".getIcalDate(strtotime($course->details['endtime']), false)."\n";
	echo "SUMMARY:".$course->details['ctitle']."\n";
	echo "DESCRIPTION: ".$course->details['cshortoverview']."\n";
	echo "LOCATION: ".$course->details['ctime'].", ".$course->GetVenue()->GetAddress(false)."\n";
	echo "END:VEVENT\n";
	echo "END:VCALENDAR\n";
			
?>