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


/**
 * Paper Size Interface
 * @package base
 */
interface PaperSizeInterface
{
	/**
	 * @param $paper_size_id
	 */
	function __construct($paper_size_id);
	
	function __destruct();
	
	/**
	 * @param string $name
	 * @param float $width
	 * @param float $height
	 * @param float $margin_left
	 * @param float $margin_right
	 * @param float $margin_top
	 * @param float $margin_bottom
	 * @return integer
	 */
	public function create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom);
	
	/**
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return float
	 */
	public function get_width();
	
	/**
	 * @return float
	 */
	public function get_height();
	
	/**
	 * @return float
	 */
	public function get_margin_left();
	
	/**
	 * @return float
	 */
	public function get_margin_right();
	
	/**
	 * @return float
	 */
	public function get_margin_top();
	
	/**
	 * @return float
	 */
	public function get_margin_bottom();
	
	/**
	 * @return float
	 */
	public function get_base();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param float $width
	 * @return bool
	 */
	public function set_width($width);
	
	/**
	 * @param float $height
	 * @return bool
	 */
	public function set_height($height);
	
	/**
	 * @param float $margin_left
	 * @return bool
	 */
	public function set_margin_left($margin_left);
	
	/**
	 * @param float $margin_right
	 * @return bool
	 */
	public function set_margin_right($margin_right);
	
	/**
	 * @param float $margin_top
	 * @return bool
	 */
	public function set_margin_top($margin_top);
	
	/**
	 * @param float $margin_bottom
	 * @return bool
	 */
	public function set_margin_bottom($margin_bottom);
	
	/**
	 * @return array
	 */
	public static function list_entries();
	
	/**
	 * @param integer $id
	 * @return array
	 */
	public static function get_size_by_id($id);
	
	/**
	 * @param integer $id
	 * @return array
	 */
	public static function get_standard_size();
}
?>