<?php 
class FAQPage extends BasePage
{	protected $category;
	protected $perpage = 20;
		
	function __construct()
	{	parent::__construct('faqs');
		$this->category = new FAQCat($_GET['catid']);
	} // end of fn __construct
	
	protected function DisplayCategoryList()
	{	ob_start();
		$faqcats = new FAQCats();
		if ($faqcats->cats)
		{	echo '<h2>Categories</h2><ul>';
			foreach($faqcats->cats as $c)
			{	$cat = new FAQCat($c);
				echo '<li', $c['catid'] == $this->category->id ? ' class="current-subpage"' : '', '><a href="', $cat->Link(), '">', $this->InputSafeString($c['catname']), '</a></li>';	
			}
			echo '<li', !$this->category->id ? ' class="current-subpage"' : '', '><a href="', SITE_SUB, '/faq/">show all questions</a></li></ul></div>';
		}
		return ob_get_clean();
	} // end of fn DisplayCategoryList
	
	public function QuestionsList()
	{	ob_start();
		if ($questions = ($this->category->id ? $this->category->GetFAQ() : $this->GetAllQuestions()))
		{	
			if ($_GET['page'] > 1)
			{	$start = ($_GET['page'] - 1) * $this->perpage;
			} else
			{	$start = 0;
			}
			$end = $start + $this->perpage;
			
			foreach($questions as $q)
			{	if (++$count > $start)
				{	if ($count > $end)
					{	break;
					}
					
					$faq = new FAQ($q);
					echo '<div class="question clearfix" onclick="ToggleAnswer(this);"><h3><a>', nl2br($this->InputSafeString($faq->details['question'])), '</a></h3><div class="answer">', nl2br($this->InputSafeString($faq->details['answer'])), '</div></div>';
				}
			}
			
			if (count($questions) > $this->perpage)
			{	$pag = new AjaxPagination($_GET['page'], count($questions), $this->perpage, 'faqContainer', 'ajax_faq.php', $_GET);
				echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
			}
		} else
		{	echo '<p>No questions.</p>';	
		}
		return ob_get_clean();
	} // end of fn QuestionsList
	
	public function GetAllQuestions()
	{	$questions = array();
		$sql = 'SELECT * FROM faq WHERE live=1 ORDER BY listorder, created';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$questions[$row['faqid']] = $row;
			}
		}
		return $questions;
	} // end of fn GetAllQuestions
	
} // end of defn FAQPage
?>