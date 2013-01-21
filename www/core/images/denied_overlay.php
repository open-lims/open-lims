<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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


$image = "../../".$_GET['image'];
$denied_overlay = "../../images/icons/denied_overlay.png";
	
if (file_exists($image) and file_exists($denied_overlay))
{
	header("Content-Type: image/png");

	$source_image = imagecreatefrompng($image);
	$denied_image = imagecreatefrompng($denied_overlay);
	

	$new_image = imagecreatetruecolor(16, 16);
	
	imagesavealpha($new_image, true);
	$trans_color = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
	imagefill($new_image, 0, 0, $trans_color);
	
	
	imagecopy($new_image, $source_image, 0, 0, 0, 0, 16, 16);
	
	imagecopy($new_image, $denied_image, 0, 0, 0, 0, 16, 16);
	
	imagepng($new_image);
				
	imagedestroy($new_image);
}
?>
