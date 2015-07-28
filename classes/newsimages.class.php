<?php
class NewsImages extends NewsImageBase
{	var $images = array();

	function __construct()
	{	parent::__construct();
		$this->Get();
	} //  end of fn __construct
	
	function Get($id = 0)
	{	$this->ReSet();
		
		if ($dir = opendir($this->filedir))
		{	
			while ($file = readdir($dir))
			{	
				if (strstr($file, ".jpg"))
				{	
					if ($id = str_replace(".jpg", "", $file))
					{	if ($id == (int)$id)
						{	$this->images[$id] = new NewsImage($id);
						}
					}
				}
			}
			closedir($dir);
		}
		
	} // end of fn Get
	
	function ReSet()
	{	
		$this->images = array();
	} // end of fn ReSet
	
	function AdminListImages()
	{	echo "<h3><a href='newsimage.php'>Add new image</a></h3><br class='clear'/>\n";
		if ($this->images)
		{	echo "<table>\n<tr>\n<th>Image</th>\n<th>Location</th>\n<th>Actions</th>\n</tr>\n";
			foreach ($this->images as $image)
			{	echo "<tr class='stripe", $i++ % 2, "'>\n<td><img src='", $image->ImageLink(), "' /></td>\n<td>", 
						$image->ImageLink(), "</td>\n<td><a href='newsimage.php?id=", $image->id, "'>edit</a>";
				if ($image->CanDelete())
				{	echo " | <a href='newsimages.php?delimage=", $image->id, "'>delete</a>";
				}
				echo "</td>\n</tr>\n";
			}
			echo "</table>\n";
		}
	} // end of fn AdminListImages
	
} // end of defn NewsImages
?>