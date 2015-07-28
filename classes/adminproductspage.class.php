<?php
class AdminProductsPage extends AdminPage
{	
	function __construct()
	{	parent::__construct('ACCOUNTS');
	} //  end of fn __construct

	function LoggedInConstruct()
	{	parent::LoggedInConstruct();
		if ($this->user->CanUserAccess('accounts'))
		{	$this->ProductsLoggedInConstruct();
		}
	} // end of fn LoggedInConstruct
	
	function ProductsLoggedInConstruct()
	{	$this->breadcrumbs->AddCrumb('products.php', 'Products');
	} // end of fn ProductsLoggedInConstruct
	
	function ProductsBody()
	{	
	} // end of fn ProductsBody
	
	function AdminBodyMain()
	{	if ($this->user->CanUserAccess('accounts'))
		{	$this->ProductsBody();
		}
	} // end of fn AdminBodyMain
	
} // end of defn AdminProductsPage
?>