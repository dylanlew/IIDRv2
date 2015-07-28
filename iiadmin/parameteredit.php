<?php
include_once('sitedef.php');

class ParameterEditPage extends AdminPage
{	var $parameters = Object;
	var $group = '';
	var $groups = array();

	function __construct()
	{	parent::__construct('CMS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->breadcrumbs->AddCrumb('parameteredit.php', 'Parameters');
		//	$this->css[] = 'parameteredit.css';
			$this->parameters = new Parameters();
			$this->group = $_GET['group'];
			$this->groups = $this->parameters->ParameterGroups();
			if (count($this->groups) == 1)
			{	foreach ($this->groups as $group=>$count)
				{	$this->group = $group;
				}
			}
			if (count($_POST) && $this->user->CanUserAccess('web content'))
			{	$this->Save();
			}
		}
	} // end of fn LoggedInConstruct
	
	function ParametersSubMenu()
	{	if (count($this->groups) > 1)
		{	echo '<ul id="menu">';
			foreach ($this->groups as $group=>$count)
			{	echo '<li><a href="parameteredit.php?group=', $group, '">', $group ? $group : '&lt; not grouped &gt;', ' (', $count, ')</a></li>';
			}
			echo '</ul>';
		} else print_r($this->groups);
	} // end of fn ParametersSubMenu
	
	function Save()
	{	$fail = array();
		foreach ($this->parameters->details as $field=>$line)
		{	if ($line->details['pargroup'] == $this->group)
			{	$value = $_POST[$field];
				$linefail = array();
				if ($value)
				{	switch ($line->fieldvalid)
					{	case 'PHONE': if (!$this->ValidPhoneNumber($value)) $linefail[] = 'invalid phone number for ' . $field;
										break;
						case 'EMAIL': if (!$this->ValidEMail($value)) $linefail[] = 'invalid email for ' . $field;
										break;
					}
				}
				if ($linefail)
				{	$fail[] = implode(', ', $linefail);
				} else
				{	if ($line->Save($value))
					{	$this->successmessage = 'changes saved';
					}
				}
			}
		}
		
		if ($this->successmessage)
		{	$this->parameters->GetDetails();
		}
		
		$this->failmessage = implode('<br />', $fail);
		
		return false;
	} // end of fn Save
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	$this->ParametersSubMenu();
			echo '<div style="float: left;">';
			$this->parameters->InputForm($this->group);
			echo '</div><div class="clear"></div>';
		}
	} // end of fn AdminBodyMain
	
} // end of defn ParameterEditPage

$page = new ParameterEditPage();
$page->Page();
?>