<?php
include_once('sitedef.php');

class AskTheImamListingPage extends AskTheImamPage
{	
	private $answer_options = array(0=>array('display'=>'Unanswered only', 'sql'=>'answeredby=0'), 1=>array('display'=>'All questions', 'sql'=>''), 2=>array('display'=>'Answered only', 'sql'=>'answeredby>0'));
	private $order_options = array(0=>array('display'=>'as submitted', 'sql'=>'askedtime ASC'), 1=>array('display'=>'most recent', 'sql'=>'askedtime DESC'));
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function ATIConstructor()
	{	parent::ATIConstructor();
	} // end of fn ATIConstructor
	
	public function ATIMainContent()
	{	echo $this->FilterForm(), $this->ListQuestions();
	} // end of fn ATIMainContent

	public function FilterForm()
	{	ob_start();
		echo '<form class="akFilterForm" method="get" action="', $_SERVER['SCRIPT_NAME'], '"><span>show</span><select name="ansopt">';
		foreach ($this->answer_options as $option=>$text)
		{	echo '<option value="', $option, '"', $option == $_GET['ansopt'] ? ' selected="selected"' : '', '>', $this->InputSafeString($text['display']), '</option>';
		}
		echo '</select><span>order by</span><select name="orderby">';
		foreach ($this->order_options as $value=>$option)
		{	echo '<option value="', $value, '"', $value == $_GET['orderby'] ? ' selected="selected"' : '', '>', $this->InputSafeString($option['display']), '</option>';
		}
		echo '</select><input type="submit" class="submit" value="Apply Filter" /><div class="clear"></div></form><div class="clear"></div>';
		return ob_get_clean();
	} // end of fn FilterForm
	
	public function ListQuestions()
	{	ob_start();
		if ($questions = $this->GetQuestions())
		{	
			echo '<table><tr><th>Submitted</th><th>by</th><th>Question</th><th>Answered?</th><th>Published?</th><th>Categories</th><th>Actions</th></tr>';
			foreach ($questions as $question_row)
			{	$ati = new AdminAskTheImam($question_row);
				echo '<tr><td>', date('d-M-y @H:i', strtotime($question_row['askedtime'])), '</td><td>', $this->InputSafeString($question_row['username']), '<br />', $this->InputSafeString($question_row['useremail']);
				if ($question_row['userid'] && ($student = new Student($question_row['userid'])) && $student->id)
				{	
					echo '<br />Student: <a href="member.php?id=', $student->id, '">', trim($this->InputSafeString($student->details['firstname'] . ' ' . $student->details['surname'])), '</a>';
				}
				echo '</td><td>', $this->ShortText($question_row['asked']), '</td><td class="atiTableAnswer">';
				if ($question_row['answeredby'])
				{	$adminuser = new AdminUser($question_row['answeredby']);
					echo '<h4>by: ';
					if ($this->CanAdminUser('administration'))
					{	echo '<a href="useredit.php?userid=', $adminuser->userid, '">', $adminuser->username, '</a>';
					} else
					{	echo $adminuser->username;
					}
					echo '</h4>', $this->ShortText($question_row['answer']);
				} else
				{	echo 'no';
				}
				echo '</td><td>', $question_row['publish'] ? 'Yes' : 'No', '</td><td>', $ati->CatsList(), '</td><td><a href="ati_question.php?id=' . $question_row['askid'] . '">', $question_row['answeredby'] ? 'view' : 'respond', '</a></td></tr>';
			}
			echo '</table>';
		} else
		{	echo '<p>No questions found</p>';
		}
		return ob_get_clean();
	} // end of fn ListQuestions
	
	public function GetQuestions()
	{	$questions = array();
		$where = array();
		
		if (($ansopt = $this->answer_options[(int)$_GET['ansopt']]) && $ansopt['sql'])
		{	$where[] = $ansopt['sql'];
		}
		
		$sql = 'SELECT * FROM asktheimam';
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		if (($order = $this->order_options[(int)$_GET['orderby']]) && $order['sql'])
		{	$sql .= ' ORDER BY ' . $order['sql'];
		}
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$questions[$row['askid']] = $row;
			}
		} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		return $questions;
	} // end of fn GetQuestions

} // end of defn AskTheImamListingPage

$page = new AskTheImamListingPage();
$page->Page();
?>