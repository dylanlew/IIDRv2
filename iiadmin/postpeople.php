<?php
include_once('sitedef.php');

class PostPeoplePage extends AdminPostsPage
{
	public function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function PostLoggedInConstruct()
	{	parent::PostLoggedInConstruct('people');
		$this->js[] = 'admin_postpeople.js';
		$this->breadcrumbs->AddCrumb('postpeople.php?id=' . $this->post->id, 'people');
	} // end of fn PostLoggedInConstruct
	
	protected function PostBodyMain()
	{	parent::PostBodyMain();
		echo $this->post->PeopleListContainer();
	} // end of fn PostBodyMain
	
} // end of defn PostPeoplePage

$page = new PostPeoplePage();
$page->Page();
?>