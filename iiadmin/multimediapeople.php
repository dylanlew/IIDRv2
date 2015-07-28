<?php
include_once('sitedef.php');

class MultimediaEmbedPage extends AdminMultimediaPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function MMLoggedinConstruct()
	{	parent::MMLoggedinConstruct('people');
		$this->js[] = 'admin_mmpeople.js';
		$this->breadcrumbs->AddCrumb('multimediapeople.php?id=' . $this->multimedia->id, 'People');
	} // end of fn MMLoggedinConstruct
	
	protected function MMBodyMain()
	{	parent::MMBodyMain();
		echo $this->multimedia->PeopleListContainer();
	} // end of fn MMBodyMain
	
} // end of defn MultimediaEmbedPage

$page = new MultimediaEmbedPage();
$page->Page();
?>