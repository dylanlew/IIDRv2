<?php 
class PostListingPage extends BasePage
{	protected $ptype;
	protected $perpage = 10;

	function __construct($ptype)
	{	parent::__construct($this->ptype = $ptype);
		$this->perpage = $this->GetParameter('pag_posts');
		$this->css[] = 'page.css';
		$this->css[] = 'post.css';
	} // end of fn __construct
	
	public function PostsSideBar(){}
	
	function MainBodyContent()
	{
		echo $this->PostsSideBar(), '<div class="col3-wrapper-with-sidebar"><div id="post_listing_container">', $this->PostListing(), '</div></div><div class="clear"></div>';	
		
	} // end of fn MainBodyContent
	
	public function PostListing()
	{	ob_start();
		if ($posts = $this->GetPosts())
		{
			if ($_GET['page'] > 1)
			{	$start = ($_GET['page'] - 1) * $this->perpage;
			} else
			{	$start = 0;
			}
			$end = $start + $this->perpage;
			
			echo '<ul class="post_listing">';
			foreach($posts as $post_row)
			{	if (++$count > $start)
				{	if ($count > $end)
					{	break;
					}
				
					$post = new Post($post_row);
					$link = $this->link->GetPostLink($post);
					$datetext = array();
					if ($authortext = $post->GetAuthorDate())
					{	$datetext[] = $authortext;
					}
					$datetext[] = date('l jS F Y', strtotime($post->details['pdate']));
					echo '<li><div class="postlist_image"><a href="', $link, '"><img src="', ($img = $post->HasImage('default')) ? $img : (SITE_URL . 'img/posts/default.png'), '" alt="', $title = $this->InputSafeString($post->details['ptitle']), '" title="', $title, '" /></div><div class="postlist_content"><h4><a href="', $link, '">', $title, '</a></h4><p>', $this->InputSafeString(substr(strip_tags($post->details['pcontent']), 0, 150)), ' ...</p></div><div class="postlist_date">', $post->GetAuthorDate(), '</div><div class="clear"></div></li>';
				}
			}
			echo '</ul>';
			if (count($posts) > $this->perpage)
			{	
				$pag = new AjaxPagination($_GET['page'], count($posts), $this->perpage, 'post_listing_container', 'ajax_posts.php', array_merge($_GET, array('ptype'=>$this->ptype)));
				echo '<div class="pagination">', $pag->Display(), '</div>';
			}
		}
		return ob_get_clean();
	} // end of fn PostListing
	
	public function GetPosts()
	{	$posts = array();
		$where = array('live=1');
		if ($ptype = $this->SQLSafe($this->ptype))
		{	$where[] = 'ptype="' . $ptype . '"';
		}
		if ($year = (int)$_GET['year'])
		{	$where[] = 'LEFT(pdate, 4)="' . $year . '"';
		}
		if ($catid = (int)$_GET['cat'])
		{	$where[] = 'catid=' . $catid;
		}
		
		$sql = 'SELECT * FROM posts WHERE ' . implode(' AND ', $where) . ' ORDER BY pdate DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$posts[$row['pid']] = $row;
			}
		}
		return $posts;
	} // end of fn GetPosts
	
	function GetArchiveSubmenu()
	{	ob_start();
		if ($years = $this->GetArchiveYears($this->ptype))
		{	echo '<div class="sidebar-menu"><h2>Archive</h2><ul>';
			
			foreach($years as $year)
			{	$classes = array();
				if ($_GET['year'] == $year)
				{	$classes[] = 'current-subpage';
				}
				
				echo '<li';
				if ($classes)
				{	echo ' class="' . implode(' ', $classes) . '"';
				}
				echo '><a href="', SITE_URL, $this->ptype, '-archive/', $year, '/">', $year, '</a></li>';
				
			}
			
			echo '</ul></div>';
		}
		return ob_get_clean();
	} // end of fn GetArchiveSubmenu
	
	function GetCategorySubmenu()
	{	ob_start();
		if ($cats = $this->GetCategories($this->ptype))
		{	echo '<div class="sidebar-menu"><h2>Categories</h2><ul>';
			
			foreach($cats as $cat_row)
			{	$cat = new PostCategory($cat_row);
				$classes = array();
				if ($_GET['cat'] == $cat->id)
				{	$classes[] = 'current-subpage';
				}
				
				echo '<li';
				if ($classes)
				{	echo ' class="' . implode(' ', $classes) . '"';
				}
				echo '><a href="', $cat->Link($this->ptype), '">', $this->InputSafeString($cat->details['ctitle']), '</a></li>';
				
			}
			echo '</ul></div>';
		}
		return ob_get_clean();
	} // end of fn GetCategorySubmenu

	public function GetArchiveYears()
	{	$years = array();
		$sql = 'SELECT LEFT(pdate, 4) AS pyear, COUNT(pid) FROM posts WHERE live=1 AND ptype="' . $this->SQLSafe($this->ptype) . '" GROUP BY pyear ORDER BY pyear DESC';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$years[$row['pyear']] = $row['pyear'];
			}
		}
		return $years;
	} // end of fn GetArchiveYears
	
	public function GetCategories()
	{	$cats = array();
		$where = array('posts.live=1', 'posts.catid=postcategories.cid');
		if ($ptype = $this->SQLSafe($this->ptype))
		{	$where[] = 'posts.ptype="' . $ptype . '"';
		}
		$sql = 'SELECT postcategories.*, count(posts.pid) FROM postcategories, posts WHERE ' . implode(' AND ', $where) . ' GROUP BY postcategories.cid ORDER BY postcategories.ctitle';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$cats[] = $row;
			}
		}
		return $cats;
	} // end of fn GetCategories
	
} // end of defn PostListingPage
?>