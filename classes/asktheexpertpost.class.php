<?php

class AskTheExpertPost extends Post
{
	public function __construct($id = null)
	{
		parent::__construct($id, 'ask-the-expert', 'AskTheExpertPost');
	}
}

?>