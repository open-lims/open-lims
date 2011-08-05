<?php
/**
 * @package project
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
 * Project Item Interface
 * @package project
 */ 		 
interface ProjectItemInterface
{
	/**
	 * @param integer $project_id
	 */
	function __construct($project_id);
	
	function __destruct();
	
	/**
     * Links an item to the project
     * @return bool
     */
	public function link_item();
	
	/**
     * Unlinks an item from a specific project
     * @return bool
     */
	public function unlink_item();
	
	/**
     * Unlinks an item from all projects
     * @return bool
     */
	public function unlink_item_full();
	
	/**
     * Set item as active
     * @param bool $active
     * @return bool
     */
	public function set_active($active);
	
	/**
     * Set item as required item
     * @param bool $required
     * @return bool
     */
	public function set_required($required);
	
	/**
     * @return bool
     */
	public function is_active();
	
	/**
     * @return bool
     */
	public function is_required();
	
	/**
     * @return array
     */
	public function get_project_items();
	
	/**
     * @return array
     */
    public function get_project_status_items($project_status_id);
    
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
     * @param integer $status_id
     * @return bool
     */
	public function set_status_id($status_id);
	
	/**
     * @return bool
     */
	public function set_item_status();
	
	/**
     * @return bool
     */
	public function unset_item_status();
	
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
     * Creates a log-entry, that a new item is links and links the item to the log-entry
     * @return bool
     */
	public function create_log_entry();
	
	/**
     * @param integer $item_id
     * @param integer $project_id
     * @param integer $project_status_id
     * @return array
     */
    public static function get_gid_by_item_id_and_project_id($item_id, $project_id, $project_status_id);
    
    /**
     * @param integer $item_id
     * @param integer $project_id
     * @return array
     */
    public static function get_gid_by_item_id_and_status_id($item_id, $status_id);
	
    /**
  	 * Returns a list of project related items
  	 * @return array
  	 */
	public static function list_projects_by_item_id($item_id);
}
?>
