<?php
include_once('sitedef.php');

class AskImamListPage extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function AskImamBody()
	{	
		echo '<table><tr class="newlink"><th colspan="9"><a href="askimamtopic.php">Create new theme</a></th></tr><tr><th></th><th>Title</th><th>Start date</th><th>Instructor(s)</th><th>Categories</th><th>Questions</th><th>Live?</th><th>Closed?</th><th>Actions</th></tr>';
		foreach ($this->GetTopics() as $topic_row)
		{	$topic = new AdminAskImamTopic($topic_row);
			echo '<tr><td>';
			if (file_exists($topic->GetImageFile('thumbnail')))
			{	echo '<img src="', $topic->GetImageSRC('thumbnail'), '" />';
			} else
			{	echo 'no photo';
			}
			echo '</td><td>', $this->InputSafeString($topic->details['title']), '</td><td>', date('d/m/y', strtotime($topic->details['startdate'])), '</td><td>', $topic->InstructorsListDisplay(),' </td><td>', $topic->CategoryListDisplay(),' </td><td>', count($topic->questions), '</td><td>', $topic->details['live'] ? 'live' : '', '</td><td>', $topic->details['closed'] ? 'closed' : '', '</td><td><a href="askimamtopic.php?id=', $topic->id, '">edit</a>';
			if ($topic->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="askimamtopic.php?id=', $topic->id, '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	} // end of fn AskImamBody
	
	public function GetTopics()
	{	$topics = array();
		$sql = 'SELECT * FROM askimamtopics ORDER BY startdate DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$topics[] = $row;
			}
		}
		return $topics;
	} // end of fn FunctionName
	
} // end of defn AskImamListPage

$page = new AskImamListPage();
$page->Page();
?>