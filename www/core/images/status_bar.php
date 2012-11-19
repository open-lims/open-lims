<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
 * @license GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */
 
 
if ($_GET[length] and $_GET[height] and $_GET[color])
{
	if (!$_GET[value])
	{
		$_GET[value] = 0;
	}

	/*
	 * bgcolor and linecolor is optional
	 */
	 
	if (!$_GET[bgcolor])
	{
		$background_red = 255;
		$background_green = 255;
		$background_blue = 255;
	}
	else
	{
		$background_red = hexdec($_GET[bgcolor]{0}."".$_GET[bgcolor]{1});
		$background_green = hexdec($_GET[bgcolor]{2}."".$_GET[bgcolor]{3});
		$background_blue = hexdec($_GET[bgcolor]{4}."".$_GET[bgcolor]{5});
	}
			
	if (!$_GET[linecolor])
	{
		$line_red = 0;
		$line_green = 0;
		$line_blue = 0;
	}
	else
	{
		$line_red = hexdec($_GET[linecolor]{0}."".$_GET[linecolor]{1});
		$line_green = hexdec($_GET[linecolor]{2}."".$_GET[linecolor]{3});
		$line_blue = hexdec($_GET[linecolor]{4}."".$_GET[linecolor]{5});
	}

	header("Content-Type: image/png");

	$bar_length = $_GET[length];
	$bar_height = $_GET[height];
	
	$fill_red = hexdec($_GET[color]{0}."".$_GET[color]{1});
	$fill_green =hexdec($_GET[color]{2}."".$_GET[color]{3});
	$fill_blue = hexdec($_GET[color]{4}."".$_GET[color]{5});
	
	$image = imagecreatetruecolor($bar_length, $bar_height);
	
	$background = imagecolorallocate($image, $background_red, $background_green, $background_blue);
	$line = imagecolorallocate($image, $line_red, $line_green, $line_blue);
	$fill = imagecolorallocate($image, $fill_red, $fill_green, $fill_blue);
	
	
	imagefill($image, 0, 0, $background);
	
	imageline($image, 1, 1, $bar_length-1, 1, $line);
	imageline($image, 1, 1, 1, $bar_height-1, $line);
	imageline($image, 1, $bar_height-1, $bar_length-1, $bar_height-1, $line);
	imageline($image, $bar_length-1, 1, $bar_length-1, $bar_height-1, $line);
	
	$percent = $_GET[value];
	
	if ($percent > 100)
	{
		$percent = 100;
	}
	
	$max_bar_size = $bar_length-2;
	$bar_size = $max_bar_size/100*$percent;
	
	if ($bar_size > 1)
	{
		imagefilledrectangle($image, 2, 2, $bar_size, $bar_height-2, $fill);
	}
	
	imagepng($image);
	imagedestroy($image);

}

?>
