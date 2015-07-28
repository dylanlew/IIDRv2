<?php
include_once("sitedef.php");

class PPAccountsListPage extends AccountsMenuPage
{	
	function __construct()
	{	parent::__construct();
		$this->css[] = "adminctry.css";
	} //  end of fn __construct
	
	function AccountsLoggedInConstruct()
	{	parent::AccountsLoggedInConstruct();
		$this->breadcrumbs->AddCrumb("ppaccounts.php", "Paypal Accounts");
	} // end of fn AccountsLoggedInConstruct
	
	function AccountsBody()
	{	$this->AccountList();
	} // end of fn AccountsBody
	
	function AccountList()
	{	echo "<table><tr class='newlink'><th colspan='3'><a href='ppedit.php'>Create new account</a></th></tr>\n<tr><th>Account name</th><th>Secure Merchant ID</th><th>Actions</th></tr>";
		foreach ($this->Accounts() as $account)
		{	
			echo "<tr class='stripe", $i++ % 2, "'>\n<td>", $this->InputSafeString($account->details["username"]), "</td>\n<td>", $this->InputSafeString($account->details["businessid"]), "</td>\n<td><a href='ppedit.php?id=", $account->id, "'>edit</a>";
			if ($histlink = $this->DisplayHistoryLink("ppaccounts", $account->id))
			{	echo "&nbsp;|&nbsp;", $histlink;
			}
			if ($account->CanDelete())
			{	echo "&nbsp;|&nbsp;<a href='ppedit.php?id=", $account->id, "&delete=1'>delete</a>";
			}
			echo "</td>\n</tr>\n";
		}
		echo "</table>\n";
	} // end of fn CtryList
	
	function Accounts()
	{	$accounts = array();
		
		$sql = "SELECT * FROM ppaccounts ORDER BY username";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$accounts[] = new AdminPaypalAccount($row);
			}
		}
		
		return $accounts;
	} // end of fn Accounts
	
} // end of defn PPAccountsListPage

$page = new PPAccountsListPage();
$page->Page();
?>