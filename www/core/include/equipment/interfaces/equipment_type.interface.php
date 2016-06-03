<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Equipment Type Management Interface
 * @package equipment
 */ 		 
interface EquipmentTypeInterface
{
	/**
	 * @param integer $equipment_type_id
	 */
	function __construct($equipment_type_id);
	
	function __destruct();
	
	/**
     * Creates a new equipment-type
     * @param integer $toid
     * @param string $name
     * @param integer $cat_id
     * @param integer $location_id
     * @param string $description
     * @return integer
     */
	public function create($toid, $name, $cat_id, $location_id, $description, $manufacturer);
	
	/**
     * Deletes a equipment-type
     * @return bool
     */
	public function delete();
	
	/**
     * @return string
     */
	public function get_name();
	
	/**
     * @return string
     */
    public function get_internal_name();
    
    /**
     * @return string
     */
    public function get_manufacturer();
    
     /**
     * @return string
     */
    public function get_description();
    
    /**
     * Returns the cateogory-name of the current equipment-type
     * @return string
     */
    public function get_cat_id();
	
 	/**
     * Returns the cateogory-name of the current equipment-type
     * @return string
     */
	public function get_cat_name();
	
	/**
     * @return integer
     */
    public function get_location_id();
    
	/**
     * Returns the childs of the current equipment-type
     * @return array
     */
	public function get_children();
	
	/**
     * @param string $name
     * @return bool
     */
	public function set_name($name);
	
	/**
     * @param string $name
     * @return bool
     */
    public function set_manufacturer($manufacturer);
    
    /**
     * @param string $name
     * @return bool
     */
    public function set_location_id($location_id);
    
    /**
     * @param integer $user_id
     * @return bool
     */
    public function add_responsible_person($user_id);
    
    /**
     * @param integer $user_id
     * @return bool
     */
    public function delete_responsible_person($user_id);
    
     /**
     * @param integer $user_id
     * @return bool
     */
    public function is_user_responsible($user_id);
    
    /**
     * @return array
     */
    public function list_users();
    
    /**
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function add_organisation_unit($organisation_unit_id);
    
    /**
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function delete_organisation_unit($organisation_unit_id);
    
    /**
     * @param integer $organisation_unit
     * @return bool
     */
    public function is_organisation_unit($organisation_unit_id);
    
    /**
     * @return array
     */
    public function list_organisation_units();
    
	/**
     * @param integer $id
     * @return bool
     */
	public static function exist_id($id);
	
	/**
     * @param string $name
     * @return bool
     */
	public static function exist_name($name);
	
	/**
     * @return array
     */
    public static function list_entries_by_cat_id($cat_id);
    
	/**
     * @return array
     */
	public static function list_root_entries();
	
	/**
     * @return array
     */
    public static function list_entries_by_id($id);
	
    /**
     * @return array
     */
	public static function list_entries();
}

?>
