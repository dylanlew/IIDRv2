<?php
class AdminBookingPmt extends BookingPmt
{	
	function __construct($id = "")
	{	parent::__construct($id);
	} // fn __construct
	
	function CanDelete()
	{	return $this->id && $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query("DELETE FROM bookingpmts WHERE payid=" . (int)$this->id))
			{	if ($this->db->AffectedRows())
				{	$this->RecordAdminAction(array("tablename"=>"bookingpmts", "tableid"=>$this->id, "area"=>"booking payments", "action"=>"deleted", "actiontype"=>"deleted", "deleteparentid"=>$this->details["bookid"], "deleteparenttable"=>"bookings"));
					$booking = new AdminBooking($this->details["bookid"]);
					$booking->SetExpectedFlag();
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete

	function Save($data = array(), $bookid = 0)
	{	$fail = array();
		$success = array();
		$admin_actions = array();
		
		$booking = new Booking($bookid);
		$fields = array("bookid=" . (int)$booking->id, "currency='" . $booking->details["currency"] . "'");
		
		if ($amount = round($data["amount"], 2))
		{	$fields[] = "amount=$amount";
			if ($this->id && ($amount != $this->details["amount"]))
			{	$admin_actions[] = array("action"=>"Amount", "actionfrom"=>$this->details["amount"], "actionto"=>$amount);
			}
		} else
		{	$fail[] = "zero amount cannot be paid, did you mean to delete the payment?";
		}
		
		if ($this->id)
		{	if (($d = (int)$data["dpaydate"]) && ($m = (int)$data["mpaydate"]) && ($y = (int)$data["ypaydate"]))
			{	$paydate = $this->datefn->SQLDate(mktime(0,0,0,$m, $d, $y)) . " " . $this->StringToTime($data["paytime"]) . ":00";
			} else
			{	$fail[] = "payment date can't be blank";
				$paydate = $this->details["paydate"];
			}
		} else
		{	$paydate= $this->datefn->SQLDateTime();
		}
		$fields[] = "paydate='$paydate'";
		if ($this->id && ($paydate != $this->details["paydate"]))
		{	$admin_actions[] = array("action"=>"Date", "actionfrom"=>$this->details["paydate"], "actionto"=>$paydate);
		}
		
		$fields[] = "paynotes='" . $this->SQLSafe($data["paynotes"]) . "'";
		if ($this->id && ($data["paynotes"] != $this->details["paynotes"]))
		{	$admin_actions[] = array("action"=>"Description text", "actionfrom"=>$this->details["paynotes"], "actionto"=>$data["paynotes"]);
		}
				
		if (($this->id || !$fail) && ($set = implode(", ", $fields)))
		{	
			if ($this->id)
			{	$sql = "UPDATE bookingpmts SET $set WHERE payid={$this->id}";
			} else
			{	$sql = "INSERT INTO bookingpmts SET $set";
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$base_parameters = array("tablename"=>"bookingpmts", "tableid"=>$this->id, "area"=>"booking payments");
						if ($admin_actions)
						{	foreach ($admin_actions as $admin_action)
							{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
							}
						}
						$success[] = "Changes saved";
						$this->Get($this->id);
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = "New payment created";
						$this->RecordAdminAction(array("tablename"=>"bookingpmts", "tableid"=>$this->id, "area"=>"booking payments", "action"=>"created"));
						$this->Get($this->id);
						$booking = new AdminBooking($this->details["bookid"]);
						$booking->SetExpectedFlag();
						if ($data["receipt"])
						{	$this->SendPaymentEmail();
						}
					}
				
				} else
				{	if (!$this->id)
					{	$fail[] = "Insert failed";
					}
				}
			}
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));
		
	} // end of fn Save
	
	function InputForm(AdminBooking $booking)
	{	
		ob_start();
		if (!$data = $this->details)
		{	if (!$data = $_POST)
			{	$data["receipt"] = 1;
			}
		}
		
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id . "&bookid=" . $booking->id);
		if ($data["pptransid"])
		{	$form->AddRawText("<label>PayPal Tx ID</label><span class='dataText'>{$data["pptransid"]}</span><br class='clear'/>\n");
		}
		$form->AddTextInput("Payment amount (" . $booking->Currency("cursymbol") . ")", "amount", number_format($data["amount"], 2, ".", ""), "short", 10, 1);
		if ($this->id) // for existing allow date change
		{	$form->AddMultiInput("Date / time of payment", array(
						array("type"=>"DATE", "name"=>"paydate", "value"=>$data["paydate"]), 
						array("type"=>"TEXT", "name"=>"paytime", "value"=>substr($data["paydate"], 11, 5), "css"=>"short", "maxlength"=>5)), 
					true);
		} else
		{	$form->AddCheckbox("Send email receipt", "receipt", "1", $data["receipt"]);
		}
		$form->AddTextArea("Notes", "paynotes", $this->InputSafeString($data["paynotes"]), "", 0, 0, $rows = 3, $cols = 40);
		$form->AddSubmitButton("", $this->id ? "Save Changes" : "Make Payment", "submit");
		if ($this->id)
		{	echo "<p>", $this->DisplayHistoryLink("bookingpmts", $this->id), "</p>";
		}
		if ($this->CanDelete())
		{	
			echo "<p><a href='", $_SERVER["SCRIPT_NAME"], "?id=", $this->id, "&delete=1", $_GET["delete"] ? "&confirm=1" : "", "'>", $_GET["delete"] ? "please confirm you really want to " : "", "delete this payment</a></p>\n";
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn AdminStaffCategory
?>