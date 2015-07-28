<?php 
require_once('init.php');

class InstructorInterviewPage extends InstructorPage
{	private $interview;
	
	public function __construct()
	{	parent::__construct();
		$this->AddBreadcrumb($this->InputSafeString($this->interview->details['ivtitle']));
	} // end of fn __construct
	
	protected function AssignInstructor()
	{	$this->interview = new InstructorInterview($_GET['id']);
		$this->instructor = new Instructor($this->interview->details['inid']);
	} // end of fn AssignInstructor
	
	public function MainBodyMainHeader()
	{	ob_start();
		echo '<h1><span', $this->interview->details['socialbar'] ? ' class="headertextWithSM"' : '', '>', $this->InputSafeString($this->interview->details['ivtitle']), '</span>', $this->interview->details['socialbar'] ? $this->GetSocialLinks(3) : '', '</h1>';
		return ob_get_clean();
	} // end of fn MainBodyMainHeader
	
	public function MainBodyMainContent()
	{	ob_start();
		echo '<div id="interviewtext"><h2>Interview given by ', $this->InputSafeString($this->instructor->GetFullName()), ' on ', date('jS F Y', strtotime($this->interview->details['ivdate'])), '</h2>', stripslashes($this->interview->details['ivtext']), '</div>';
		return ob_get_clean();
	} // end of fn MainBodyMainContent
	
} // end of defn InstructorInterviewPage

$page = new InstructorInterviewPage();
$page->Page();
?>