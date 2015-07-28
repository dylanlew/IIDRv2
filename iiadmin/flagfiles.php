<?php
include_once('sitedef.php');

class FlagFilesPage extends AdminPage
{	var $flagfiles = array();

	function __construct()
	{	parent::__construct("ADMIN");
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('technical'))
		{	$this->GetFlagFiles();
			
			if ($_POST['files_saved'])
			{	$this->Save($_POST['flagfiles']);
			}
			
			$this->breadcrumbs->AddCrumb('flagfiles.php', 'Flag Files');
		}
	} // end of fn LoggedInConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("technical"))
		{	echo $this->InputForm();
		}
	} // end of fn AdminBodyMain
	
	public function Save($files_set = array())
	{	$success = array();
		
		foreach ($this->flagfiles as $ffid=>$flagfile)
		{	$exists = file_exists($filepath = CITDOC_ROOT . '/flagfiles/' . $flagfile['ffname']);
			if ($files_set[$ffid])
			{	if (!$exists)
				{	// then create
					if ($fhandle = fopen($filepath, 'w'))
					{	fputs($fhandle, $flagfile['ffdesc']);
						fclose($fhandle);
						$success[] = $this->InputSafeString($flagfile['ffdesc']) . ' added';
					}
				}
			} else
			{	if ($exists)
				{	// then delete
					if (@unlink($filepath))
					{	$success[] = $this->InputSafeString($flagfile['ffdesc']) . ' removed';
					}
				}
			}
		}
		
		if ($success)
		{	$this->GetFlagFiles();
			$this->successmessage = implode('<br />', $success);
		}
		
	} // end of fn Save
	
	private function InputForm()
	{	ob_start();
		$form = new Form($_SERVER['SCRIPT_NAME']);
		$form->AddHiddenInput('files_saved', '1');
		foreach ($this->flagfiles as $ffid=>$flagfile)
		{	$form->AddCheckBox($this->InputSafeString($flagfile['ffdesc']), 'flagfiles[' . $ffid . ']', 1, $this->FlagFileSet($flagfile['ffname']));
		}
		$form->AddSubmitButton('&nbsp;', 'Save Changes', 'submit');
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	private function GetFlagFiles()
	{	$this->flagfiles = array();
		$sql = 'SELECT * FROM flagfiles ORDER BY adminorder';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->flagfiles[$row['ffid']] = $row;
			}
		}
	} // end of fn GetFlagFiles
	
} // end of defn FlagFilesPage

$page = new FlagFilesPage();
$page->Page();
?>