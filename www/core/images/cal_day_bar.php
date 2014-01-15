<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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

if ($_GET['length'] and $_GET['height'] and $_GET['value'])
{
	$background_red = 255;
	$background_green = 255;
	$background_blue = 255;

	if ($_GET['value'])
	{
		$value_red = hexdec($_GET['value']{0}."".$_GET['value']{1});
		$value_green = hexdec($_GET['value']{2}."".$_GET['value']{3});
		$value_blue = hexdec($_GET['value']{4}."".$_GET['value']{5});
	}
	
	header("Content-Type: image/png");

	$bar_length = $_GET['length'];
	$bar_height = $_GET['height'];

	$image = imagecreatetruecolor($bar_length, $bar_height);
	
	$background = imagecolorallocate($image, $background_red, $background_green, $background_blue);

	imagefill($image, 0, 0, $background);

	if ($_GET['value'])
	{
		$fill = imagecolorallocate($image, $value_red, $value_green, $value_blue);
		imagefilledrectangle($image, 1, 1, $bar_length, $bar_height, $fill);
	} 
	
	imagepng($image);
	imagedestroy($image);
}

?>
