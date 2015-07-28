<?php
class StudentComments extends Base
{	public $sctype = '';
	public $parentid = 0;
	public $comments = array();
	
	public function __construct($sctype = '', $parentid = 0)
	{	parent::__construct();
		$this->sctype = $sctype;
		$this->parentid = (int)$parentid;
		$this->Get();
	} // fn __construct
	
	public function Get($liveonly = true)
	{	$this->comments = array();
		$sql = 'SELECT * FROM studentcomments WHERE sctype="' . $this->SQLSafe($this->sctype) . '" AND parentid=' . (int)$this->parentid;
		if ($liveonly)
		{	$sql .= ' AND suppressed=0';
		}
		$sql .= ' ORDER BY postdate DESC, scid ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$this->comments[$row['scid']] = $row;
			}
		}
	} // end of fn Get

	public function OutputList($limit = 0)
	{	ob_start();
		if ($this->comments)
		{	echo '<ul>';
			foreach($this->comments as $comment_row)
			{	if ((++$count > $limit) && $limit)
				{	echo '<li class="revListMore lastReview"><a onclick="GetMoreReviews(', $this->parentid, ',\'', $this->sctype, '\',', $limit + $this->reviewperpage, ');">see earlier comments</a></li>';
					break;
				}
				$comment = new StudentComment($comment_row);
				$author = $comment->GetAuthor();
				echo '<li', $count == count($this->comments) ? ' class="lastReview"' : '', '>',
				//	'<span class="rating"><span style="width:', $rating = (int)($comment->details['rating'] * 100), '%">', $rating, '%</span></span>',
					'<p>', nl2br($this->InputSafeString($comment->details['commenttext'])), '</p><p class="author">', $this->AgoDateString(strtotime($comment->details['postdate'])), ' by ', $this->InputSafeString($author->GetName()), '</p></li>';
			}
			echo '</ul>';
		} else
		{	echo '<p>No comments left yet</p>';
		}
		return ob_get_clean();
	} // end of fn OutputList
	
} // end of class defn StudentComments
?>