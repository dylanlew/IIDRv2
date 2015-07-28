<?php 
require_once('init.php');

class CheckoutGift extends CheckoutPage
{	private $attendees = array();
	protected $bc_to_show = array('course_reg'=>true);
		
	function __construct()
	{	parent::__construct();
		
		$this->js[] = 'store_gift.js';
		
		$this->next_stage = 'checkout-address.php';
		
		if (!$this->user-id || $this->GetStage() < 2)
		{	$this->RedirectToPreviousStage();	
		}
		
		$this->HandleSubmit();
		if (!$this->attendees = $this->cart->GetAttendeeProducts())
		{	$this->RedirectToNextStage();	
		}
	} // end of fn __construct
	
	public function HandleSubmit()
	{	
		if (isset($_POST['att_email']))
		{	$saved = $this->cart->UpdateAttendees($_POST);
			$this->failmessage = $saved['failmessage'];
			$this->successmessage = $saved['successmessage'];
			
			if (!$this->failmessage)
			{	$this->RedirectToNextStage();	
			}
		}
	} // end of fn HandleSubmit
	
	public function MainBodyContent()
	{	echo $this->AttendeesDisplayList();
	} // end of fn MainBodyContent
	
	public function AttendeesDisplayList()
	{	ob_start();
		$courses = array();
		echo '<div id="checkoutGiftContainer"><h2>Please give details for all actual course participants</h2><form action="" method="post"><ul class="attendee_listing">';
		foreach ($this->attendees as $rowid => $ticket_row)
		{	$ticket = new CourseTicket($ticket_row['id']);
			if (!$courses[$ticket->details['cid']])
			{	$courses[$ticket->details['cid']] = new Course($ticket->details['cid']);
			}
			echo '<li><h3>', $this->InputSafeString($courses[$ticket->details['cid']]->content['ctitle']), ', ', $courses[$ticket->details['cid']]->DatesDisplay('j M Y'), '<br />Ticket type: ', $this->InputSafeString($ticket->details['tname']), ' &times; ', $ticket_row['qty'], '</h3><ul>';
			foreach ($ticket_row['attendees'] as $att_id=>$attendee)
			{	$key = $rowid . '|' . $att_id;
				echo '<li><div class="al_email"><span>Email address</span><input type="text" name="att_email[', $key, ']" value="', $this->InputSafeString($attendee['att_email']), '" /></div><div class="al_fname"><span>First name</span><input type="text" name="att_firstname[', $key, ']" value="', $this->InputSafeString($attendee['att_firstname']), '" /></div><div class="al_sname"><span>Surname</span><input type="text" name="att_surname[', $key, ']" value="', $this->InputSafeString($attendee['att_surname']), '" /></div><div class="clear"></div></li>';
			}
			echo '</ul></li>';
		}
		echo '</ul><p><input type="submit" name="submit_gift" value="Continue" class="button-link checkout-link" /></p></form><div class="clear"></div></div>';
		return ob_get_clean();
	} // end of fn AttendeesDisplayList

} // end of defn CheckoutLoginRegister

$page = new CheckoutGift();
$page->Page();
?>