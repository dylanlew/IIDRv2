<?php
class HomeBannerItem extends Base
{	var $details = array();
	var $id = 0;
	var $language = "";
	var $imagelocation = "";
	var $imagedir = "";
	var $image_w = 978;
	var $image_h = 348;
	var $thumb_w = 100;
	var $thumb_h = 0;
	
	function __construct($id = 0)
	{	parent::__construct();
		$this->imagelocation = SITE_URL . "img/homebanner/";
		$this->imagedir = CITDOC_ROOT . "/img/homebanner/";
		$this->thumb_h = round(($this->thumb_w * $this->image_h) / $this->image_w, 0);
		$this->AssignHBLanguage();
		$this->Get($id);
	} // fn __construct
	
	function AssignHBLanguage()
	{	$this->language = $this->lang;
	} // end of fn AssignHBLanguage
	
	function Reset()
	{	$this->details = array();
		$this->courses = array();
		$this->id = 0;
	} // end of fn Reset
	
	function Get($id = 0)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id["hbid"];
			$this->AddDetailsForLang($this->language);
		} else
		{	if ($result = $this->db->Query("SELECT * FROM homebanner WHERE hbid=" . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
	} // end of fn Get
	
	function AddDetailsForLang($lang = "")
	{	$sql = "SELECT * FROM homebanner_lang WHERE hbid={$this->id} AND lang='$lang'";
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	foreach ($row as $field=>$value)
				{	$this->details[$field] = $value;
				}
			} else
			{	if ($lang == $this->def_lang)
				{	// as last resort go for english
					if ($lang != "en") // only if default language not english
					{	$sql = "SELECT * FROM homebanner_lang WHERE hbid=$this->id AND lang='en'";
						if ($result = $this->db->Query($sql))
						{	if ($row = $this->db->FetchArray($result))
							{	foreach ($row as $field=>$value)
								{	$this->details[$field] = $value;
								}
							}
						}
					}
				} else
				{	$this->AddDetailsForDefaultLang();
				}
			}
		}
	} // end of fn AddDetailsForLang
	
	function AddDetailsForDefaultLang()
	{	$this->AddDetailsForLang($this->def_lang);
	} // end of fn AddDetailsForDefaultLang
	
	function Link()
	{	if ($this->details["hblink"])
		{	if (strstr($this->details["hblink"], "http://") || strstr($this->details["hblink"], "https://"))
			{	return $this->details["hblink"];
			} else
			{	return SITE_URL . $this->details["hblink"];
			}
		}
	} // end of fn Link
	
	function ImageFile()
	{	return $this->imagedir . (int)$this->id . ".jpg";
	} // end of fn ImageFile
	
	function ThumbFile()
	{	return $this->imagedir . "thumbs/" . (int)$this->id . ".jpg";
	} // end of fn ThumbFile
	
	function ImageSRC()
	{	return $this->imagelocation . (int)$this->id . ".jpg";
	} // end of fn ImageSRC
	
	function ThumbSRC()
	{	return $this->imagelocation . "thumbs/" . (int)$this->id . ".jpg";
	} // end of fn ThumbSRC
	
	public function ShowsInLanguages()
	{	$speaks = array();
		$sql = 'SELECT lang FROM homebanner_speak WHERE hbid=' . (int)$this->id;
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$speaks[$row['lang']] = $row['lang'];
			}
		}
		return $speaks;
	} // end of fn ShowsInLanguages
	
} // end of defn HomeBannerItem
?>