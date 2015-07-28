<?php
class AdminMailListPage extends AdminPage
{	var $startdate = '';
	var $enddate = '';
	var $regname = '';
	
	function __construct()
	{	parent::__construct('MEMBERS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('members'))
		{	
			$this->MailListLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function MailListLoggedInConstruct()
	{	$this->regname = $_GET['regname'];
		// set up dates
		if (($ys = (int)$_GET['ystart']) && ($ds = (int)$_GET['dstart']) && ($ms = (int)$_GET['mstart']))
		{	$this->startdate = $this->datefn->SQLDate(mktime(0,0,0,$ms, $ds, $ys));
		} else
		{	$this->startdate = $this->datefn->SQLDate(strtotime('-4 weeks'));
		}
		
		if (($ys = (int)$_GET['yend']) && ($ds = (int)$_GET['dend']) && ($ms = (int)$_GET['mend']))
		{	$this->enddate = $this->datefn->SQLDate(mktime(0,0,0,$ms, $ds, $ys));
		} else
		{	$this->enddate = $this->datefn->SQLDate();
		}
	} // end of fn MailListLoggedInConstruct
	
	function MailListBody()
	{	
	} // end of fn MailListBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('members'))
		{	$this->MailListBody();
		}
	} // end of fn AdminBodyMain
	
	public function GetMailList()
	{	$maillist = array();
		$where = array();
		
		if ($this->startdate)
		{	$where[] = 'registered>="' . $this->startdate . ' 00:00:00"';
		}
		
		if ($this->enddate)
		{	$where[] = 'registered<="' . $this->enddate . ' 23:59:59"';
		}
		
		if ($regname = $this->SQLSafe($this->regname))
		{	$where[] = '(listname LIKE "%' . $regname . '%" OR listemail LIKE "%' . $regname . '%")';
		}
		
		$sql = 'SELECT * FROM maillist';
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY registered DESC';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$maillist[$row['mlid']] = $row;
			}
		}
		return $maillist;
	} // end of fn GetMailList
	
} // end of defn AdminMailListPage
?>