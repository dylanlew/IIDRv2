<?php 
class InstructorPage extends BasePage
{	protected $instructor;
	
	public function __construct()
	{	parent::__construct('people');
		$this->css[] = 'page.css';
		$this->css[] = 'jquery.mCustomScrollbar.css';
		$this->js[] = 'jquery-ui-1.8.23.custom.min.js';
		$this->js[] = 'jquery.mousewheel.min.js';
		$this->js[] = 'testimonialslide.js';
		$this->js[] = 'jquery.mCustomScrollbar.min.js';
		$this->css[] = 'studentreviews.css';
		$this->css[] = 'multimedia.css';
		$this->css[] = 'people.css';
		$this->js[] = 'productreview.js';
		$this->js[] = 'jquery.lightbox-0.5.js';
		$this->css[] = 'jquery.lightbox-0.5.css';
		
		//$this->instructor = new Instructor($_GET['id']);
		$this->AssignInstructor();
		if(!$this->instructor->id)
		{	header('location: people.php');
			exit;
		}
		
		$this->AddBreadcrumb('People', $this->link->GetLink('people.php'));
		if ($cats = $this->instructor->CatsForBreadCrumbs())
		{	foreach ($cats as $catid=>$catname)
			{	$this->AddBreadcrumb($catname, '');
			}
		}
		$this->AddBreadcrumb($this->InputSafeString($this->instructor->details['instname']), $this->instructor->Link());
	} // end of fn __construct
	
	protected function AssignInstructor()
	{	$this->instructor = new Instructor($_GET['id']);
	} // end of fn AssignInstructor
	
	function MainBodyContent()
	{	
		echo '<div id="people_inner">', $this->MainBodySideBar(), '<div id="people_inner_main">', $this->MainBodyMainHeader(), $this->MainBodyMainContent(), '</div><div class="clear"></div></div>', $this->MainBodyFooter();
		
	} // end of fn MainBodyContent
	
	public function MainBodyFooter()
	{	ob_start();
		$events = $this->DisplayEventsListing();
		$interviews = $this->DisplayInterviewAndPostListing();//DisplayInterviewListing();
		$mm = $this->DisplayMultimediaListing();
		if ($events || $interviews || $mm)
		{	echo '<div id="people_bottom"><h3>More from ', $this->InputSafeString($this->instructor->GetFullName()), '</h3>', $events, $interviews, $mm, '</div><div class="clear"></div>';
		}
		return ob_get_clean();
	} // end of fn MainBodyFooter
	
	public function MainBodyMainHeader()
	{	ob_start();
		echo '<h1><span', $this->instructor->details['socialbar'] ? ' class="headertextWithSM"' : '', '>', $this->InputSafeString($this->instructor->GetFullName()), '</span>', $this->instructor->details['socialbar'] ? $this->GetSocialLinks(3, true) : '', '</h1>';
		return ob_get_clean();
	} // end of fn MainBodyMainHeader
	
	public function MainBodyMainContent()
	{	ob_start();
		echo stripslashes($this->instructor->details['instbio']), $this->GalleryListingLightBox();
		return ob_get_clean();
	} // end of fn MainBodyMainContent
	
	public function MainBodySideBar()
	{	ob_start();
		echo '<div id="people_inner_left"><img class="instructor-image" src="', ($image = $this->instructor->HasImage('thumbnail')) ? $image : $this->DefaultImageSRC($this->instructor->imagesizes['thumbnail']), '" alt="', $name = $this->InputSafeString($this->instructor->GetFullName()), '" title="', $name, '" />', $this->DisplayTestimonials(), '</div>';
		return ob_get_clean();
	} // end of fn MainBodySideBar
	
	public function DisplayEventsListing()
	{	ob_start();
		if ($events = $this->instructor->GetAllEvents())
		{	echo '<div class="people_bottom_col"><h4>Courses/Events</h4><div id="instEventListContainer">', $this->DisplayEventsListingList($events), '</div></div>';
		}
		return ob_get_clean();
	} // end of fn DisplayEventsListing
	
	public function DisplayEventsListingList($events, $page = 0, $perpage = 5)
	{	ob_start();
		if ($page > 1)
		{	$start = ($page - 1) * $perpage;
		} else
		{	$start = 0;
		}
		$end = $start + $perpage;
		
		echo '<ul>';
		foreach ($events as $event)
		{	if (++$count > $start)
			{	if ($count > $end)
				{	break;
				}
				
				echo '<li><div class="pb_event_image">';
				if ($event['img'])
				{	
					echo $event['link'] ? $event['link'] : '', '<img src="', $event['img'], '" alt="', $event['title'], '" title="', $event['title'], '" />', $event['link'] ? '</a>' : '';
				}
				echo '</div><div class="pb_event_text"><p class="pb_text_header">', $event['link'] ? $event['link'] : '', $event['title'], $event['link'] ? '</a>' : '', '</p><p class="pb_text_sub">', $event['subtitle'], '</p></div><div class="clear"></div></li>';
			}
		}
		echo '</ul>';

		if (count($events) > $perpage)
		{	$pag = new AjaxPagination($_GET['page'], count($events), $perpage, 'instEventListContainer', 'ajax_instructor.php', array_merge($_GET, array('action'=>'events')));
			echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
		}
			
		return ob_get_clean();
	} // end of fn DisplayEventsListingList
	
	public function DisplayInterviewListing()
	{	ob_start();
		if ($interviews = $this->instructor->GetInterviews())
		{	echo '<div class="people_bottom_col"><h4>Interviews</h4><div id="instInterviewListContainer">', $this->DisplayInterviewListingList($interviews), '</div></div>';
		}
		return ob_get_clean();
	} // end of fn DisplayInterviewListing
	
	public function DisplayInterviewListingList($interviews, $page = 0, $perpage = 5)
	{	ob_start();
		if ($page > 1)
		{	$start = ($page - 1) * $perpage;
		} else
		{	$start = 0;
		}
		$end = $start + $perpage;
		
		echo '<ul>';
		foreach ($interviews as $interview_row)
		{	if (++$count > $start)
			{	if ($count > $end)
				{	break;
				}
				
				$interview = new InstructorInterview($interview_row);
				echo '<li><div class="pb_event_text_full"><p class="pb_text_header"><a href="', $interview->Link(), '">', $this->InputSafeString($interview->details['ivtitle']), '</a></p><p class="pb_text_sub">', date('j M Y', strtotime($interview->details['ivdate'])), '</p></div><div class="clear"></div></li>';
			}
		}
		echo '</ul>';

		if (count($interviews) > $perpage)
		{	$pag = new AjaxPagination($_GET['page'], count($interviews), $perpage, 'instInterviewListContainer', 'ajax_instructor.php', array_merge($_GET, array('action'=>'interviews')));
			echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
		}
			
		return ob_get_clean();
	} // end of fn DisplayInterviewListingList
	
	public function DisplayInterviewAndPostListing()
	{	ob_start();
		if ($posts = $this->instructor->GetInterviewsAndPosts())
		{	echo '<div class="people_bottom_col"><h4>Interviews/Articles</h4><div id="instInterviewListContainer">', $this->DisplayInterviewAndPostListingList($posts), '</div></div>';
		}
		return ob_get_clean();
	} // end of fn DisplayInterviewAndPostListing
	
	public function DisplayInterviewAndPostListingList($interviews, $page = 0, $perpage = 5)
	{	ob_start();
		if ($page > 1)
		{	$start = ($page - 1) * $perpage;
		} else
		{	$start = 0;
		}
		$end = $start + $perpage;
		
		echo '<ul>';
		foreach ($interviews as $ivpost)
		{	if (++$count > $start)
			{	if ($count > $end)
				{	break;
				}
				switch ($ivpost['type'])
				{	case 'interview':
						$interview = new InstructorInterview($ivpost['item']);
						echo '<li><div class="pb_event_text_full"><p class="pb_text_header"><a href="', $interview->Link(), '">', $this->InputSafeString($interview->details['ivtitle']), '</a></p><p class="pb_text_sub">', date('j M Y', strtotime($interview->details['ivdate'])), '</p></div><div class="clear"></div></li>';
						break;
					case'post':
						$post = new Post($ivpost['item']);
						//$this->VarDump($post->details);
						echo '<li><div class="pb_event_text_full"><p class="pb_text_header"><a href="', $this->link->GetPostLink($post), '">', $this->InputSafeString($post->details['ptitle']), '</a></p><p class="pb_text_sub">', date('j M Y', strtotime($post->details['pdate'])), '</p></div><div class="clear"></div></li>';
						break;
				}
			}
		}
		echo '</ul>';

		if (count($interviews) > $perpage)
		{	$pag = new AjaxPagination($_GET['page'], count($interviews), $perpage, 'instInterviewListContainer', 'ajax_instructor.php', array_merge($_GET, array('action'=>'interviewposts')));
			echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
		}
			
		return ob_get_clean();
	} // end of fn DisplayInterviewAndPostListingList
	
	public function DisplayMultimediaListing()
	{	ob_start();
		if ($multimedia = $this->instructor->GetMultiMediaAll())
		{	echo '<div class="people_bottom_col"><h4>Multimedia</h4><div id="instMultimediaListContainer">', $this->DisplayMultimediaListingList($multimedia), '</div></div>';
		}
		return ob_get_clean();
	} // end of fn DisplayMultimediaListing
	
	public function DisplayMultimediaListingList($multimedia, $page = 0, $perpage = 5)
	{	ob_start();
		if ($page > 1)
		{	$start = ($page - 1) * $perpage;
		} else
		{	$start = 0;
		}
		$end = $start + $perpage;
		
		echo '<ul>';
		foreach ($multimedia as $mm_row)
		{	if (++$count > $start)
			{	if ($count > $end)
				{	break;
				}
				
				$mm = new MultiMedia($mm_row);
				echo '<li><div class="pb_event_image"><img src="', $mm->Thumbnail(), '" title="', $title = $mm->InputSafeString($mm->details['mmname']), '" alt="', $title, '" /><a class="pb_event_image_link" href="', $link = $mm->Link(), '"></a></div><div class="pb_event_text"><p class="pb_text_header"><a href="', $link, '">', $title, '</a></p></div><div class="clear"></div></li>';
			}
		}
		echo '</ul>';

		if (count($multimedia) > $perpage)
		{	$pag = new AjaxPagination($_GET['page'], count($multimedia), $perpage, 'instMultimediaListContainer', 'ajax_instructor.php', array_merge($_GET, array('action'=>'multimedia')));
			echo '<div class="pagination">', $pag->Display(), '</div><div class="clear"></div>';
		}
			
		return ob_get_clean();
	} // end of fn DisplayMultimediaListingList
	
	public function DisplayTestimonials()
	{	ob_start();
		$reviewlist = $this->instructor->ReviewList(0);
		$reviewform = $this->user->ReviewForm($this->instructor->id, 'instructor');
		if ($reviewlist || $reviewform)
		{	echo '<div id="inst_reviews">', $reviewlist, $reviewform, '</div>';
		}
		return ob_get_clean();
	} // end of fn DisplayTestimonials
	
	public function GalleryListing()
	{	ob_start();
		if ($galleries = $this->instructor->GetGalleries())
		{	echo '<div id="people_galleries">',
				//'<h3>Photos</h3>',
				'<ul>';
			foreach ($galleries as $gallery_row)
			{	$gallery = new Gallery($gallery_row);
				foreach ($gallery->photos as $photo_row)
				{	$photo = new GalleryPhoto($photo_row);
					if ($img = $photo->HasImage('thumbnail'))
					{	echo '<li><a onclick="OpenGalleryPhoto(', $photo->id, ');"><img src="', $img, '" alt="', $title = $this->InputSafeString($photo->details['title']), '" title="', $title, '" /></a></li>';
					}
				}
			}
			echo '</ul><div class="clear"></div><script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow")); $("#gal_photo_modal_popup").jqm();});</script><!-- START gallery photo modal popup --><div id="gal_photo_modal_popup" class="jqmWindow"><a href="#" class="submit" onclick="CloseGalleryPhoto(); return false;">Close</a><div id="galPhotoModalInner"></div></div></div>';
		}
		return ob_get_clean();
	} // end of fn GalleryListing
	
	public function GalleryListingLightBox()
	{	ob_start();
		if ($galleries = $this->instructor->GetGalleries())
		{	echo '<div id="people_galleries">',
				//'<h3>Photos</h3>',
				'<ul>';
			foreach ($galleries as $gallery_row)
			{	$gallery = new Gallery($gallery_row);
				foreach ($gallery->photos as $photo_row)
				{	$photo = new GalleryPhoto($photo_row);
					if ($img = $photo->HasImage('thumbnail'))
					{	echo '<li><a href="', $photo->HasImage(), '"><img src="', $img, '" alt="', $title = $this->InputSafeString($photo->details['title']), '" title="', $title, '" /></a></li>';
					}
				}
			}
			echo '</ul><div class="clear"></div>',
				'<script type="text/javascript">$().ready(function(){$("#people_galleries ul li a").lightBox();});</script>',
				'</div>';
		}
		return ob_get_clean();
	} // end of fn GalleryListingLightBox
	
	public function MultiMediaListing()
	{	ob_start();
		if ($multimedia = $this->instructor->GetMultiMedia())
		{	echo //'<h3>Multimedia recommended for this instructor ...</h3>',
				'<div class="mm_list_container"><ul><li><ul>';
			foreach ($multimedia as $mm_row)
			{	$mm = new MultiMedia($mm_row);
				echo $mm->DisplayInList();
			}
			echo '</ul></li></ul></div>';
		}
		return ob_get_clean();
	} // end of fn MultiMediaListing
	
} // end of defn InstructorPage
?>