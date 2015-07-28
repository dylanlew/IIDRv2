<?php
class NewsImageBase extends Base
{	var $imagedir = "res/img/";
	var $filedir = "res/img/";

	function __construct()
	{	parent::__construct();
		$this->imagedir = SITE_SUB . "/" . $this->imagedir;
		$this->filedir = CITDOC_ROOT . "/" . $this->filedir;
	} //  end of fn __construct
	
	function NextFreeID()
	{	$nextid = 0;
	
		if ($dir = opendir($this->filedir))
		{	
			while ($file = readdir($dir))
			{	
				if (strstr($file, ".jpg"))
				{	
					if ($id = str_replace(".jpg", "", $file))
					{	if ($id = (int)$id)
						{	if ($id > $nextid)
							{	$nextid = $id;
							}
						}
					}
				}
			}
			closedir($dir);
			$nextid++;
		}
		
		return $nextid;
		
	} // end of fn NextFreeID
	
} // end of class NewsImage
?>