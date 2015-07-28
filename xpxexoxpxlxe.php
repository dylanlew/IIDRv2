<?php 
require_once('init.php');

class InstructorsPage extends BasePage
{	private $instructors;
	private $perpage = 0;
	private $perline = 4;
	
	function __construct()
	{	parent::__construct('people');
		$this->instructors = new Instructors();
		$this->AddBreadcrumb('People');
		$this->css[] = 'page.css';
		$this->css[] = 'people.css';
		$this->perpage = $this->GetParameter('pag_peoplerows') * $this->perline;
	} // end of fn __construct
	
	function MainBodyContent()
	{	
		if ($this->instructors->instructors)
		{	
			if ($_GET['page'] > 1)
			{	$start = ($_GET['page'] - 1) * $this->perpage;
			} else
			{	$start = 0;
			}
			$end = $start + $this->perpage;
			echo '<ul class="instructors-images">';
			
			foreach($this->instructors->instructors as $instructor)
			{	if (++$count > $start)
				{	if ($count > $end)
					{	break;
					}
					if (++$linecount > $this->perline)
					{	echo '</ul><div class="clear"></div><ul class="instructors-images">';
						$linecount = 1;
					}
					$name = $this->InputSafeString($instructor->details['instname']);
					echo '<li><div class="inst_info_image"><a href="', $this->link->GetInstructorLink($instructor), '"><img src="',($img = $instructor->HasImage('thumbnail')) ? $img : $this->DefaultImageSRC($instructor->imagesizes['thumbnail']), '" alt="', $name, '" title="', $name, '" /></a></div><div class="instructor-info"><a href="', $this->link->GetInstructorLink($instructor), '">', $this->InputSafeString($instructor->details['instname']), '</a></div></li>';
				}
			}
			
			echo '</ul><div class="clear"></div>';
			if (count($this->instructors->instructors) > $this->perpage)
			{	$pag = new Pagination($_GET['page'], count($this->instructors->instructors), $this->perpage, $_SERVER['SCRIPT_NAME']);
				echo '<div class="pagination">', $pag->Display(''), '</div>';
			}
		}
		
	} // end of fn MemberBody
	
} // end of defn PagePage

$page = new InstructorsPage();
$page->Page();

?>