<?php
class AdminPageEditPage extends CMSPage
{	public $page = '';
	protected $page_option = '';

	public function __construct()
	{	parent::__construct('CMS');
	} //  end of fn __construct

	public function CMSLoggedInConstruct()
	{	parent::CMSLoggedInConstruct();
		$this->PageEditConstruct();
	} // end of fn CMSLoggedInConstruct
	
	protected function PageEditConstruct($page_option = '')
	{	$this->page_option = $page_option;
		$this->css[] = 'adminpages.css';
		$this->css[] = 'admincoursepage.css';
		
		$this->AssignPage();
		$this->ConstructFunctions();
		
		$this->breadcrumbs->AddCrumb('pagelist.php', 'Pages');
		if ($this->page->id)
		{	$this->breadcrumbs->AddCrumb('pageedit.php?id=' . $this->page->id, $this->InputSafeString($this->page->admintitle));
		}
	} // end of fn PageEditConstruct

	protected function AssignPage()
	{	$this->page = new AdminPageContent($_GET['id'], $this->user->CanUserAccess('administration'));
	} // end of fn AssignPage
	
	public function CMSBodyMain()
	{	$this->PageEditMainContent();
	} // end of fn CMSBodyMain
	
	protected function PageEditMainContent()
	{	$this->PageBodyMenu();
	} // end of fn PageEditMainContent
	
	private function PageBodyMenu()
	{	if ($this->page->id)
		{	echo '<div class="course_edit_menu"><ul>';
			foreach ($this->BodyMenuOptions() as $key=>$option)
			{	echo '<li', $this->page_option == $key ? ' class="selected"' : '', '><a href="', $option['link'], '">', $option['text'], '</a></li>';
			}
			echo '</ul><div class="clear"></div></div><div class="clear"></div>';
		}
	} // end of fn PageBodyMenu
	
	protected function BodyMenuOptions()
	{	$options = array();
		if ($this->page->id)
		{	$options['edit'] = array('link'=>'pageedit.php?id=' . $this->page->id, 'text'=>'Edit Page');
			if ($this->page->details['galleries'])
			{	$options['galleries'] = array('link'=>'pagegalleries.php?id=' . $this->page->id, 'text'=>'Galleries');
			}
		}
		return $options;
	} // end of fn BodyMenuOptions
	
	protected function ConstructFunctions(){}
	
} // end of defn AdminPageEditPage
?>