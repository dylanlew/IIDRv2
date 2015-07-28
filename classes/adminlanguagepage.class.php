<?php
class AdminLanguagePage extends AdminPage
{
	function __construct()
	{	parent::__construct("CONTENT");
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess("administration"))
		{	$this->LanguageConstruct();
		}
	} // end of fn LoggedInConstruct

	function LanguageConstruct()
	{	$this->breadcrumbs->AddCrumb("languages.php", "Languages");
	} // end of fn LanguageConstruct
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess("administration"))
		{	$this->LanguageContent();
		}
	} // end of fn AdminBodyMain
	
	function LanguageContent()
	{	
	} // end of fn LanguageContent
	
} // end of defn AdminLanguagePage
?>