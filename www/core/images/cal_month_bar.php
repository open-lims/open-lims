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
 
 
if ($_GET[length] and $_GET[height])
{
	$background_red = 255;
	$background_green = 255;
	$background_blue = 255;

	if ($_GET[value1])
	{
		$value1_red = hexdec($_GET[value1]{0}."".$_GET[value1]{1});
		$value1_green = hexdec($_GET[value1]{2}."".$_GET[value1]{3});
		$value1_blue = hexdec($_GET[value1]{4}."".$_GET[value1]{5});
	}
	
	if ($_GET[value2])
	{
		$value2_red = hexdec($_GET[value2]{0}."".$_GET[value2]{1});
		$value2_green = hexdec($_GET[value2]{2}."".$_GET[value2]{3});
		$value2_blue = hexdec($_GET[value2]{4}."".$_GET[value2]{5});
	}
	
	if ($_GET[value3])
	{
		$value3_red = hexdec($_GET[value3]{0}."".$_GET[value3]{1});
		$value3_green = hexdec($_GET[value3]{2}."".$_GET[value3]{3});
		$value3_blue = hexdec($_GET[value3]{4}."".$_GET[value3]{5});
	}
	
	if ($_GET[value4])
	{
		$value4_red = hexdec($_GET[value4]{0}."".$_GET[value4]{1});
		$value4_green = hexdec($_GET[value4]{2}."".$_GET[value4]{3});
		$value4_blue = hexdec($_GET[value4]{4}."".$_GET[value4]{5});
	}
	
	header("Content-Type: image/png");

	$bar_length = $_GET[length];
	$bar_height = $_GET[height];

	$image = imagecreatetruecolor($bar_length, $bar_height);
	
	$background = imagecolorallocate($image, $background_red, $background_green, $background_blue);

	imagefill($image, 0, 0, $background);

	$value_pos = ceil($bar_height/4);

	if ($_GET[value1])
	{
		$fill = imagecolorallocate($image, $value1_red, $value1_green, $value1_blue);
		imagefilledrectangle($image, 1, 1, $bar_length, $value_pos, $fill);
	}
	
	if ($_GET[value2])
	{
		$fill = imagecolorallocate($image, $value2_red, $value2_green, $value2_blue);
		imagefilledrectangle($image, 1, $value_pos, $bar_length, ($value_pos*2), $fill);
	}
	
	
	if ($_GET[value3])
	{
		$fill = imagecolorallocate($image, $value3_red, $value3_green, $value3_blue);
		imagefilledrectangle($image, 1, ($value_pos*2), $bar_length, ($value_pos*3), $fill);
	}
	
	if ($_GET[value4])
	{
		$fill = imagecolorallocate($image, $value4_red, $value4_green, $value4_blue);
		imagefilledrectangle($image, 1, ($value_pos*3), $bar_length, ($value_pos*4), $fill);
	} 
	
	imagepng($image);
	imagedestroy($image);
}

?>
