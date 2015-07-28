<?php
include_once("sitedef.php");

class SiteEmailEditPage extends AdminAKMembersPage
{	var $email;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AKMembersLoggedInConstruct()
	{	$this->email  = new SiteEmail($_GET["id"]);
		$this->css[] = "sitemail_inc.php";

	} // end of fn AKMembersLoggedInConstruct
	
	function Page() // display actual page
	{	$this->HTMLHeader();
		$this->DisplayTitle();
		echo "<body>\n";
		$this->AKMembersBody();
		echo "</body>\n</html>\n";
	} // end of fn Page
	
	function AKMembersBody()
	{	switch ($_GET["disp"])
		{	case "header": echo $this->email->header;
							break;
			case "body": echo stripslashes($this->email->details["mailbody"]);
							break;
			case "footer": echo $this->email->Footer();
							break;
		//	case "full": echo $this->email->header, stripslashes($this->email->details["mailbody"]), $this->email->Footer();
			case "full": echo stripslashes($this->email->details["mailbody"]), $this->email->Footer();
							break;
		}
	} // end of fn AKMembersBody
	
} // end of defn SiteEmailEditPage

$page = new SiteEmailEditPage();
$page->Page();
?>