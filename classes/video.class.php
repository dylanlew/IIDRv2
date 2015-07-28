<?php
class Video extends Base implements Searchable
{	
	var $details = array();
	var $id = 0;
	var $imagelocation = "";
	var $imagedir = "";
	var $imagesizes = array();
	
	function __construct($id = 0)
	{	parent::__construct();
		$this->imagelocation = SITE_URL . "img/videos/";
		$this->imagedir = CITDOC_ROOT . "/img/videos/";
		$this->imagesizes['default'] = array(560, 315);
		$this->imagesizes['thumbnail'] = array(215, 120);
		$this->Get($id); 
	} // fn __construct
	
	function Reset()
	{	$this->details = array();
		$this->id = 0;
	} // end of fn Reset
	
	function Get($id = 0)
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			$this->id = $id["vid"];
		} else
		{	if ($result = $this->db->Query("SELECT * FROM videos WHERE vid=" . (int)$id))
			{	if ($row = $this->db->FetchArray($result))
				{	$this->Get($row);
				}
			}
		}
		
	} // end of fn Get	
	
	function AddHit()
	{
		$this->db->Query("UPDATE videos SET vhits = vhits+1 WHERE vid = ". (int)$this->id);
	}
	
	function GetPopular($limit = 5)
	{
		$videos = array();
		
		if($result = $this->db->Query("SELECT * FROM videos WHERE live = 1 ORDER BY vhits DESC LIMIT ". (int)$limit))
		{
			while($row = $this->db->FetchArray($result))
			{
				$videos[] = new Video($row);
			}
		}
		
		return $videos;
	}
	
	function GetAll()
	{
		$videos = array();
		
		if($result = $this->db->Query("SELECT * FROM videos WHERE live = 1 ORDER BY dateadded DESC"))
		{
			while($row = $this->db->fetchArray($result))
			{
				$videos[] = new Video($row);
			}
		}
		
		return $videos;
	}
	
	function GetMostViewed($limit = 5)
	{
		$videos = array();
		
		if($result = $this->db->Query("SELECT * FROM videos WHERE live = 1 ORDER BY vhits DESC LIMIT ". (int)$limit))
		{
			while($row = $this->db->fetchArray($result))
			{
				$videos[] = new Video($row);
			}
		}
		
		return $videos;	
	}
	
	function CanView()
	{
		return $this->details["live"];	
	}
	
	function GetDuration()
	{
		$hours = floor($this->details['vduration']/3600);
		$mins = floor(($this->details['vduration']%3600)/60);
		$secs = $this->details['vduration']-(3600*$hours)-(60*$mins);
		
		if($hours)
		{
			$format = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
		}
		else
		{
			$format = sprintf('%02d:%02d', $mins, $secs);	
		}
		
		return $format;
	}
	
	function GetThumbOverview($width = 140)
	{
		return "<p class='video-thumbnail'><a href='". $this->link->GetVideoLink($this) ."'><img src='". $this->GetImageSRC('thumbnail') ."' width ='". $width ."' alt='' /><span class='duration'>". $this->GetDuration() ."</span><span class='play'></span></a></p>";	
	}
	
	function GetThumbHome($width = 470, $height = 264)
	{
		return "<div class='video-image-home'><img src='". $this->GetImageSRC('default') ."' width ='". $width ."' height='". $height ."' alt='". $this->details['vtitle']."' /><div class='video-image-play'></div><div class='video-image-title'><div class='video-image-title-inner'><p><a href='". $this->link->GetVideoLink($this) ."'>". $this->details['vtitle']."</a></p></div></div></div>";	
	}
	function GetThumbProduct($width = 400, $height = 250)
	{
		return "<div class='video-image-product'><img src='". $this->GetImageSRC('default') ."' width ='". $width ."' height='". $height ."' alt='". $this->details['vtitle']."' /><div class='video-image-play'></div><div class='video-image-title'><div class='video-image-title-inner'><p><a href='". $this->link->GetVideoLink($this) ."'>". $this->details['vtitle']."</a></p></div></div></div>";	
	}
	
	function Output($width = 600, $height = 380, $class = '', $autoplay = '0')
	{
		$classname = ($class ? "class='".$class."'" : "");
		
		switch($this->details['vtype'])
		{
			case 'vimeo':
				$id = uniqid('player_');
				return "<iframe id='". $id ."' ". $classname ." src='http://player.vimeo.com/video/". $this->details['vfile'] ."?api=1&player_id=". $id ."&autoplay=".$autoplay."' width='". (int)$width ."' height='". (int)$height ."' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
		}
	}
	
	function HasImage($size = '')
	{	return file_exists($this->GetImageFile($size)) ? $this->GetImageSRC($size) : false;
	}
	
	function GetImageFile($size = 'default')
	{	return $this->imagedir . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}
	
	function GetImageSRC($size = 'default')
	{	return $this->imagelocation . $this->InputSafeString($size) . '/' . (int)$this->id .'.jpg';
	}
	
	function GetCategoryList($liveonly = true)
	{
		$categories = array();
		
		$sql = 'SELECT * FROM videocategories';
		if ($liveonly)
		{	$sql .= ' WHERE live = 1';
		}
		$sql .= ' ORDER BY ctitle ASC';
		
		if($result = $this->db->Query($sql))
		{	while($row = $this->db->FetchArray($result))
			{	$categories[$row['cid']] = $row['ctitle'];
			}
		}
		
		return $categories;
	}
	
	function CategoryListing()
	{
		ob_start();
		
		echo "<div id='videocategorybox'>";
		echo "<h2>Video Categories</h2>";
		
		
		foreach($this->GetCategoryList() as $id => $title)
		{
			$cat = new VideoCategory($id);
			$videos = $cat->GetVideos(5);
			
			echo "<div class='videocategory'>";
			echo "<h3><a href='". $this->link->GetVideoCategoryLink($cat) ."'>". $this->InputSafeString($title) ."</a></h3>";
			
			if(sizeof($videos))
			{
				echo "<ul>";
				
				foreach($videos as $v)
				{	
					echo "<li>". $v->GetThumbOverview() ."<p><a href='". $this->link->GetVideoLink($v) ."'>". $this->InputSafeString($v->details['vtitle']) ."</a></p></li>";
				}
				
				echo "</ul>";
			}
			
			echo "</div>";
			echo "<div class='clear'></div>";
		}
		
		
		echo "</div>";
		
		return ob_get_clean();
	}
	
	function MostViewed()
	{
		ob_start();
		$posts = $this->GetMostViewed();
		echo "<div class='mostpopularlisting'>";
		echo "<h3>Most Viewed and Listened</h3>";
		if($posts){
			foreach ($posts as $p){
				echo '<div class="mostpopularitem clearfix">';
				echo '<div class="mostopularimage"><a href="'. $this->link->GetPostLink($p) .'"><img src="';
				if($img = $p->HasImage('smallthumbnail')) {
					echo $img;
				}
				else 
				{
					echo SITE_URL . 'img/posts/default.png';
				}
				echo '" /></a>';
				echo '</div>';
				echo '<div class="mostpopularcontent"><p class="mostpopulartitle"><a href="'. $this->link->GetPostLink($p) .'">'.$p->details['ptitle'].'</a></p></div>';
				echo '<br />';
				echo '</div>';

			}
		}
		else {
			echo 'There are no "'.$this->type.'" available at the moment';
		}
		
		echo '</div>';
		return ob_get_clean();	
	}
	
	/** Search Functions ****************/
	public function Search($term)
	{
		$match = " MATCH(vtitle, vdesc) AGAINST('". $this->SQLSafe($term) ."') ";
		$sql = "SELECT *, {$match} as matchscore FROM videos WHERE {$match} AND live = 1 ORDER BY matchscore DESC";
		
		$results = array();
		
		if($result = $this->db->Query($sql))
		{
			while($row = $this->db->FetchArray($result))
			{
				$results[] = new Video($row);	
			}
		}
		
		return $results;
	}
	
	public function SearchResultOutput()
	{
		return 'Video result output Video::SearchResultOutput';
	}
	
} // end of defn Video
?>