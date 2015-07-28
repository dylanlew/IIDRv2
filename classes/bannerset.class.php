<?php
class BannerSet extends BlankItem
{
	public $items = array();
	public $has_video = false;
	
	public function __construct($id = null)
	{	parent::__construct($id, 'bannersets', 'id');	
		$this->Get($id);
	} // end of fn __construct
	
	public function Get($id = array())
	{	$this->Reset();
		if (is_array($id))
		{	$this->id = (int)$id['id'];
			$this->details = $id;
			$this->GetItems();
		} else
		{	if ((int)$id && ((int)$id == $id))
			{	if ($result = $this->db->Query('SELECT * FROM bannersets WHERE id=' . (int)$id))
				{	if ($row = $this->db->FetchArray($result))
					{	return $this->Get($row);
					}
				}
			} else
			{	// try by title
				if ($result = $this->db->Query('SELECT * FROM bannersets WHERE title="' . $this->SQLSafe($id) . '"'))
				{	if ($row = $this->db->FetchArray($result))
					{	return $this->Get($row);
					}
				}
			}
		}
	} // end of fn Get
	
	public function ResetExtra()
	{	$this->items = array();
	} // end of fn Reset
	
	public function GetItems()
	{	if ($this->id)
		{	$this->items = array();
			
			if ($result = $this->db->Query('SELECT * FROM banneritems WHERE setid=' . (int)$this->id . ' ORDER BY disporder ASC, id'))
			{	while ($row = $this->db->FetchArray($result))
				{	$this->items[] = new BannerItem($row);
				}
			}
		}
	} // end of fn GetItems
	
	public function OutputSlider($id = null)
	{	ob_start();
		if ($this->items)
		{
			echo '<div ', $id ? ('id="' . $this->InputSafeString($id) . '"') : '', ' class="banner-wrapper">', sizeof($this->items) > 1 ? '<div class="banner-pagination"></div><div class="banner-next"></div><div class="banner-prev"></div>' : '', '<div class="banners"><ul>';
			
			foreach($this->items as $item)
			{	$p = new GalleryPhoto($item->details['itemid']);
				echo '<li><div class="bannertext"><h3>', $this->InputSafeString($item->details['disptitle']), '</h3><p>', $this->InputSafeString($item->details["dispdesc"]), '</p></div><div class="banneritem">';
				if($src = $p->HasImage('default'))
				{
					$img = "<img src='" . $src . "' alt='' />";
					echo $item->details['url'] ? ("<a href='" . $item->details['url'] . "'>" . $img . "</a>") : $img;
				}
			/*	switch($item->details['type'])
				{
					case 'video':
							$this->has_video = true;
							$v = new Video($item->details['itemid']);
							echo $v->Output(465, 290, 'vimeoplayer');
						break;
					case 'image':
							$p = new GalleryPhoto($item->details['itemid']);
							if($src = $p->HasImage('default'))
							{
								$img = "<img src='" . $src . "' alt='' />";
								echo $item->details['url'] ? ("<a href='" . $item->details['url'] . "'>" . $img . "</a>") : $img;
							}
						break;	
				}*/
							
				echo '</div></li>';
			}
					
			echo '</ul></div></div>';
			
			if(sizeof($this->items) > 1)
			{	
				echo "<script>$(function() { $('.banners ul').cycle({ fx: 'scrollLeft', timeout: 3000, pager: '.banner-pagination', next: '.banner-next', prev: '.banner-prev' }); }); </script>";
				
				// On video play, stop the slider
		/*		if($this->has_video)
				{
					echo "<script src='http://a.vimeocdn.com/js_v6/froogaloop2.min.js'></script>";
					echo "<script>$(function() { 
							$('iframe.vimeoplayer').each(function() {  
								element = document.getElementById($(this).attr('id')); 
								Froogaloop(element).addEvent('ready', ready);	
							});
							
							function ready(player_id) { 
								Froogaloop(player_id).addEvent('play', stopSlider); 
								Froogaloop(player_id).addEvent('pause', stopSlider);
							}
							
							function stopSlider(player_id) { 
								$('.banners ul').cycle('pause');	
							}
						});</script>";	
				}*/
			}
		}
		return ob_get_clean();
	} // end of fn OutputSlider
	
	public function OutputPlainSlider($id = null)
	{	ob_start();
		if ($this->items)
		{
			echo '<div ', $id ? ('id="' . $this->InputSafeString($id) . '"') : '', ' class="banner-wrapper">', sizeof($this->items) > 1 ? '<div class="banner-pagination"></div><div class="banner-next"></div><div class="banner-prev"></div>' : '', '<div class="banners"><ul>';
			
			foreach($this->items as $item)
			{	$p = new GalleryPhoto($item->details['itemid']);
				echo '<li><div class="banneritem_plain">';
				if($src = $p->HasImage('default'))
				{
					$img = "<img src='" . $src . "' alt='' />";
					echo $item->details['url'] ? ("<a href='" . $item->details['url'] . "'>" . $img . "</a>") : $img;
				}
							
				echo '</div></li>';
			}
					
			echo '</ul></div></div>';
			
			if(sizeof($this->items) > 1)
			{	
				echo "<script>$(function() { $('.banners ul').cycle({ fx: 'scrollLeft', timeout: 3000, pager: '.banner-pagination', next: '.banner-next', prev: '.banner-prev' }); }); </script>";
			}
		}
		return ob_get_clean();
	} // end of fn OutputPlainSlider
	
	public function MMDimensionsFromImage($img_size = array())
	{	$height = $img_size['height'] - 20;
		$width = $img_size['width'] - 20;
		if (($width / $height) > 2)
		{	$width = $height * 2;
		} else
		{	if ($height > $width)
			{	$height = $width;
			}
		}
		return array('width'=>$width, 'height'=>$height);
	} // end of fn MMDimensionsFromImage
	
	public function OutputMultiSlider($id = '', $width = 960,  $height = 290)
	{	ob_start();
		if ($this->items)
		{
			echo '<div ', $id ? ('id="' . $this->InputSafeString($id) . '"') : '', ' class="banner-wrapper">', count($this->items) > 1 ? ('<div class="banner-pagination" style="left: ' . floor(($width / 2) - (13 * count($this->items))) . 'px;"></div><div class="banner-next" style="height: ' . $height . 'px;"></div><div class="banner-prev" style="height: ' . $height . 'px;"></div>') : '', '<div class="banners" style="width:', $width, 'px; height:', $height, 'px; overflow: hidden"><ul>';
			
			foreach($this->items as $item)
			{	echo '<li style="width: ', $width, 'px; height: ', $height, 'px;"><div class="banneritem_plain">';
				if ($img_done = ($item->details['itemid'] && ($p = new GalleryPhoto($item->details['itemid'])) && $p->id && ($src = $p->HasImage('default'))))
				{
					$img = "<img src='" . $src . "' alt='' />";
					echo ($item->details['url'] && !$item->details['multimedia']) ? ("<a href='" . $item->details['url'] . "'>" . $img . "</a>") : $img;
				}
				if ($item->details['multimedia'] && ($mm = new Multimedia($item->details['multimedia'])) && $mm->id)
				{	$mm_size = array('width'=>$width, 'height'=>$height);
					if ($img_done)
					{	$mm_size = $this->MMDimensionsFromImage($mm_size);
					}
					echo '<div class="banner_mm_container" style="width: ', $mm_size['width'], 'px; height: ', $mm_size['height'], 'px; top: ', floor(($height - $mm_size['height']) / 2), 'px; left: ', floor(($width - $mm_size['width']) / 2), 'px;" onclick="$(\'.banners ul\').cycle(\'pause\');">', $mm->Output($mm_size['width'], $mm_size['height']), '</div>';
				}
				echo '</div></li>';
			}
					
			echo '</ul></div></div>';
			
			if (count($this->items) > 1)
			{	
				echo '<script>$(function() { $(".banners ul").cycle({ fx: "scrollHorz", timeout: 4000, pager: ".banner-pagination", next: ".banner-next", prev: ".banner-prev" }); }); </script>';
			}
		}
		return ob_get_clean();
	} // end of fn OutputMultiSlider
	
} // end of class BannerSet
?>