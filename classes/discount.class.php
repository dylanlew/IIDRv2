<?php
class Discount extends BlankItem
{	
	function __construct($id = 0)
	{	parent::__construct($id, "discounts", "discid");
	} // fn __construct
	
	function GetFromCode($code = "")
	{	$this->Reset();
		$sql = "SELECT * FROM discounts WHERE disccode='" . $this->SQLSafe($code) . "'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	$this->Get($row);
			}
		}
		
	} // end of fn GetFromCode
	
	function AppliesToCourse($course = 0)
	{	
		if (!$this->details["courseid"] && ($this->details["country"] === ""))
		{	return true;
		}
		
		if (is_a($course, "Course"))
		{	if ($this->details["courseid"] == $course->id)
			{	return true;
			}
		} else
		{	if ($this->details["courseid"] == $course)
			{	return true;
			}
			if ($this->details["country"])
			{	$course = new Course($course);
			}
		}
		
		if ($this->details["country"])
		{	return $this->details["country"] === $course->details["country"];
		}
		
		return false;
		
	} // end of fn AppliesToCourse
	
	function StillValid()
	{	if (!$this->id)
		{	return false;
		}
		if ((int)$this->details["lastdate"])
		{	if (strtotime($this->details["lastdate"] . " 23:59:59") < time())
			{	return false;
			}
		}
		if ((int)$this->details["maxuses"])
		{	if ($this->details["maxuses"] <= $this->BookingsCount())
			{	return false;
			}
		}
		return true;
	} // end of fn StillValid
	
	function BookingsCount()
	{	if ($id = (int)$this->id)
		{	$sql = "SELECT COUNT(bookid) AS bookcount FROM bookings WHERE discount=" . $this->id;
			if ($result = $this->db->Query($sql))
			{	if ($row = $this->db->FetchArray($result))
				{	return $row["bookcount"];
				}
			}
		}
		return 0;
	} // end of fn BookingsCount
	
	function ApplyToPrice($price = 0)
	{	
		if ($this->details["discpc"])
		{	return round(($price * (100 - $this->details["discpc"])) / 100, 2);
		}
		if ($this->details["discamount"])
		{	if ($this->details["discamount"] >= $price)
			{	return 0;
			} else
			{	return round($price - $this->details["discamount"], 2);
			}
		}
		return $price;
	} // end of fn ApplyToPrice
	
} // end of defn Discount
?>