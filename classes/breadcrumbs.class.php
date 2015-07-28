<?php
class BreadCrumbs extends Base
{	var $crumbs = array();
	var $crumbLinker = "<strong style='font-size: 1.3em;'>&nbsp;&raquo;&nbsp;</strong>";
	var $rightLinks = array();

	function __construct($homelink = "", $homedesc = "")
	{	parent::__construct();
		$this->AddCrumb($homelink, $homedesc ? $homedesc : "homepage");
	} // end of fn __constructor

	function AddCrumb($link = "", $text = "")
	{	$this->crumbs[] = array("link"=>$link, "text"=>$text);
	} // end of fn AddCrumb

	function ResetCrumbs()
	{	$this->crumbs = array();
	} // end of fn ResetCrumbs

	function ResetRightLinks()
	{	$this->rightLinks = array();
	} // end of fn ResetRightLinks

	function AddRightLink($link = "", $text = "")
	{	$this->rightLinks[] = array("link"=>$link, "text"=>$text);
	} // end of fn AddRightLink

	function Display()
	{	$crumbs = array();
		foreach ($this->crumbs as $count=>$crumb)
		{	if (($count == count($this->crumbs) - 1) || !$crumb["link"]) // i.e. last crumb or no link
			{	$crumbs[] = "<span class='bcCrumb'>{$crumb["text"]}</span>";
			} else
			{	$crumbs[] = "<a class='bcCrumb' href='{$crumb["link"]}'>{$crumb["text"]}</a>";
			}
		}
		echo "<div class='bcBody'>";
		$this->RightLink();
		echo implode($this->crumbLinker, $crumbs), 
				"<div class='clear'></div></div>\n";
	} // end of fn Display

	function RightLink()
	{	echo "<div class='bcRightLink'>";
		foreach ($this->rightLinks as $rlink)
		{	echo "<a href='", $rlink["link"], "' target='_blank'>", $rlink["text"], "</a>";
		}
		echo "<a href='index.php?logout=1'>log out</a></div>\n";

	} // end of fn RightLink

} // end of class defn BreadCrumbs

class NoBreadCrumbs extends Breadcrumbs
{	function __constructor(){} // dummy constructor

	function Display(){} // dummy function

} // end of class defn NoBreadCrumbs
?>