<?php
class AskTheImamPage extends BasePage
{	protected $topic;
	protected $category;
	protected $instructor;
	protected $archive_perpage = 5;
	protected $questions_perpage = 10;
	protected $anchor_lead = '&raquo; ';
	
	function __construct()
	{	parent::__construct('asktheexpert');
	
		if ($_GET['topic'])
		{	$this->topic = new AskImamTopic($_GET['topic']);
		}
		
		$this->AssignFilter();
		$this->AssignLatestTopic();

		$this->AddBreadcrumb('Ask the Expert', SITE_SUB . '/asktheexpert.php');
		if ($this->category->id)
		{	$this->AddBreadcrumb($this->InputSafeString($this->category->details['ctitle']), $this->CategoryLink($this->category));
		} else
		{	if ($this->instructor->id)
			{	$this->AddBreadcrumb($this->InputSafeString($this->instructor->GetFullName()), $this->InstructorLink($this->instructor));
			} else
			{	if ($this->topic->id)
				{	foreach ($this->topic->cats as $cat_row)
					{	$cat = new CourseCategory($cat_row);
						$this->AddBreadcrumb($this->InputSafeString($cat->details['ctitle']), $this->CategoryLink($cat));
						break;
					}
					$this->AddBreadcrumb($this->InputSafeString($this->topic->details['title']), $this->topic->Link());
				}
			}
		}
	} // end of fn __construct
	
	protected function DisplayCatsLinks($cats = array(), $sep = ', ')
	{	$cat_links = array();
		foreach ($cats as $cat_row)
		{	$cat = new CourseCategory($cat_row);
			ob_start();
			echo '<a href="', $this->CategoryLink($cat), '">', $cat->CascadedName(), '</a>';
			$cat_links[] = ob_get_clean();
		}
		return implode($sep, $cat_links);
	} // end of fn DisplayCatsLinks
	
	public function InstructorLink($inst_row = array())
	{	if (is_a($inst_row, 'Instructor'))
		{	$inst_row = $inst_row->details;
		}
		return SITE_SUB . '/people/' . $inst_row['inid'] . '-' . $inst_row['instslug'] . '/';
	} // end of fn CategoryLink
	
	public function InstructorAskExpertLink($inst_row = array())
	{	if (is_a($inst_row, 'Instructor'))
		{	$inst_row = $inst_row->details;
		}
		return SITE_SUB . '/asktheexpert/instructor/' . $inst_row['inid'] . '/' . $inst_row['instslug'] . '/';
	} // end of fn InstructorAskExpertLink
	
	public function CategoryLink($cat_row = array())
	{	if (is_a($cat_row, 'CourseCategory'))
		{	$cat_row = $cat_row->details;
		}
		return SITE_SUB . '/asktheexpert/category/' . $cat_row['cid'] . '/' . $cat_row['catslug'] . '/';
	} // end of fn CategoryLink
	
	public function TopicLink($topic_row = array())
	{	if (is_a($topic_row, 'AskImamTopic'))
		{	$topic_row = $topic_row->details;
		}
		return SITE_SUB . '/asktheexpert/topic/' . $topic_row['askid'] . '/' . $topic_row['slug'] . '/';
	} // end of fn TopicLink
	
	protected function DisplayInstLinks($instlist = array())
	{	$inst_links = array();
		foreach ($instlist as $inst_row)
		{	ob_start();
			$inst = new Instructor($inst_row);
			echo '<a href="', $this->InstructorLink($inst), '">', $this->InputSafeString($inst->GetFullName()), '</a>';
			$inst_links[] = ob_get_clean();
		}
		return implode(', ', $inst_links);
	} // end of fn DisplayInstLinks
	
	protected function AssignFilter()
	{	
		if ($catid = (int)$_GET['catid'])
		{	$this->category = new CourseCategory($catid);
		} else
		{	if ($inid = (int)$_GET['inid'])
			{	$this->instructor = new Instructor($inid);
			}
		}
	} // end of fn AssignFilter
	
	protected function AssignLatestTopic()
	{	if (!$this->topic->id && !$this->category->id && !$this->instructor->id && ($latest = $this->GetLatestTopic()))
		{	$this->topic = new AskImamTopic($latest);
		}
	} // end of fn AssignLatestTopic

	protected function DisplayCatList($cats = array(), $catstodisp = array(), $parentid = 0)
	{	if ($catstodisp)
		{	ob_start();
			echo '<div class="sidebar-menu"><ul>';
			foreach ($catstodisp as $catid=>$cat)
			{	if ($cats[$catid] && ($cats[$catid]['parentcat'] == $parentid))
				{	echo '<li', $catid == $this->category->id ? ' class="current-subpage"' : '', '>';
					if (!$cats[$catid]['no_topics'])
					{	echo '<a href="', $this->CategoryLink($cats[$catid]), '">';
					//	echo '<a href="', $_SERVER['SCRIPT_NAME'], '?filter=cat&id=', $catid, '">';
					}
					echo $this->InputSafeString($cats[$catid]['ctitle']), $cats[$catid]['no_topics'] ? '' : '</a>', $this->DisplayCatList($cats, $cats[$catid]['subcats'], $catid), '</li>';
				}
			}
			echo '</ul></div>';
			return ob_get_clean();
		}
	} // end of fn DisplayCatList

	protected function DisplayInstructorsList()
	{	if ($instructors = $this->GetInstructorList())
		{	ob_start();
			echo '<div class="sidebar-menu"><ul>';
			foreach ($instructors as $inid=>$inst_row)
			{	$inst = new Instructor($inst_row);
				$classes = array();
				if ($inid == $this->instructor->id)
				{	$classes[] = 'current-subpage';
				}
				if (strlen(htmlspecialchars_decode(stripslashes($inst->GetFullName()))) > 19)
				{	$classes[] = 'menu_long';
				}
				echo '<li';
				if ($classes)
				{	echo ' class="', implode(' ', $classes), '"';
				}
				echo '><a href="', $this->InstructorAskExpertLink($inst), '">',$this->InputSafeString($inst->GetFullName()), '</a></li>';
			}
			echo '</ul></div>';
			return $this->SubMenuToggle('Experts', ob_get_clean(), 'inst', $this->instructor->id);
		}
	} // end of fn DisplayInstructorsList
	
	protected function GetInstructorList()
	{	$instructors = array();
		$sql = 'SELECT instructors.* FROM askimaminstructors, instructors, askimamtopics WHERE askimaminstructors.inid=instructors.inid AND askimaminstructors.askid=askimamtopics.askid AND askimamtopics.live=1 AND askimamtopics.startdate<="' . $this->datefn->SQLDate() . '" ORDER BY instructors.instname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$instructors[$row['inid']] = $row;
			}
		}
		return $instructors;
	} // end of fn GetInstructorList
	
	protected function GetCategoriesList()
	{	$rawcats = array();
		$sql = 'SELECT coursecategories.* FROM askimamtocats, coursecategories, askimamtopics WHERE askimamtocats.catid=coursecategories.cid AND askimamtocats.askid=askimamtopics.askid AND askimamtopics.live=1 AND askimamtopics.startdate<="' . $this->datefn->SQLDate() . '" ORDER BY coursecategories.ctitle';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$row['subcats'] = array();
				$rawcats[$row['cid']] = $row;
			}
		}
		
		if ($rawcats)
		{	// add in missing parents
			do
			{	$found_parents = false;
				foreach ($rawcats as $cat)
				{	if ($cat['parentcat'] && !$rawcats[$cat['parentcat']])
					{	$sql = 'SELECT * FROM coursecategories WHERE cid=' . $cat['parentcat'];
						if ($result = $this->db->Query($sql))
						{	if ($row = $this->db->FetchArray($result))
							{	$row['no_topics'] = true;
								$row['subcats'] = array();
								$rawcats[$row['cid']] = $row;
								$found_parents = true;
								$found_some = true;
							}
						}
					}
				}
			} while ($found_parents);
			
			if ($found_some)
			{	uasort($rawcats, array($this, 'UASortCats'));
			}
			
			//return $this->CascadedCats($rawcats);
			foreach ($rawcats as $cat)
			{	if ($cat['parentcat'] && $rawcats[$cat['parentcat']])
				{	$rawcats[$cat['parentcat']]['subcats'][$cat['cid']] = $cat['cid'];
				}
			}
		
		}
		
		return $rawcats;
	} // end of fn GetCategoriesList
	
	private function UASortCats($a, $b)
	{	if (strtolower($a['ctitle']) == strtolower($b['ctitle']))
		{	return $a['cid'] > $b['cid'];
		} else
		{	return strtolower($a['ctitle']) > strtolower($b['ctitle']);
		}
	} // end of fn UASortCats
	
	protected function GetLatestTopic()
	{	if ($archives = $this->GetArchives(1))
		{	return array_pop($archives);
		}
	} // end of fn GetLatestTopic
	
	protected function DisplayArchiveList($limit = 0)
	{	ob_start();
		if ($archives = $this->GetArchives())
		{	echo '<h3>Latest Themes</h3><ul>'; // changed from Topics to Themes, Tim 11/10/13
			foreach ($archives as $topic_row)
			{	if (++$archive_count > $limit)
				{	
					echo '<li class="listShowMore"><a onclick="ShowArchive(', $limit + $this->archive_perpage, ',', (int)$this->topic->id, ');">show more</a></li>';
					break;
				}
				echo '<li><a href="', $this->TopicLink($topic_row), '">', $this->InputSafeString($topic_row['title']), ', ', date('M \'y', strtotime($topic_row['startdate'])), '</a></li>';
			}
			echo '</ul>';
		}
		return ob_get_clean();
	} // end of fn DisplayArchiveList
	
	protected function GetArchives($limit = 0)
	{	$topics = array();
		$where = array();
		$where[] = 'live=1';
		$where[] = 'startdate<="' . $this->datefn->SQLDate() . '"';
		if ($askid = (int)$this->topic->id)
		{	$where[] = 'NOT askid=' . $askid;
		}
		$sql = 'SELECT * FROM askimamtopics WHERE ' . implode(' AND ', $where) . ' ORDER BY startdate DESC';
		if ($limit = (int)$limit)
		{	$sql .= ' LIMIT ' . $limit;
		}
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$topics[$row['askid']] = $row;
			}
		}
		return $topics;
	} // end of fn GetArchives
	
	protected function DisplayComingUpList()
	{	ob_start();
		if ($comingup = $this->GetComingUp())
		{	echo '<h3>Coming up</h3><ul>';
			foreach ($comingup as $topic_row)
			{	/*if (++$archive_count > $limit)
				{	
					echo '<li class="listShowMore"><a onclick="ShowArchive(', $limit + $this->archive_perpage, ',', (int)$this->topic->id, ');">show more</a></li>';
					break;
				}*/
				echo '<li>', $this->InputSafeString($topic_row['title']), ', ', date('j M \'y', strtotime($topic_row['startdate'])), '</li>';
			}
			echo '</ul>';
		}
		return ob_get_clean();
	} // end of fn DisplayComingUpList
	
	protected function GetComingUp()
	{	$topics = array();
		$sql = 'SELECT * FROM askimamtopics WHERE live=1 AND startdate>"' . $this->datefn->SQLDate() . '" ORDER BY startdate ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$topics[$row['askid']] = $row;
			}
		}
		return $topics;
	} // end of fn GetArchives
	
	public function ListQuestionsContainer($questions = array(), $page = 0, $perpage = 0)
	{	ob_start();
		echo '<div id="askQuestionsContainer">', 
			$this->ListQuestions($questions, $page, $perpage), 
			'</div>'
			;
		return ob_get_clean();
	} // end of fn ListQuestionsContainer
	
	protected function ListQuestions($questions = array(), $page = 0, $perpage = 0)
	{	ob_start();
		if ($questions)
		{	if ($perpage)
			{	if ($_GET['page'] > 1)
				{	$start = ($_GET['page'] - 1) * $perpage;
				} else
				{	$start = 0;
				}
				$end = $start + $perpage;
			}
			
			$topics = array();
			$catlinks = array();
			echo '<ul class="askQuestionList">';
			foreach($questions as $question_id=>$question)
			{	if (++$count > $start)
				{	if ($end && ($count > $end))
					{	break;
					}
					$question = new AskImamQuestion($question_id);
					
					if (!$topics[$question->details['askid']])
					{	$topics[$question->details['askid']] = new AskImamTopic($question->details['askid']);
						$topics[$question->details['askid']]->cats_links = $this->DisplayCatsLinks($topics[$question->details['askid']]->cats, '');
						$topics[$question->details['askid']]->inst_links = $topics[$question->details['askid']]->DisplayInstructorText();
					}
					
					echo '<li><div class="askImamLeft"><h4><a onclick="ShowAnswerInLine(', $question->id, ');">', $this->InputSafeString($question->details['qtext']), '</a></h4>';
					if ($topics[$question->details['askid']]->inst_links)
					{	echo '<p>', $topics[$question->details['askid']]->inst_links, '</p>';
					}
					echo '</div><div class="askImamRight">';
				/*	if ($topics[$question->details['askid']]->cats_links)
					{	echo '<p>', $topics[$question->details['askid']]->cats_links, '</p>';
					}*/
					echo '</div><div class="clear"></div>',
						//'<p class="questionAnswerLink" id="askAnswerOpener', $question->id, '"><a onclick="ShowAnswerInLine(', $question->id, ');">see the answer</a></p>',
						'<div class="questionAnswerContainer" id="askAnswerContainer', $question->id, '"><p class=questionAnswerLink><a onclick="HideAnswerInLine(', $question->id, ');">hide answer</a></p><div class="questionAnswerInner" id="askAnswerInner', $question->id, '"></div><div class="clear"></div></div></li>';
				}
			}
			
			echo '</ul><div class="clear"></div>';
			if ($end && (count($questions) > $perpage))
			{	$pag = new AjaxPagination($_GET['page'], count($questions), $perpage, 'askQuestionsContainer', 'ajax_askimampage.php', $_GET);
				
				$pag = new Pagination($page, count($questions), $perpage, $pagelink);
				echo '<div class="pagination">', $pag->Display(''), '</div>';
			}
			echo '<script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#rlp_modal_popup").jqm();});</script>',
			'<!-- START question answer modal popup --><div id="rlp_modal_popup" class="jqmWindow"><a href="#" class="jqmClose submit">Close</a><div id="rlpModalInner"></div></div>';
		}
		return ob_get_clean();
	} // end of fn ListQuestions
	
} // end of defn AskTheImamPage
?>