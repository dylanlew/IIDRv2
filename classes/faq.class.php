<?php
class FAQ extends BlankItem
{	var $cats = array();

	public function __construct($id = 0)
	{	parent::__construct($id, 'faq', 'faqid');
	} // end of fn __construct
	
	public function GetExtra()
	{	$this->GetCats();
	} // end of fn GetExtra
	
	public function GetCats()
	{	$this->cats = array();
		if ($this->id)
		{	$sql = 'SELECT faqcats.* FROM faqcats, faqtocats WHERE faqcats.catid=faqtocats.catid AND faqtocats.faqid=' . $this->id . ' ORDER BY faqcats.listorder, faqcats.catid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->cats[$row['catid']] = $row;
				}
			}
		}
	} // end of fn GetCats
	
} // end of class FAQ
?>