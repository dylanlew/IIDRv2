<?php
// graph.class.php
// written by Tim Ward Apr 2006
//
// base for creating jpeg graphs from dataset

class Graph
{	
	protected $dataWidth = 400;
	protected $dataHeight = 200;
	protected $dataXStart = 0; // defined as data units
	protected $dataYStart = 0; // defined as data units
	protected $dataXFactor = 1; // pixcels per data unit in x direction
	protected $dataYFactor = 1; // pixcels per data unit in y direction
	protected $data = array();
	protected $padding = 20;
	protected $x_axisGap = 20;
	protected $y_axisGap = 30;
	private $backColour;
	protected $backColourNum = 0xFFFF99;
	private $axisColour;
	protected $axisColourNum = 0x0000FF;
	private $lineColours = array();
	protected $lineColourNums = array(0=>0xFF0000, 1=>0x00FF00, 2=>0x0000FF);
	protected $font = "";
	protected $fontdir = "";
	protected $fontname = "verdana.ttf";
	protected $titleHeight = 20;
	protected $titleString = "";
	protected $titleFontSize = 12;
	protected $axisFontSize = 8;
	protected $legend = array();
	protected $legendHeight = 12;
	protected $legendFontSize = 8;
	protected $perXTagfactor = 20; // number of values marked on x-axis

	public function Graph() // constructor
	{	
		$this->fontdir = CITDOC_ROOT . "/fonts/";
		$this->font = $this->fontdir . $this->fontname;
		$this->DefineData();
		
		if (!$this->titleString) $this->titleHeight = 0;
		if (!count($this->legend)) $this->legendHeight = 0;

		$this->graph = imagecreatetruecolor(
					$width = $this->dataWidth + $this->y_axisGap + ($this->padding * 2), 
					$height = $this->dataHeight + $this->x_axisGap + ($this->padding * 2) 
												+ $this->titleHeight + $this->legendHeight);
		$this->axisColour = imagecolorallocate($this->graph, ($this->axisColourNum >> 16) & 0xFF, 
									($this->axisColourNum >> 8) & 0xFF, $this->axisColourNum & 0xFF);
		$this->backColour = imagecolorallocate($this->graph, ($this->backColourNum >> 16) & 0xFF, 
									($this->backColourNum >> 8) & 0xFF, $this->backColourNum & 0xFF);

		foreach ($this->lineColourNums as $count=>$col)
		{	$this->lineColours[$count] = imagecolorallocate($this->graph, ($col >> 16) & 0xFF, 
									($col >> 8) & 0xFF, $col & 0xFF);
		}
		
		imagefilledrectangle($this->graph, 0, 0, $width, $height, $this->backColour);
		
		$this->DrawAxis();
		$this->DrawAllData();
		if ($this->titleString) $this->DrawTitle();
		if (count($this->legend)) $this->DrawLegend();
		
	} // end of constructor

	function DrawLegend()
	{	$left = $this->padding + $this->y_axisGap + $this->legendFontSize;
		$y = $this->dataHeight + $this->x_axisGap + $this->titleHeight 
									+ $this->padding + $this->legendFontSize + 2;
		foreach ($this->legend as $count=>$str)
		{	$size = imagettfbbox($this->legendFontSize, 0, $this->font, $str);
			imagefttext($this->graph, $this->legendFontSize, 0, $left, $y, $this->axisColour, 
												$this->font, $str);
			$left += (3 + $size[4]);
			imagefilledrectangle($this->graph, $left, $y, $left + $this->legendFontSize, 
							$y - $this->legendFontSize, $this->lineColours[$count]);
			$left += ($this->legendFontSize * 2);
			
		}
		
	} // end of DrawLegend

	function DrawTitle()
	{	
		$size = imagettfbbox($this->titleFontSize, 0, $this->font, $this->titleString);
		imagefttext($this->graph, $this->titleFontSize, 0, 
					floor(($this->dataWidth + $this->x_axisGap + ($this->padding * 2) - $size[2]) / 2), 
					3 - $size[5], 
					$this->axisColour, $this->font, $this->titleString);
		
	} // end of DrawTitle

	public function DrawAxis()
	{	
		// y-axis
		imageline($this->graph, 
					$this->padding + $this->y_axisGap, 
					$this->padding + $this->titleHeight, 
					$this->padding + $this->y_axisGap, 
					$this->padding + $this->x_axisGap + $this->dataHeight + $this->titleHeight, 
					$this->axisColour);
		// x-axis
		imageline($this->graph, 
					$this->padding, 
					$this->padding + $this->dataHeight + $this->titleHeight, 
					$this->padding + $this->y_axisGap + $this->dataWidth, 
					$this->padding + $this->dataHeight + $this->titleHeight, 
					$this->axisColour);
		
		$this->DrawAxisLabels();
		
	} // end of DrawAxis

	public function DrawAxisLabels()
	{	
		$xtag_top = $this->padding + $this->dataHeight - 2 + $this->titleHeight;
		$xtag_bottom = $this->padding + $this->dataHeight + 2 + $this->titleHeight;
		
		$ytag_left = $this->padding + $this->y_axisGap - 2;
		$ytag_right = $this->padding + $this->y_axisGap + 2;
		
		$xdataEnd = ($this->dataWidth / $this->dataXFactor) + $this->dataXStart;

		// get max height of x-tags
		$tagy = 0;
		foreach ($this->data as $point)
		{	$size = imagettfbbox($this->axisFontSize, 0, $this->font, $point["n"]);
			$y = 3 + $this->padding + $this->dataHeight - $size[5] + $this->titleHeight;
			if ($y > $tagy) $tagy = $y;
		}
		
		$pertag = ceil(count($this->data) / $this->perXTagfactor);

		foreach ($this->data as $count=>$point)
		{	if (!($count % $pertag))
			{	$x = $this->padding + $this->y_axisGap + ($count * $this->dataXFactor);
				imageline($this->graph, $x, $xtag_top, $x, $xtag_bottom, $this->axisColour);
				// do label for tag
			 	$size = imagettfbbox($this->axisFontSize, 0, $this->font, $point["n"]);
		 		$tagx = $x - round($size[2] / 2, 0);
				imagefttext($this->graph, $this->axisFontSize, 0, $tagx, $tagy, 
									$this->axisColour, $this->font, $point["n"]);
			}
		}
		
		
		$ydu_per_tag = ceil($this->dataHeight / ($this->dataYFactor * 10)); // data units per tag
		$ydataEnd = ($this->dataHeight / $this->dataYFactor) + $this->dataYStart;

		for ($tag = $this->dataYStart; $tag <= $ydataEnd; $tag += $ydu_per_tag)
		{	
			$y = $this->padding + $this->dataHeight + $this->titleHeight 
									- (($tag - $this->dataYStart) * $this->dataYFactor);

			imageline($this->graph, $ytag_left, $y, $ytag_right, $y, $this->axisColour);

			// do label for tag
		 	$size = imagettfbbox($this->axisFontSize, 0, $this->font, $tag);
			$tagx = $this->padding + $this->y_axisGap - $size[4] - 4;
		 	$tagy = $y - round($size[5] / 2, 0);
			imagefttext($this->graph, $this->axisFontSize, 0, $tagx, $tagy, 
									$this->axisColour, $this->font, $tag);
		}

	} // end of DrawAxisLabels

	protected function DefineData()
	{	
		$this->GetData();
		$this->DefineXFactor();
		$this->DefineYFactor();
		
	} // end of DefineData

	protected function DefineXFactor()
	{	if ($this->data)
		{	$this->dataXFactor = $this->dataWidth / count($this->data);
		}
	} // end of DefineXFactor

	protected function DefineYFactor($zerofloor = true)
	{	
	  	$max = -999999999;
	  	$min = 99999999;
		foreach ($this->data as $value)
		{	foreach($value["y"] as $point)
			{	if ($point > $max) $max = $point;
				if ($point < $min) $min = $point;
			}
		}
		if (!$zerofloor)
		{	$this->dataYStart = floor($min);
		}
//echo $max, "\n", $this->dataYStart, "\n", $this->dataHeight;
		if ($max > $this->dataYStart) $this->dataYFactor = $this->dataHeight / ($max - $this->dataYStart);

	} // end of DefineYFactor

	protected function GetData()
	{	
	} // end of DefineData

	function DrawAllData()
	{	
		$xcount = 0;
		$last = array();
		foreach($this->data as $value)
		{
			$x = $xcount * $this->dataXFactor;
			foreach ($value["y"] as $set=>$point)
			{	$y = ($point - $this->dataYStart) * $this->dataYFactor;
					
				if ($last[$set])
				{	imageline($this->graph, 
								$this->padding + $this->y_axisGap + $x, 
								$this->padding + $this->dataHeight 
												+ $this->titleHeight - $y, 
								$this->padding + $this->y_axisGap + $last[$set]["x"], 
								$this->padding + $this->dataHeight 
												+ $this->titleHeight - $last[$set]["y"], 
								$this->lineColours[$set]);
					
				}
				
				$last[$set] = array("x"=>$x, "y"=>$y);
			}
			$xcount++;
		}
		
	} // end of DrawData

	public function Output($filename = "")
	{	
		if (!$filename)
		{	header("Pragma: ");
			header("Cache-Control: ");
			header("Content-type: image/jpeg");
			header("Content-Disposition: attachment; filename=\"ws-graph.jpeg\"");
		}
		
		imagejpeg($this->graph, $filename, 70);
		imagedestroy($this->graph);
	
	} // end of Output

} // end of defn Graph
?>