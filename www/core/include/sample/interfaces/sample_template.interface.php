<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Sample Template Interface
 * @package sample
 */ 	 
interface SampleTemplateInterface
{
	/**
	 * @param integer $sample_template_id
	 */
	function __construct($sample_template_id);
	
	function __destruct();
	
	/**
	 * Creates a new sample-template
	 * @param integer $object_id
	 * @param integer $category_id
	 * @return bool
	 */
	public function create($object_id, $category_id);
	
	/**
	 * Deletes a sample-tempalte
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return integer
	 */
	public function get_cat_id();
	
	/**
	 * @return array
	 */
	public function get_information_fields();
	
	/**
	 * Checks if a required-section exists in template
	 * @return bool
	 */
	public function is_required_requirements();
	
	/**
	 * Returns the requirements of the required-section
	 * @return array
	 */
	public function get_required_requirements();
	
	/**
	 * Returns the requirements of the sample
	 * @return array
	 */
	public function get_requirements();
	
	/**
	 * Returns the attributes of a given gid-entry
	 * @param integer $gid
	 * @return array
	 */
	public function get_gid_attributes($gid);
	
	/**
	 * Returns an item-element selected by GID
	 * @param integer $gid
	 * @return array
	 */
	public function get_gid_item($gid);
	
	/**
	 * Returns information about an class (elemtens inside class-element) from sample-template
	 * @param string $class_name
	 * @return array
	 */
	public function get_class($class_name);
	
	/**
	 * Checks a given id
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id);
	
	/**
	 * Returns a list of all templates in a category
	 * @param integer $cat_id
	 * @return array
	 */
	public static function list_entries_by_cat_id($cat_id);
	
	/**
	 * @return array
	 */
	public static function list_entries();	
}
?>
