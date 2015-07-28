<?php
class AdminAskImamPage extends AdminPage
{	var $topic;
	var $question;
	var $topic_option = '';

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('web content'))
		{	$this->AskImamLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function AskImamLoggedInConstruct($topic_option = '')
	{	$this->AssignTopic();
		$this->topic_option = $topic_option;
		$this->css[] = 'admincoursepage.css';
		$this->js[] = 'admin_askimam.js';
		$this->AskImamConstructFunctions();
		$this->breadcrumbs->AddCrumb('askimamtopics.php', 'Ask the Expert');
		if ($this->topic->id)
		{	$this->breadcrumbs->AddCrumb('askimamtopic.php?id=' . $this->topic->id, $this->InputSafeString($this->topic->details['title']));
			if ($this->question->id)
			{	$this->breadcrumbs->AddCrumb('askimamquestions.php?id=' . $this->topic->id, 'Questions');
				$this->breadcrumbs->AddCrumb('askimamquestions.php?id=' . $this->question->id, $this->question->AdminTitle());
			}
		}
	} // end of fn AskImamLoggedInConstruct
	
	public function AssignTopic()
	{	
	} // end of fn AssignTopic
	
	function AskImamBody()
	{	
	} // end of fn AskImamBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('web content'))
		{	$this->AskImamBodyMenu();
			$this->AskImamBody();
		}
	} // end of fn AdminBodyMain
	
	function AskImamConstructFunctions()
	{	
	} // end of fn AskImamConstructFunctions
	
	public function BodyMenuOptions()
	{	$options = array();
		if ($this->topic->id)
		{	$options['topic'] = array('link'=>'askimamtopic.php?id=' . $this->topic->id, 'text'=>'Theme');
			$options['instructors'] = array('link'=>'askimaminstructors.php?id=' . $this->topic->id, 'text'=>'Instructors');
			$options['categories'] = array('link'=>'askimamcategories.php?id=' . $this->topic->id, 'text'=>'Topics');
			$options['questions'] = array('link'=>'askimamquestions.php?id=' . $this->topic->id, 'text'=>'Questions');
			if ($this->question->id)
			{	$options['question'] = array('link'=>'askimamquestion.php?id=' . $this->question->id, 'text'=>$this->question->AdminTitle());
				$options['multimedia'] = array('link'=>'askimammultimedia.php?id=' . $this->question->id, 'text'=>'Multimedia');
				$options['comments'] = array('link'=>'askimamcomments.php?id=' . $this->question->id, 'text'=>'Comments');
			}
		}
		return $options;
	} // end of fn BodyMenuOptions
	
	function AskImamBodyMenu()
	{	
		if ($this->topic->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->topic_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div><h2>', $this->HeaderTitle(), '</h2>';
		}
	} // end of fn CoursesBodyMenu
	
	public function HeaderTitle()
	{	ob_start();
		echo 'Theme: "', $this->InputSafeString($this->topic->details['title']).'"';
		if ($this->question->id)
		{	echo ', Question #', $this->question->id, ':"', $this->InputSafeString($this->question->details['qtext']), '"';
		}
		return ob_get_clean();
	} // end of fn HeaderTitle
	
} // end of defn AdminCourseEditPage
?>