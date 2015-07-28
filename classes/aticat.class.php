<?php
class ATICat extends BlankItem
{		
	public function __construct($id = 0)
	{	parent::__construct($id, 'atiqcats', 'catid');
	} // end of fn __construct
	
	public function GetQuestions($liveonly = true)
	{	$questions = array();
		$sql = 'SELECT asktheimam.* FROM asktheimam,  atitocats WHERE asktheimam.askid=atitocats.askid AND atitocats.catid=' . $this->id;
		if ($liveonly)
		{	$sql .= ' AND asktheimam.publish=1';
		}
		$sql .= ' ORDER BY asktheimam.answertime';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$questions[$row['askid']] = $row;
			}
		}
		return $questions;
	} // end of fn GetQuestions
	
} // end of class ATICat
?>