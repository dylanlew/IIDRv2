<?php
include_once('sitedef.php');

class AjaxAskImam_MM extends AdminAskImamPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	public function AssignTopic()
	{	$this->question = new AdminAskImamQuestion($_GET['id']);
		if ($this->question->id)
		{	$this->topic = $this->question->GetTopic();
		} else
		{	$this->topic = new AdminAskImamTopic($_GET['topicid']);
		}
	} // end of fn AssignTopic
	
	function AskImamLoggedInConstruct()
	{	parent::AskImamLoggedInConstruct();
		
		if ($this->question->id)
		{	switch ($_GET['action'])
			{	case 'refresh':
					echo $this->question->MultiMediaTable();
					break;
				case 'add':
					$this->question->AddMultimedia($_GET['mmid']);
					break;
				case 'remove':
					$this->question->RemoveMultimedia($_GET['mmid']);
					break;
				case 'popup':
					echo $this->MultiMediaList();
					break;
				default: echo 'action not found: ', $this->InputSafeString($_GET['action']);
			}
		} else echo 'no question';
	} // end of fn AskImamLoggedInConstruct
	
	private function MultiMediaList()
	{	ob_start();
		if ($cats = $this->GetMultiMedia())
		{	echo '<ul class="coursemmList">';
			foreach ($cats as $catid=>$cat_mm)
			{	$cat = new LibCat($catid);
			//print_r($cat->details);
				echo '<li id="mmlist_', $catid, '"><h3><a onclick="MultimediaListToggle(', $catid, ');">', $this->InputSafeString($cat->details['lcname']), '</a></h3><ul>';
				foreach ($cat_mm as $mmid=>$mm_row)
				{	$mm = new Multimedia($mm_row);
					echo '<li class="mmitem_', $mmid, '"><a onclick="MultiMediaAdd(', $this->question->id, ',', $mmid, ');">', $this->InputSafeString($mm->details['mmname']), '</a></li>';
				}
				echo '</ul></li>';
			}
			echo '<ul>';
		//	$this->VarDump($cats);
		} else
		{	echo 'no multimedia available';
		}
		return ob_get_clean();
	} // end of fn MultiMediaList
	
	private function GetMultiMedia()
	{	$cats = array();
		$existing = $this->question->GetMultiMedia();
		$sql = 'SELECT * FROM multimedia ORDER BY mmorder, posted';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$existing[$row['mmid']])
				{	$mm = new Multimedia($row);
					if ($mm->cats)
					{	foreach ($mm->cats as $cat)
						{	if (!$cats[$cat['lcid']])
							{	$cats[$cat['lcid']] = array();
							}
							$cats[$cat['lcid']][$row['mmid']] = $row;
						}
					} else
					{	if (!$cats[0])
						{	$cats[0] = array();
						}
						$cats[0][$row['mmid']] = $row;
					}
				}
			}
		}
		
		return $cats;
	} // end of fn GetMultiMedia
	
} // end of defn AjaxAskImam_MM

$page = new AjaxAskImam_MM();
?>