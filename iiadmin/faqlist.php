<?php
include_once('sitedef.php');

class FAQListingPage extends AdminFAQPage
{	
	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function FAQMainContent()
	{	echo $this->FilterForm(), $this->ListQuestions();
	} // end of fn FAQMainContent

	public function FilterForm()
	{	ob_start();
		echo '<form class="akFilterForm" method="get" action="', $_SERVER['SCRIPT_NAME'], '"><span>text search</span><input type="text" name="searchtext" class="long" value="', $this->InputSafeString($_GET['searchtext']), '" />';
		if (($faqcats = new FAQCats(true, false)) && $faqcats->cats)
		{	echo '<span>category</span><select name="cat"><option value="">-- all --</option>';
			foreach ($faqcats->cats as $catid=>$cat)
			{	echo '<option value="', $catid, '"', $catid == $_GET['cat'] ? ' selected="selected"' : '', '>', $this->InputSafeString($cat['catname']), '</option>';
			}
			echo '</select>';
		}
		echo '<input type="submit" class="submit" value="Apply Filter" /><div class="clear"></div></form><div class="clear"></div>';
		return ob_get_clean();
	} // end of fn FilterForm
	
	public function ListQuestions()
	{	ob_start();
		
		echo '<table><tr class="newlink"><th colspan="8"><a href="faq.php">New FAQ</a></th></tr><tr><th>Created</th><th>Question</th><th>Answer</th><th>Categories</th><th>Ask the<br />Imam?</th><th>Live?</th><th>List order</th><th>Actions</th></tr>';
		foreach ($this->GetQuestions() as $question_row)
		{	$faq = new AdminFAQ($question_row);
			echo '<tr><td>', date('d-M-y @H:i', strtotime($question_row['created'])), '</td><td>', $this->ShortText($question_row['question']), '</td><td>', $this->ShortText($question_row['answer']), '</td><td>', $faq->CatsList(), '</td><td>';
			if ($question_row['askid'])
			{	echo '<a href="ati_question.php?id=', $question_row['askid'], '">View</a>';
			}
			echo '</td><td>', $question_row['live'] ? 'Yes' : '', '</td><td>', (int)$question_row['listorder'], '</td><td><a href="faq.php?id=' . $question_row['faqid'] . '">edit</a>';
			if ($faq->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="faq.php?id=' . $question_row['faqid'] . '&delete=1">delete</a>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn ListQuestions
	
	public function GetQuestions()
	{	$questions = array();
		$where = array();
		$tables = array('faq');
		
		if ($_GET['cat'])
		{	$tables[] = 'faqtocats';
			$where[] = 'faqtocats.faqid=faq.faqid';
			$where[] = 'faqtocats.catid=' . (int)$_GET['cat'];
		}
		
		if ($searchtext = $this->SQLSafe($_GET['searchtext']))
		{	$where[] = '(question LIKE "%' . $searchtext . '%" OR answer LIKE "%' . $searchtext . '%")';
		}
		
		$sql = 'SELECT faq.* FROM ' . implode(',', $tables);
		if ($wstr = implode(' AND ', $where))
		{	$sql .= ' WHERE ' . $wstr;
		}
		$sql .= ' ORDER BY faq.listorder, faq.created';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$questions[$row['faqid']] = $row;
			}
		} else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		return $questions;
	} // end of fn GetQuestions

} // end of defn FAQListingPage

$page = new FAQListingPage();
$page->Page();
?>