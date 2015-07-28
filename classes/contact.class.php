<?php
class Contact extends Base
{	var $failmessage = "";
	var $data = array();

	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function InputData($datakey = "")
	{	return $this->InputSafeString($this->data[$datakey]);
	} // end of fn InputData
	
	function Validate($data = array())
	{	$this->data = $data;
		$this->fail = array();
		
		if ($this->data["email"] || $this->data["phone"])
		{	if ($this->data["email"] && !$this->ValidEMail($this->data["email"]))
			{	$this->fail[] = "the email address you provided is not valid";
			}
			
			if ($this->data["phone"] && !$this->ValidPhoneNumber($this->data["phone"]))
			{	$this->fail[] = "the phone number you provided is not valid";
			}
		} else
		{	$this->fail[] = "you must provide either an email address or a phone number";
		}
		
		
		return !count($this->fail);
	} // end of fn Validate
	
	function Send($data)
	{
		if ($this->Validate($data))
		{	$subject = 'New Enquiry - ' . SITE_NAME;
		
			$message  = "You have received a new enquiry via the " . SITE_NAME . " website \n";
			$message .= "--------------------------------------------------------------------------------\n\n";
			$message .= "Name: {$this->data['name']} \n";
			$message .= "Email: {$this->data['email']} \n";
			$message .= "Query: \n{$this->data['query']} \n\n";
			$message .= "--------------------------------------------------------------------------------\n\n";
			
			$headers = "Content-type: text/plain; charset=iso-8859-1 \n";
			$headers .= "From: ". $this->data['email'] ."\n";
			$headers .= "X-Mailer: PHP". phpversion() ."\n\n";
			
			if(mail($this->GetParameter("fwemail"), $subject, $message, $headers))
			{	return true;
			}
		}
		
		return false;
	} // end of fn Send
	
	function SentConfirmation()
	{	return "send succeeded";
	} // end of fn SentConfirmation
	
	function SentErrorMessage()
	{	return "We were unable to submit your enquiry: " . implode(", ", $this->fail);
	} // end of fn SentErrorMessage
	
	function ContactDetails()
	{	$details = array();
		
		if ($email = $this->GetParameter("compemail"))
		{	$details[] = array("label"=>"Email us", "text"=>$email);
		}
		if ($phone = $this->GetParameter("compphone"))
		{	$details[] = array("label"=>"Phone us", "text"=>$phone);
		}
		if ($address = $this->GetParameter("compaddress"))
		{	$details[] = array("label"=>"Address", "text"=>nl2br($this->InputSafeString($address)));
		}
			
		return $details;
	} // end of fn ContactDetails
	
} // end of defn Contact
?>