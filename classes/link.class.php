<?php
class Link
{	public $suffix = '/';
	public $prefixes = array('page' => 'page',
							 'storeproduct' => 'store/product',
							 'subproduct' => 'subscription',
							 'storecategory' => 'store/category',
							 'video' => 'video',
							 'videocategory' => 'video/category',
							 'course' => 'course',
							 'event' => 'event',
							 'instructor' => 'people',
							 'download' => 'download',
							 );
							 
	private $allow_rewrite = true;	
	public static $instance;
	
	public function __construct()
	{
		$this->RewriteEnabled();
	} // fn __construct
	
	public static function GetInstance()
	{
		if (!isset(self::$instance))
		{	self::$instance = new Link;	
		}
		
		return self::$instance;
	} // fn GetInstance
	
	public function RewriteEnabled()
	{	return $this->allow_rewrite;
	} // fn RewriteEnabled
	
	public function GetSiteURL()
	{	return SITE_URL;	
	} // fn GetSiteURL
	
	public function GetSSLSiteURL()
	{	return SITE_URL;	
	} // fn GetSSLSiteURL
	
	public function GetLink($link = '', $ssl = false)
	{	return ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL()) . $link;	
	} // fn GetLink
	
	public function GetPageLink($id, $ssl = false)
	{
		if (!$id instanceof PageContent)
		{	$id = new PageContent($id);
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{	if ($id->details['inparent'] && $id->parentpage && ($parent = new PageContent($id->parentpage)) && $parent->id)
			{	$url = $this->GetPageLink($parent) . '#sub_' . $id->details['pagename'];
			} else
			{	$url .= $this->prefixes['page'] . '/'. $id->details['pagename'] . $this->suffix;
			}
		} else
		{	$url .= "page.php?page=". $id->details['pagename'];	
		}
		
		return $url;
	} // fn GetPageLink
	
	public function GetInstructorLink($id, $ssl = false)
	{
		if( !$id instanceof Instructor)
		{	$id = new Instructor($id);
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if ($this->RewriteEnabled())
		{	$url .= $this->prefixes['instructor'] . '/'. $id->details['inid'] . '-'. $id->details['instslug'] . $this->suffix;
		} else
		{	$url .= "instructor.php?id=". $id->details['inid'];	
		}
		
		return $url;
	} // fn GetInstructorLink
	
	public function GetPostLink($id, $ssl = false)
	{
		if(!$id instanceof Post)
		{
			$id = new Post($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{
			$url .= $id->type . '/'. $id->details['pid'] .'-'. $id->details['pslug'] . $this->suffix;
		}
		else
		{
			// would need post.php which would redirect appropriately		
		}
		
		return $url;
	} // fn GetPostLink
	
	public function GetVideoLink($id, $ssl = false)
	{
		if(!$id instanceof Video)
		{
			$id = new Video($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{
			$url .= $this->prefixes['video'] . '/'. $id->details['vid'] .'-'. $id->details['vslug'] . $this->suffix;
		}
		else
		{
			$url .= "video.php?id=". $id->details['vid'];
		}
		
		return $url;
	} // fn GetVideoLink
	
	public function GetVideoCategoryLink($id, $ssl = false)
	{
		if(!$id instanceof VideoCategory)
		{
			$id = new VideoCategory($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{
			$url .= $this->prefixes['videocategory'] . '/'. $id->details['cid'] .'-'. $id->details['cslug'] . $this->suffix;
		}
		else
		{
			$url .= "multimedia.php?id=". $id->details['cid'];
		}
		
		return $url;
	} // fn GetVideoCategoryLink
	
	public function GetStoreProductLink($id, $ssl = false)
	{	
		if (!$id instanceof StoreProduct)
		{
			$id = new StoreProduct($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if ($this->RewriteEnabled())
		{	$url .= $this->prefixes['storeproduct'] . '/' . $id->id . '/' . $id->details['slug'] . $this->suffix;
		} else
		{	$url .= 'storeproduct.php?id=' . $id->id;
		}
		
		return $url;
		
	} // fn GetStoreProductLink
	
	public function GetSubProductLink($id, $ssl = false)
	{	
		if (!$id instanceof SubscriptionProduct)
		{	$id = new SubscriptionProduct($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if ($this->RewriteEnabled())
		{ 	$url .= $this->prefixes['subproduct'] . '/'. $id->id . '/' . $id->details['slug'] . $this->suffix;
		} else
		{ 	$url .= 'subproduct.php?id=' . $id->id;
		}
		
		return $url;
		
	} // fn GetSubProductLink
	
	public function GetStoreCategoryLink($id, $ssl = false)
	{
		if(!$id instanceof StoreCategory)
		{	$id = new StoreCategory($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{	$url .= $this->prefixes['storecategory'] . '/'. $id->details['cid'] . '-'. $id->details['cslug'] . $this->suffix;
		} else
		{	$url .= 'store.php?catid=' . $id->details['cid'];
		}
		
		return $url;
	} // fn GetStoreCategoryLink
	
	public function GetCourseLink($id, $ssl = false)
	{
		if(!$id instanceof Course)
		{	$id = new Course($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{	$url .= $this->prefixes[$id->content['ctype']] . '/' . $id->details['cid'] . '-' . $id->content['cslug'] . $this->suffix;
		} else
		{	$url .= 'course.php?id=' . $id->details['cid'];
		}
		
		return $url;
	} // fn GetCourseLink
	
	public function GetDownloadLink($id, $ssl = false)
	{
		if(!$id instanceof StoreDownload)
		{	$id = new StoreDownload($id);	
		}
		
		$url = ($ssl ? $this->GetSSLSiteURL() : $this->GetSiteURL());
		
		if($this->RewriteEnabled())
		{	$url .= $this->prefixes['download'] . '/' . $id->GetFilename();
		} else
		{	$url .= 'download.php?id=' . $id->details['cid'];
		}
		
		return $url;
	} // fn GetDownloadLink
	
	public function GenerateHtaccess()
	{
		$lines = array();
		$lines[] = '#---Automated htaccess---';
	} // fn GenerateHtaccess
	
} // end of class Link
?>