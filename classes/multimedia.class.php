<?php
class Multimedia extends BlankItem implements Searchable
{	var $language = '';
	var $cats = array();
	protected $pdf_dir = '/lib_pdfs/';
	protected $mp3_dir = '/lib_mp3s/';
	protected $mp4_dir = '/lib_mp4s/';
	var $imagelocation = '';
	var $imagedir = '';
	var $image_w = 300;
	var $image_h = 400;
	var $thumb_w = 150;
	var $thumb_h = 110;
	var $popup_width = 900;
	var $popup_height = 600;
	
	function __construct($id = '')
	{	parent::__construct($id, 'multimedia', 'mmid');
		$this->pdf_dir = CITDOC_ROOT . $this->pdf_dir;
		$this->mp3_dir = CITDOC_ROOT . $this->mp3_dir;
		$this->mp4_dir = CITDOC_ROOT . $this->mp4_dir;
		$this->imagelocation = SITE_URL . 'img/multimedia/';
		$this->imagedir = CITDOC_ROOT . '/img/multimedia/';
	//	$this->thumb_h = round(($this->thumb_w * $this->image_h) / $this->image_w, 0);
	} // fn __construct
	
	public function GetExtra()
	{	$this->GetCats();
	} // end of fn GetExtra
	
	public function GetCats()
	{	$this->cats = array();
		if ($this->id)
		{	$sql = 'SELECT libcats.* FROM libcats, multimediacats WHERE libcats.lcid=multimediacats.lcid AND multimediacats.mmid=' . $this->id . ' ORDER BY libcats.lcorder, libcats.lcid';
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->cats[$row['lcid']] = $row;
				}
			}
		}
	} // end of fn GetCats
	
	public function ResetExtra()
	{	$this->parent = array();
		$this->cats = array();
	} // end of fn ResetExtra
	
	public function PDFLocation()
	{	if (file_exists($filename = $this->PDFFilename()))
		{	return $filename;
		}
		return '';
	} // end of fn PDFLocation
	
	protected function PDFFilename()
	{	return $this->pdf_dir . $this->id . '.pdf';
	} // end of fn PDFFilename
	
	public function PDFExists()
	{	return file_exists($this->PDFLocation());
	} // end of fn PDFExists
	
	public function PDFLink()
	{	return SITE_URL . 'library_pdf.php?id=' . $this->id;
	} // end of fn PDFLink
	
	protected function MP3Filename()
	{	return $this->mp3_dir . $this->id . '.mp3';
	} // end of fn MP3Filename
	
	public function MP3Location()
	{	if (file_exists($filename = $this->MP3Filename()))
		{	return $filename;
		}
		return '';
	} // end of fn MP3Location
	
	public function MP3Exists()
	{	return file_exists($this->MP3Location());
	} // end of fn MP3Exists
	
	public function MP3Link()
	{	return SITE_URL . 'library_mp3.php?id=' . $this->id;
	} // end of fn MP3Link
	
	protected function MP4Filename()
	{	return $this->mp4_dir . $this->id . '.mp4';
	} // end of fn MP4Filename
	
	public function MP4Location()
	{	if (file_exists($filename = $this->MP4Filename()))
		{	return $filename;
		}
		return '';
	} // end of fn MP4Location
	
	public function MP4Exists()
	{	return file_exists($this->MP4Location());
	} // end of fn MP4Exists
	
	public function MP4Link()
	{	return SITE_URL . 'library_mp4.php?id=' . $this->id;
	} // end of fn MP4Link
	
	public function JPGLink($force_downland = false)
	{	return SITE_URL . 'library_jpg.php?id=' . $this->id . ($force_downland ? '&fd=1' : '');
	} // end of fn JPGLink
	
	public function PDFViewName()
	{	return $this->details['mmslug'] . '.pdf';
	} // end of fn PDFViewName
	
	public function JPGViewName()
	{	return $this->details['mmslug'] . '.jpg';
	} // end of fn JPGViewName
	
	public function MP3ViewName()
	{	return $this->details['mmslug'] . '.mp3';
	} // end of fn MP3ViewName
	
	public function ViewName($suffix = 'jpg')
	{	return $this->details['mmslug'] . '.' . $suffix;
	} // end of fn ViewName
	
	function HasImage()
	{	return (file_exists($this->ImageFile())) ? $this->ImageSRC() : false;
	} // end of fn HasImage
	
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
	
	public function Thumbnail($width = 0, $height = 0)
	{	switch ($this->details['mmtype'])
		{	case 'vimeo':
				if ($this->details['videocode'])
				{	return SITE_URL . 'vimeo_thumb.php?vidid=' . $this->details['videocode'];
				}
				break;
			case 'youtube':
				if ($this->details['videocode'])
				{	return 'http://img.youtube.com/vi/' . $this->details['videocode'] . '/default.jpg';
				}
				break;
			default: // check for poster
				if (file_exists($this->ThumbFile()))
				{	return $this->ThumbSRC();
				} else
				{	return $this->DefaultImageSRC(array($width, $height));
				}
				// if 
		}
	} // end of fn Thumbnail
	
	public function ViewLabel()
	{	switch ($this->MediaType())
		{	case 'youtube':
			case 'vimeo':
			case 'mp4':
				return 'Watch';
				break;
			case 'mp3':
				return 'Listen';
				break;
			default:
				return 'View';
		}
	} // end of fn ViewLabel
	
	public function MediaType()
	{	if ($this->details['mmtype'])
		{	return $this->details['mmtype'];
		} else
		{	if ($this->MP3Exists())
			{	return 'mp3';
			} else
			{	if ($this->MP4Exists())
				{	return 'mp4';
				} else
				{	if ($this->PDFExists())
					{	return 'pdf';
					}
				}
			}
		}
	} // end of fn MediaType
	
	public function ButtonType()
	{	switch($this->MediaType())
		{	case 'mp3':
				return 'audio';
				break;
			case 'mp4':
			case 'youtube':
			case 'vimeo':
				return 'video';
				break;
		}
		return 'multimedia';
	} // end of fn ButtonType
	
	public function CanEmbed()
	{	return (($mtype = $this->MediaType()) == 'mp3') || ($mtype == 'mp4');
	} // end of fn CanEmbed
	
	function Output($width = 600, $height = 380, $class = '', $autoplay = false, $allow_download = false)
	{	ob_start();
		$classname = ($class ? 'class="' . $class . '"' : '');
		
		switch($this->MediaType())
		{
			case 'vimeo':
				if ($this->details['videocode'])
				{	$id = uniqid('player_');
					echo '<iframe id="', $id, '" ', $classname, ' src="http://player.vimeo.com/video/', $this->details['videocode'],'?api=1&player_id=',  $id, '&autoplay=', $autoplay ? '1' : '0', '" width="', (int)$width, '" height="', (int)$height, '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
					break;
				}
			case 'youtube':
				if ($this->details['videocode'])
				{	
					echo '<iframe width="', (int)$width, '" height="', (int)$height, '" src="http://www.youtube.com/embed/', $this->details['videocode'], '?rel=0" frameborder="0" allowfullscreen></iframe>';
					break;
				}
			case 'pdf':
				echo '<h3><a href="', $link = $this->PDFLink(), '">Download PDF</a></h3><a href="', $link, '"><img src="', $this->Thumbnail(), '" title="', $title = $this->InputSafeString($this->details['mmname']), '" alt="', $title, '" /></a>';
				break;
			case 'mp3':
				echo '<img src="', $this->Thumbnail(), '" title="', $title = $this->InputSafeString($this->details['mmname']), '" alt="', $title, '" /><div><object type="application/x-shockwave-flash" data="', SITE_URL, 'flash/player_mp3.swf" width="200" height="20"><param name="movie" value="', SITE_URL, 'flash/player_mp3_mini.swf" /><param name="wmode" value="transparent" /><param name="FlashVars" value="mp3=', urlencode($this->MP3Link()), '" /></object></div>';
				if ($allow_download)
				{	echo '<a href="', $this->MP3Link(), '">Download as MP3</a>';
				}
				break;
			case 'mp4':
				echo '<video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="', (int)$width, '" height="', (int)$height, '" poster="', $this->ImageSRC(), '" data-setup="{}"><source src="', $this->MP4Link(), '" type="video/mp4"></video>';
				if ($allow_download)
				{	echo '<a href="', $this->MP4Link(), '">Download as MP4</a>';
				}
				break;
			default: // assumed to be jpeg if one exists
				if (file_exists($this->ImageFile()))
				{	list($width, $height) = getimagesize($this->ImageFile());
					$div_width = $width;
					if ($width > $this->popup_width)
					{	$div_width = $width = $this->popup_width;
					}
					if ($height > $this->popup_height)
					{	$height = $this->popup_height;
						$div_width += 20;
					}
					echo '<a class="mmJpegLink" onclick="$(\'#mmj_modal_popup\').jqmShow();"><img src="', $this->Thumbnail(), '" title="', $title = $this->InputSafeString($this->details['mmname']), '" alt="', $title, '" /></a><script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#mmj_modal_popup").jqm();});</script><!-- START jpeg modal popup --><div id="mmj_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: ', $div_width + 48, 'px; margin-left: -', ceil(($div_width + 48) / 2), 'px;"><a href="#" class="jqmClose submit">Close</a><div id="mmjModalInner" style="width: ', $div_width, 'px; height: ', $height, 'px;"><h3><a href="', $this->JPGLink(true), '">Download full image</a></h3><img width="', $width, 'px" src="', $this->JPGLink(), '" title="', $title = $this->InputSafeString($this->details['mmname']), '" alt="', $title, '" /></div></div><!-- EOF jpeg modal popup -->';
				}
		}
		
		return ob_get_clean();
	} // end of fn Output
	
	public function IFrameEmbedCode($data = array())
	{	ob_start();
		$fail = array();
		if (!$width = (int)$data['width'])
		{	$fail[] = 'width missing';
		}
		if (!$height = (int)$data['height'])
		{	$fail[] = 'height missing';
		}
		//$fail[] = 'test';
		if ($fail)
		{	echo '<!-- ', implode(', ', $fail), ' -->';
		} else
		{	echo '<iframe width="', $width, '" height="', $height, '" frameborder="0" src="', $this->EmbedSource($data), '"></iframe>';
		}
		//print_r($data);
		return ob_get_clean();
	} // end of fn IFrameEmbedCode
	
	public function EmbedSource($data = array())
	{	$paras = array('id'=>'id=' . (int)$this->id);
		if ($width = (int)$data['width'])
		{	$paras['w'] = 'w=' . $width;
		}
		if ($height = (int)$data['height'])
		{	$paras['h'] = 'h=' . $height;
		}
		if ($data['auto'])
		{	$paras['auto'] = 'auto=1';
		}
		return SITE_URL . 'mm_iframe.php?' . implode('&amp;', $paras);
	} // end of fn EmbedSource
	
	public function RecordView()
	{	if (!$_SESSION['mm_viewed'][$this->id])
		{	if (!is_array($_SESSION['mm_viewed']))
			{	$_SESSION['mm_viewed'] = array();
			}
			$sql = 'INSERT INTO multimediaviews SET mmid=' . $this->id . ', viewed="' . $this->datefn->SQLDateTime() . '"';;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$_SESSION['mm_viewed'][$this->id] = $this->id;
				}
			}
		}
	} // end of fn RecordView
	
	public function ViewCount($timeback = '')
	{	$sql = 'SELECT COUNT(viewed) AS viewcount FROM multimediaviews WHERE mmid=' . $this->id;
		if ($timeback)
		{	if ((int)$timeback !== $timeback)
			{	$timeback = strtotime($timeback);
			}
			$sql .= ' AND viewed>"' . $this->datefn->SQLDateTime($timeback) . '"';
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return (int)$row['viewcount'];
			}
		}
		return 0;
	} // end of fn ViewCount
	
	public function DisplayFullDesc($cutdown = 100)
	{	ob_start();
		if ($this->details['mmdesc'])
		{	
			if ($cutdown && (strlen($this->details['mmdesc']) > $cutdown) && ($cutdown_text = substr($this->details['mmdesc'], 0, $cutdown)))
			{	echo '<div id="mmdFulltext">';
			}
			foreach (explode("\n", $this->details['mmdesc']) as $para)
			{	echo '<p>', $this->InputSafeString($para), '</p>';
			}
			if ($cutdown_text)
			{	echo '<p class="mmdDescToggle"><a onclick="ToggleMMFullDesc(false);">Show less &raquo;</a></p></div><div id="mmdLessText" style="display:none;">';
				foreach (explode("\n", $cutdown_text) as $para)
				{	echo '<p>', $this->InputSafeString($para), '</p>';
				}
				echo '<p class="mmdDescToggle"><a onclick="ToggleMMFullDesc(true);">Read more &raquo;</a></p></div>
				<script>
				//<!--
				function ToggleMMFullDesc(showfull)
				{	document.getElementById("mmdFulltext").style.display = showfull ? "block" : "none";
					document.getElementById("mmdLessText").style.display = showfull ? "none" : "block";
				}
				ToggleMMFullDesc(false);
				//-->
				</script>
				';
			}
		}
		return ob_get_clean();
	} // end of fn DisplayFullDesc
	
	public function DisplayFullDescHTML($cutdown = 100)
	{	ob_start();
		if ($this->details['mmdesc'])
		{	
			if ($cutdown && (strlen($this->details['mmdesc']) > $cutdown) && ($cutdown_text = substr($this->details['mmdesc'], 0, $cutdown)))
			{	echo '<div id="mmdFulltext">';
			}
			echo stripslashes($this->details['mmdesc']);
			if ($cutdown_text)
			{	
				echo '<p class="mmdDescToggle"><a onclick="ToggleMMFullDesc(false);">Show less &raquo;</a></p></div><div id="mmdLessText" style="display:none;">', stripslashes($cutdown_text), '</p><p class="mmdDescToggle"><a onclick="ToggleMMFullDesc(true);">Read more &raquo;</a></p></div>
				<script>
				//<!--
				function ToggleMMFullDesc(showfull)
				{	document.getElementById("mmdFulltext").style.display = showfull ? "block" : "none";
					document.getElementById("mmdLessText").style.display = showfull ? "none" : "block";
				}
				ToggleMMFullDesc(false);
				//-->
				</script>
				';
			}
		}
		return ob_get_clean();
	} // end of fn DisplayFullDescHTML
	
	public function Link()
	{	return SITE_URL . 'multimedia/' . $this->id . '/' . $this->details['mmslug'] . '/';
	} // end of fn Link
	
	public function PostedString()
	{	$parts = array();
		if ($author = $this->InputSafeString($this->details['author']))
		{	$parts[] = 'by ' . $author;
		}
		if ((int)$this->details['posted'])
		{	$parts[] = date('jS F Y', strtotime($this->details['posted']));
		}
		return implode('&nbsp;|&nbsp;', $parts);
	} // end of fn PostedString
	
	public function DisplayInList()
	{	ob_start();
		echo '<li><div class="mml_image"><img src="', $this->Thumbnail(), '" width="140px" title="', $title = $this->InputSafeString($this->details['mmname']), '" alt="', $title, '" /></div><div class="mml_desc">', $title, '</div><a class="mml_link mml_link_', $this->ButtonType(), '" href="', $link = $this->Link(), '"></a></li>';
		return ob_get_clean();
	} // end of fn MultimediaInList
	
	public function IsVideo()
	{	return in_array($this->MediaType(), array('youtube', 'vimeo', 'mp4'));
	} // end of fn IsVideo
	
	/** Search Functions ****************/
	public function Search($term)
	{
		$match = ' MATCH(mmname, mmdesc) AGAINST("' . $this->SQLSafe($term) . '") ';
		$sql = 'SELECT *, ' . $match . ' as matchscore FROM multimedia WHERE ' . $match . ' AND live=1 AND inlib=1 ORDER BY matchscore DESC';
		
		$results = array();
		
		if($result = $this->db->Query($sql))
		{	while($row = $this->db->FetchArray($result))
			{	$results[] = new Multimedia($row);	
			}
		}
		
		return $results;
	} // end of fn Search
	
	public function SearchResultOutput()
	{	echo '<h4><span>', ucwords($this->ButtonType()), '</span><a href="', $this->Link(), '">', $link = $this->InputSafeString($this->details['mmname']), '</a></h4><p><a href="', $link, '">read more ...</a></p>';
	} // end of fn SearchResultOutput
	
	public function GetPeople($live_only = true)
	{	$people = array();
		$where = array('instructors.inid=multimediapeople.inid', 'multimediapeople.mmid=' . $this->id);
		if ($live_only)
		{	$where[] = 'instructors.live=1';
		}
		$sql = 'SELECT instructors.* FROM instructors, multimediapeople WHERE ' . implode(' AND ', $where) . ' ORDER BY instructors.showfront DESC, instructors.instname ASC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$people[$row['inid']] = $row;
			}
		}
		return $people;
	} // end of fn GetPeople
	
	public function GetAuthorText($links = true)
	{	ob_start();
		if ($this->details['author'])
		{	echo $this->InputSafeString($this->details['author']), ' ';
		}
		$by = array();
		if ($people = $this->GetPeople())
		{	foreach ($people as $inst_row)
			{	$inst = new Instructor($inst_row);
				if ($links)
				{	$by[] = '<a href="' . $inst->Link() . '">' . $this->InputSafeString($inst_row['instname']) . '</a>';
				} else
				{	$by[] =  $this->InputSafeString($inst_row['instname']);
				}
			}
		}
		if ($by)
		{	echo implode(', ', $by);
		}
		return ob_get_clean();
	} // fn GetAuthorText
		
} // end of defn Multimedia
?>