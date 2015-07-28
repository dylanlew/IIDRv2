<?php
class NewsImage extends NewsImageBase
{	var $id = 0;
	var $maxSize = 5000;

	function __construct($id = 0)
	{	parent::__construct();
		$this->Get($id);
	} //  end of fn __construct
	
	function Get($id = 0)
	{	$this->ReSet();
		if ($this->FileExists($id))
		{	$this->id = $id;
		}
	} // end of fn Get
	
	function ReSet()
	{	$this->id = 0;
	} // end of fn ReSet
	
	function FullFilePath($id = 0)
	{	return $this->filedir . $id . ".jpg";
	} // end of fn FullFilePath
	
	function FileExists($id = 0)
	{	return file_exists($this->FullFilePath($id));
	} // end of fn FileExists
	
	function Upload($file)
	{	
		$fail = array();
		$success = array();
		
		$uploadSizeLimit = 1024 * $this->maxSize;

		if ($file["size"])
		{	if ((!stristr($file["type"], "jpeg") && !stristr($file["type"], "jpg")) || $file["error"])
			{	$fail[] = "file type invalid";
			} else
			{	if ($file["size"] > $uploadSizeLimit)
				{	$fail[] = "file is too big (" . number_format($file["size"] / 1024, 1) . "kB)";
				} else
				{	
					if ($this->id)
					{	$id = $this->id;
					} else
					{	$id = $this->NextFreeID();
					}
					
					if ($id)
					{
						$this->ReSizeImage($file["tmp_name"], $this->FullFilePath($id), $this->maxSize, $this->maxSize);
						unlink($file["tmp_name"]);
						
						$this->Get($id);
						
						$success[] = "new image uploaded";
					} else
					{	$fail[] = "image not created";
					}
				}
			}
		} else
		{	$fail[] = "image not uploaded";
		}
			
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Save
	
	function ReSizeImage($uploadfile, $file, $maxwidth, $maxheight)
	{	$isize = getimagesize($uploadfile);
		$ratio = $maxwidth / $isize[0];
		$h_ratio = $maxheight / $isize[1];
		if ($h_ratio < $ratio)
		{	$ratio = $h_ratio;
		}
		$oldimage = imagecreatefromjpeg($uploadfile);
		
		if ($ratio < 1)
		{	$w_new = ceil($isize[0] * $ratio);
			$h_new = ceil($isize[1] * $ratio);
			
			$newimage = imagecreatetruecolor($w_new,$h_new);
			imagecopyresampled($newimage,$oldimage,0,0,0,0,$w_new, $h_new, $isize[0], $isize[1]);
		} else
		{	$newimage = $oldimage;
		}
		
		ob_start();
		imagejpeg($newimage, NULL, 100);
		$final_image = ob_get_contents();
		ob_end_clean();
		
		file_put_contents($file, $final_image);
		
	} // end of fn ReSizeImage
	
	function ImageLink()
	{	return $this->imagedir . $this->id . ".jpg";
	} // end of fn ImageLink
	
	function CanDelete()
	{	
		// scan all news
		$imagelink = $this->ImageLink();
		if ($result = $this->db->Query("SELECT newstext FROM news"))
		{	while ($row = $this->db->FetchArray($result))
			{	if (strstr($row["newstext"], $imagelink))
				{	return false;
				}
			}
		}
		
		// scan all pages
		if ($result = $this->db->Query("SELECT pagetext FROM pages"))
		{	while ($row = $this->db->FetchArray($result))
			{	if (strstr($row["pagetext"], $imagelink))
				{	return false;
				}
			}
		}
		
		return $this->CanAdminUserDelete();
	} // end of fn CanDelete
	
	function Delete()
	{	$fail = array();
		$success = array();
		
		if ($this->CanDelete())
		{	if (unlink($this->FullFilePath($this->id)))
			{	$success[] = "image deleted";
			}
		} else
		{	$fail[] = "can't delete image";
		}
		
		return array("failmessage"=>implode(", ", $fail), "successmessage"=>implode(", ", $success));

	} // end of fn Delete

	function UploadForm()
	{	echo "<div id='stories'>\n";
		$form = new Form($_SERVER["SCRIPT_NAME"] . "?id=" . $this->id, "imageform");
		$form->AddFileUpload($this->id ? "Replace image with" : "New image", "newsimage");
		$form->AddSubmitButton("", $this->id ? "Upload Replacement" : "Upload Image", "submit");
		$form->Output();
		if ($this->id && $this->FileExists($this->id))
		{	echo "<img src='", $this->ImageLink(), "' />\n";
			$this->ListUses();
		}
		echo "</div>\n";
	} // end of fn UploadForm
	
	function ListUses()
	{	
		echo "<div id='newsuses'><p>Currently used in ...</p>\n<ul>";
	
		if ($imagelink = $this->ImageLink())
		{	// scan all news
			if ($result = $this->db->Query("SELECT * FROM news"))
			{	while ($row = $this->db->FetchArray($result))
				{	if (strstr($row["newstext"], $imagelink))
					{	echo "<li>new story: <a href='newsstory.php?id=", $row["newsid"], "'>", $row["headline"], "</a></li>\n";
						$usedcount++;
					}
				}
			}
			
			// scan all pages
			if ($result = $this->db->Query("SELECT * FROM pages"))
			{	while ($row = $this->db->FetchArray($result))
				{	if (strstr($row["pagetext"], $imagelink))
					{	echo "<li>page: <a href='pageedit.php?id=", $row["pageid"], "'>", $row["pagetitle"], "</a></li>\n";
						$usedcount++;
					}
				}
			}
		}
		
		if (!$usedcount)
		{	echo "<li>not used in any stories or pages</li>\n";
		}
		echo "</ul></div>";
	} // end of fn ListUses
	
} // end of class NewsImage
?>