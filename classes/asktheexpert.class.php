<?php

class AskTheImam extends BlankItem
{	
	
	public function __construct($id = 0)
	{	parent::__construct($id, 'asktheexpert', 'askid');
	} // end of fn __construct
	
	public function Save($data = array())
	{
		
	} // end of fn Save
		
	public function InputForm()
	{	ob_start();
		
		
		return ob_get_clean();	
	} // end of fn InputForm
	
} // end of class AskTheImam
?>