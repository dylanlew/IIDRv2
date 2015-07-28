<?php
include_once('init.php');
class MailListPage extends PagePage
{
	function __construct()
	{	parent::__construct('mailing-list');
		$this->css[] = 'contactus.css';
		
		if (isset($_POST['listname']))
		{	$saved = $this->Save($_POST);
			$this->failmessage = $saved["failmessage"];
			if ($this->successmessage = $saved["successmessage"])
			{	unset($_POST);
			}
		}
		
	} // end of fn __construct
	
	function Save($data = array())
	{	$fields = array();
		$fail = array();
		$success = array();
		
		if ($listname = $this->SQLSafe($data['listname']))
		{	$fields[] = 'listname="' . $listname . '"';
		} else
		{	$fail[] = 'You must give us your name';
		}
		
		if ($listemail = $data['listemail'])
		{	if ($this->ValidEMail($listemail))
			{	// check already registered
				if (!$mlid = $this->AlreadyRegistered($listemail))
				{	$fields[] = 'registered="' . $this->datefn->SQLDateTime() . '"';
				}
				$fields[] = 'listemail="' . $listemail . '"';
			} else
			{	$fail[] = 'Your email is not valid, please check your typing';
			}
		} else
		{	$fail[] = 'Your must give us your email address to add yourself to our mailing list';
		}
		
		if (!$fail && ($set = implode(', ', $fields)))
		{	if ($mlid = (int)$mlid)
			{	$sql = 'UPDATE maillist SET ' . $set . ' WHERE mlid=' . $mlid;
			} else
			{	$sql = 'INSERT INTO maillist SET ' . $set;
			}
			if ($result = $this->db->Query($sql))
			{	$success[] = 'You have been added to our mailing list';
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	public function AlreadyRegistered($listemail  = '')
	{	$mlid = 0;
		$sql = 'SELECT mlid FROM maillist WHERE listemail="' . $this->SQLSafe($listemail) . '"';
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['mlid'];
			}
		}
	} // end of fn AlreadyRegistered
	
	function MainBodyContent()
	{	
		echo "<div class='course-content-wrapper'><div class='page-sidebar'><div id='psMenuContainer'>", $this->page->SideBarMenu($this->page->details["pagename"]), $this->FBSidebar(), "</div></div><div class='page-content'><h2>", $this->InputSafeString($this->page->details["pagetitle"]), "</h2>", $this->page->HTMLMainContent(), $this->ContactForm(), "</div><div class='clear'></div></div>";
	} // end of fn MemberBody
	
	function ContactForm()
	{	ob_start();
		echo '<form method="post" action="', SITE_URL, $_SERVER["SCRIPT_NAME"], '"><p><label>Your name</label><input type="text" name="listname" value="', $this->InputSafeString($_POST['listname']), '" /></p><p><label>Your email address</label><input type="text" name="listemail" value="', $this->InputSafeString($_POST['listemail']), '" /></p><p><label></label><input type="submit" class="submit" value="Add yourself to our mailing list" /></p></form>';
		return ob_get_clean();
	} // end of fn ForgottenForm
	
} // end of defn MailListPage

$page = new MailListPage();
$page->Page();
?>