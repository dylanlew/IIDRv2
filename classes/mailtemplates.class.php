<?php
class MailTemplates extends Base
{
	public $templates = array();
	
	public function __construct()
	{	parent::__construct();
		$this->Get();
	} // end of fn __construct
	
	public function Get()
	{	$this->Reset();
		$sql = "SELECT * FROM mailtemplates ORDER BY mailname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->templates[] = $this->AssignTemplate($row);
			}
		}
	} // end of fn Get
	
	public function Reset()
	{	$this->templates = array();
	} // end of fn Reset
	
	public function AssignTemplate($row = array())
	{	return new AdminMailTemplate($row);
	} // end of fn AssignTemplate
	
	function AdminList()
	{	ob_start();
		echo "<table><tr><th>Template</th><th>Subject</th><th>Actions</th></tr>";
		foreach ($this->templates as $template)
		{	
			echo "<tr class='stripe", $i++ % 2, "'>\n<td>", $this->InputSafeString($template->details["mailname"]), "</td>\n<td>", $this->InputSafeString($template->details["subject"]), "</td>\n<td><a href='mailtedit.php?id=", $template->id, "'>edit</a>";
			if ($histlink = $this->DisplayHistoryLink("mailtemplates", $template->id))
			{	echo "&nbsp;|&nbsp;", $histlink;
			}
			echo "</td>\n</tr>\n";
		}
		echo "</table>\n";
		
		return ob_get_clean();
	} // end of fn AdminList
	
} // end of class MailTemplates

?>