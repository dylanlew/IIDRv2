<?php
class AdminMultimediaPage extends CMSPage
{	protected $multimedia = '';
	protected $mm_option = '';

	function __construct()
	{	parent::__construct('CMS');
	} //  end of fn __construct

	function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->MMLoggedinConstruct();
	} // end of fn CMSLoggedInConstruct

	protected function MMLoggedinConstruct($mm_option = '')
	{	$this->mm_option = $mm_option;
		$this->css[] = 'adminpages.css';
		$this->css[] = 'admincoursepage.css';
		$this->css[] = 'adminmultimedia.css';
		$this->AssignMultimedia();
		$this->MMConstructFunctions();
		$this->breadcrumbs->AddCrumb('multimedialist.php', 'Multimedia');
		if ($this->multimedia->id)
		{	$this->breadcrumbs->AddCrumb('multimedia.php?id=' . $this->multimedia->id, $this->InputSafeString($this->multimedia->admintitle));
		}
	} // end of fn MMLoggedinConstruct
	
	protected function MMConstructFunctions(){}
	
	protected function AssignMultimedia()
	{	$this->multimedia = new AdminMultimedia($_GET['id']);
	} // end of fn AssignMultimedia
	
	function CMSBodyMain()
	{	$this->MMBodyMain();
	} // end of fn CMSBodyMain
	
	protected function MMBodyMain()
	{	$this->MMBodyMenu();
	} // end of fn AssignMultimedia
	
	private function MMBodyMenu()
	{	if ($this->multimedia->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->mm_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div>';
		}
	} // end of fn MMBodyMenu
	
	protected function BodyMenuOptions()
	{	$options = array();
		if ($this->multimedia->id)
		{	$options['edit'] = array('link'=>'multimedia.php?id=' . $this->multimedia->id, 'text'=>'Edit "' . $this->InputSafeString($this->multimedia->admintitle) . '"');
			$options['people'] = array('link'=>'multimediapeople.php?id=' . $this->multimedia->id, 'text'=>'People');
			if ($this->multimedia->CanEmbed())
			{	$options['embed'] = array('link'=>'multimediaembed.php?id=' . $this->multimedia->id, 'text'=>'Embed code');
			}
		}
		return $options;
	} // end of fn BodyMenuOptions
	
} // end of defn AdminMultimediaPage
?>