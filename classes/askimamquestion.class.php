<?php
class AskImamQuestion extends BlankItem
{	
	public function __construct($id = null)
	{	parent::__construct($id, 'askimamquestions', 'qid');
	} // fn __construct
	
	public function GetTopic()
	{	return new AskImamTopic($this->details['askid']);
	} // end of fn GetTopic
	
	public function GetMultiMedia($liveonly = true)
	{	$multimedia = array();
		
		$sql = 'SELECT multimedia.* FROM multimedia, askimam_mm WHERE multimedia.mmid=askimam_mm.mmid AND askimam_mm.qid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND multimedia.live=1';
		}
		$sql .= ' ORDER BY multimedia.mmorder, multimedia.posted';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$multimedia[$row['mmid']] = $row;
			}
		}
		
		return $multimedia;
	} // end of fn GetMultiMedia
	
	public function OutputAnswer($userid = 0)
	{	ob_start();
		if ($this->details['qanswer'])
		{	echo '<div class="qanswerText">', stripslashes($this->details['qanswer']), '</div>';
		}
		if ($mmlist = $this->GetMultiMedia())
		{	foreach ($mmlist as $mm_row)
			{	$mm = new Multimedia($mm_row);
				echo '<div class="qanswerMM">', $mm->Output(635, 380), '</div>';
			}
		}
		$comment = new StudentComment();
		$comments = new StudentComments('askimamquestions', $this->id);
		if ($this->CommentsOpen())
		{	echo '<div id="yourReviewContainer">', $comment->CreateForm($this->id, 'askimamquestions', $userid > 0), '</div><div class="clear"></div>';
		} else
		{	echo '<p>This question is now closed to further comments.</p>';
		}
		echo '<div id="prodReviewListContainer">', $comments->OutputList(2), '</div>';
		return ob_get_clean();
	} // end of fn OutputAnswer
	
	private function CommentsOpen()
	{	if (!$this->details['closed'])
		{	if (($topic = $this->GetTopic()) && $topic->id)
			{	return !$topic->details['closed'];
			}
		}
		return false;
	} // end of fn CommentsOpen
	
} // end of class AskImamQuestion
?>