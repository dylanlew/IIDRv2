<?php
class FooterPageContent extends PageContent
{	
	function __construct($id = 0)
	{	parent::__construct($id);
	} //  end of fn __construct
	
	function GetSubPages()
	{	$this->subpages = array();
		if ($this->id)
		{	$sql = "SELECT * FROM pages WHERE parentid=$this->id AND pagelive=1 AND footermenu=1 ORDER BY pageorder";
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->subpages[$row["pageid"]] = $this->AssignPage($row);
				}
			}
		}
	} // end of fn GetSubPages
	
	function AssignPage($page = 0)
	{	return new FooterPageContent($page);
	} // end of fn AssignSubPage
	
} // end of defn FooterPageContent
?>