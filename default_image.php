<?php 
require_once('init.php');

class DefaultImage extends Base
{	private $def_img = '/img/default/default.jpg';
	private $def_imagetype = 'jpg';

	function __construct()
	{	parent::__construct();		
		if (($width = (int)$_GET['width']) && ($height = (int)$_GET['height']))
		{	$this->def_img = CITDOC_ROOT . $this->def_img;
			if ($img = $this->OutputImage($width, $height, $_GET['bcolour'], $_GET['bwidth']))
			{	header('Pragma: ');
				header('Cache-Control: ');
				header('Content-Type: image/jpeg');
				header('Content-Disposition: attachment; filename="iidr_default.jpeg"');
				echo $img;
				exit;
			}
		}
	} // end of fn __construct
	
	function OutputImage($maxwidth = 0, $maxheight = 0, $bordercolour = '', $borderwidth = 0)
	{	$isize = getimagesize($this->def_img);
		$ratio = $maxwidth / $isize[0];
		$h_ratio = $maxheight / $isize[1];
		if ($h_ratio > $ratio)
		{	$ratio = $h_ratio;
		}
		switch ($this->def_imagetype)
		{	case 'png': $oldimage = imagecreatefrompng($this->def_img);
							break;
			case 'jpg':
			case 'jpeg': $oldimage = imagecreatefromjpeg($this->def_img);
							break;
		}
		
		if ($oldimage)
		{	$w_new = ceil($isize[0] * $ratio);
			$h_new = ceil($isize[1] * $ratio);
			
			if ($maxwidth && $maxheight && $ratio != 1)
			{	$newimage = imagecreatetruecolor($w_new,$h_new);
				imagecopyresampled($newimage, $oldimage,0,0,0,0,$w_new, $h_new, $isize[0], $isize[1]);
			} else
			{	$newimage = $oldimage;
			}
			
			// now get middle chunk - horizontally
			if ($maxwidth && $maxheight && ($w_new > $maxwidth || $h_new > $maxheight))
			{	$resizeimg = imagecreatetruecolor($maxwidth, $maxheight);
				$leftoffset = floor(($w_new - $maxwidth) / 2);
				imagecopyresampled($resizeimg, $newimage, 0, 0, floor(($w_new - $maxwidth) / 2), floor(($h_new - $maxheight) / 2), $maxwidth, $maxheight, $maxwidth, $maxheight);
				$newimage = $resizeimg;
			}
			
			// add border if required
			if ($rgb = $this->HexToRGB($bordercolour))
			{	$colour = imagecolorallocate($newimage, $rgb['R'], $rgb['G'], $rgb['B']);
				if ($borderwidth = (int)$borderwidth)
				{	$borderwidth = $borderwidth - 1;
				}
				imagefilledrectangle($newimage, 0, 0, $maxwidth, $borderwidth, $colour);
				imagefilledrectangle($newimage, 0, $maxheight - 1, $maxwidth, $maxheight - 1 - $borderwidth, $colour);
				imagefilledrectangle($newimage, $maxwidth - 1, 0, $maxwidth - 1 - $borderwidth, $maxheight, $colour);
				imagefilledrectangle($newimage, 0, 0, $borderwidth, $maxheight, $colour);
			}
			
			ob_start();
			imagejpeg($newimage, NULL, 100);
			return ob_get_clean();
		}
	} // end of fn ReSizePhotoPNG
	
	public function HexToRGB($bordercolour = '')
	{	if ($this->ValidColourString($bordercolour))
		{	switch (strlen($bordercolour))
			{	case 3: 
					return array('R'=>'0x' . substr($bordercolour, 0, 1) . substr($bordercolour, 0, 1), 'G'=>'0x' . substr($bordercolour, 1, 1) . substr($bordercolour, 1, 1), 'B'=>'0x' . substr($bordercolour, 2, 1) . substr($bordercolour, 2, 1));
					break;
				case 6: return array('R'=>'0x' . substr($bordercolour, 0, 2), 'G'=>'0x' . substr($bordercolour, 2, 1), 'B'=>'0x' . substr($bordercolour, 4, 1));
					break;
			}
		}
	} // end of fn HexToRGB
	
} // end of defn DefaultImage

$img = new DefaultImage();
?>