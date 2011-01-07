<?
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Item Management Interface
 * @package item
 */ 	 
interface ItemInterface
{
	function __construct($item_id);
	function __destruct();
	
	public function create();
	public function delete();
	
	public function link_object($object_id);
	public function link_method($method_id);
	public function link_sample($sample_id);
	
	public function unlink_object();
	public function unlink_method();
	public function unlink_sample();
	
	public function is_classified();
	public function get_class_ids();
	public function get_information();
	public function get_datetime();
	public function get_object_id();
	public function get_method_id();
	public function get_sample_id();
	
	public static function get_id_by_object_id($object_id);
	public static function get_id_by_method_id($method_id);
	public static function get_id_by_sample_id($sample_id);
}

?>
