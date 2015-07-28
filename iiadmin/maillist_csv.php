<?php
include_once('sitedef.php');

class MailListCSV extends AdminMailListPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function MailListBody()
	{	header("Pragma: ");
		header("Cache-Control: ");
		header("Content-Type: application/csv;charset=UTF-8");
		header("Content-Disposition: attachment; filename=\"iidr_mailing_list.csv\"");
		echo "name,email,registered date,registered time\n";
		foreach ($this->GetMailList() as $memberrow)
		{	
			echo "\"", $this->CSVSafeString($memberrow["listname"]), "\",\"", $this->CSVSafeString($memberrow["listemail"]), "\",\"", date("d/m/Y", strtotime($memberrow["registered"])), "\",\"", date("H:i", strtotime($memberrow["registered"])), "\"\n";
		}
	} // end of fn MailListBody
	
} // end of defn MailListCSV

$page = new MailListCSV();
$page->AdminBodyMain();
?>