<?php
/**
 * @package sample
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
 * Sample Item Management Interface
 * @package sample
 */ 		 
interface SampleItemInterface
{
	/**
	 * @param integer $sample_id
	 */
	function __construct($sample_id);
	
	function __destruct();
	
	/**
     * Links an item to the sample
     * @return bool
     */
	public function link_item();
	
	/**
     * Unlinks an item from a specific sample
     * @return bool
     */
	public function unlink_item();
	
	/**
     * Unlinks an item from all samples
     * @return bool
     */
	public function unlink_item_full();
	
	/**
     * @return array
     */
	public function get_sample_items();
	
	/**
     * @param integer $item_id
     * @return bool
     */
	public function set_item_id($item_id);
	
	/**
     * @param integer $gid
     * @return bool
     */
	public function set_gid($gid);
	
	/**
     * @param integer $parent_item_id
     * @return bool
     */
	public function set_parent_item_id($parent_item_id);
	
	/**
     * Adds the Item to a class
     * @param string $class_name
     * @return bool
     */
	public function set_class($class_name);
	
	/**
     * Removes the Item from a class
     * @return bool
     */
	public function unset_class();
	
	/**
     * Sets Item-Information to Class or Item
     * @param string $description
     * @param string $keywords
     * @return bool
     */
	public function set_information($description, $keywords);
	
	/**
     * Checks if an Item needs a description
     * @return bool
     */
	public function is_description();
	
	/**
     * Checks if an Item needs keywords
     * @return bool
     */
	public function is_keywords();
	
	/**
     * Checks if an Item needs a description
     * @return bool
     */
    public function is_description_required();
    
    /**
     * Checks if an Item needs keywords
     * @return bool
     */
    public function is_keywords_required();
	
    /**
     * Checks if an Item is classified
     * @return bool
     */
	public function is_classified();
	
	/**
     * @param integer $item_id
     * @return array
     */
    public static function list_entries_by_item_id($item_id, $sub_items);
    
    /**
     * @param integer $item_id
     * @param integer $sample_id
     * @return integer
     */
    public static function get_gid_by_item_id_and_sample_id($item_id, $sample_id);
    
    /**
	 * @param integer $item_id
	 * @param integer $gid
	 * @return array
	 */
	public static function list_sample_id_by_item_id_and_gid_and_parent($item_id, $gid);
	
	/**
	 * Deletes parent-sample sub-items
	 * @param integer $sample_id
	 * @return bool
	 */
	public static function delete_remaining_sample_entries($sample_id);
}

?>
