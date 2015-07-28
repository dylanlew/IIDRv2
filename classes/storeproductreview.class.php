<?php
class StoreProductReview extends BlankItem
{	
	public function __construct($id = null)
	{	parent::__construct($id, 'storeproductreviews', 'id');
	} // end of fn __construct
	
	public function GetAuthor()
	{
		if(($author = new Student($this->details['sid'])) && $author->id)
		{	return $author;	
		}
	} // end of fn GetAuthor
	
} // end of class StoreProductReview
?>