<?php
include_once('sitedef.php');

class EMailUsePage extends CMSPage
{	var $areas = array();
	var $em_userlist = array();
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CMSLoggedInConstruct()
	{	
		$this->breadcrumbs->AddCrumb('emailuse.php', 'EMail Useage');
		
		$this->GetUsers();
		$this->GetAreas();
		
		if (($delarea = $_GET['delarea']) &&  ($deluser = (int)$_GET['deluser']) && (int)$_GET['delconfirm'])
		{	$this->Delete($delarea, $deluser);
		}
		
		if (($area = $_POST['areaname']) && ($userid = (int)$_POST['userid']) && $this->user->CanUserAccess('web content'))
		{	$this->Save($area, $userid);
		}
	} // end of fn CMSLoggedInConstruct
	
	function GetAreas()
	{	$this->areas = array();
		if ($result = $this->db->Query('SELECT * FROM emailareas ORDER BY areadesc'))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->areas[$row['areaname']] = $row['areadesc'];
			}
		}
		
	} // end of fn GetAreas
	
	function GetUsers()
	{	$this->em_userlist = array();
		if ($result = $this->db->Query('SELECT * FROM adminusers ORDER BY ausername'))
		{	while ($row = $this->db->FetchArray($result))
			{	if ($this->ValidEMail($row['email']))
				{	$adminuser = new AdminUser((int)$row['auserid'], 1);
					$this->em_userlist[$row['auserid']] = $adminuser->fullname . ' (' . $adminuser->email . ')';
				}
			}
		}
		
	} // end of fn GetAreas
	
	function Delete($area = 0, $userid = 0)
	{	$sql = 'DELETE FROM emailsforarea WHERE areaname="' . $area . '" AND userid=' . $userid;
		$this->db->Query($sql);
	} // end of fn Delete
	
	function Save($area = 0, $userid = 0)
	{	
		if ($this->areas[$area] && $this->em_userlist[$userid])
		{	$sql = 'INSERT INTO emailsforarea SET areaname="' . $area . '", userid=' . $userid;
			$result = $this->db->Query($sql);
		} else
		{	$this->failmessage = 'invalid area and/or recipient';
		}

		return false;
	} // end of fn Save
	
	function CMSBodyMain()
	{	if ($this->user->CanUserAccess('administration'))
		{	$this->ListExisting();
			$this->InputForm();
		}
	} // end of fn CMSBodyMain
	
	function ListExisting()
	{	$existing = array();
		$sql = 'SELECT adminusers.*, emailareas.* FROM emailareas, emailsforarea, adminusers WHERE  emailareas.areaname=emailsforarea.areaname AND emailsforarea.userid=adminusers.auserid ORDER BY areadesc, surname, firstname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$existing[] = $row;
			}
		} else echo '<p>', $this->db->Error(), '</p>';
		
		if ($existing)
		{	echo '<table class="emailuse"><tr><th>area</th><th>sends to ...</th><th></th></tr>';
			foreach ($existing as $line)
			{	$adminuser = new AdminUser($line['auserid'], 1);
				$confirm = ($_GET['delarea'] == $line['areaname']) && ($_GET['deluser'] == $line['auserid']);
				echo '<tr class="stripe', $i++ % 2, '"><td>', $line['areadesc'], '</td><td><a href="useredit.php?userid=', $line['auserid'], '">', $adminuser->fullname, '</a> (', $adminuser->email, ')</td><td><a href="', $_SERVER['SCRIPT_NAME'], '?delarea=', $line['areaname'], '&deluser=', $line['auserid'], $confirm ? '&delconfirm=1' : '', '">', $confirm ? 'confirm you want to ' : '', 'delete this email use</a></td></tr>'; 
			}
			echo '</table>';
		} else
		{	echo '<p>no emails currently in use on site</p>';
		}
		
	} // end of fn ListExisting
	
	function InputForm()
	{	
		$regform = new Form($_SERVER['SCRIPT_NAME'], 'regform');
		$regform->AddSelect('area to send email', 'areaname', '', '', $this->areas, true);
		$regform->AddSelect('email sent to', 'userid', '', '', $this->em_userlist, true);
		$regform->AddSubmitButton('', 'Add this', 'submit');
		$regform->Output();
	
	} // end of fn InputForm
	
} // end of defn EMailUsePage

$page = new EMailUsePage();
$page->Page();
?>