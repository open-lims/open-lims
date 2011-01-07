<?php
/**
 * @package method
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
 * 
 */
require_once("interfaces/method_type.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/method_type.access.php");
	require_once("access/method_cat.access.php");
}

/**
 * Method Type Management Class
 * @package method
 */
class MethodType implements MethodTypeInterface {

	private $method_type_id;
	private $method_type;

	/**
	 * @param integer $method_type_id
	 */
    function __construct($method_type_id) {
    	if ($method_type_id) {
			$this->method_type_id = $method_type_id;
			$this->method_type = new MethodType_Access($method_type_id);
		}else{
			$this->method_type_id = null;
			$this->method_type = new MethodType_Access(null);
		}
    }
    
    function __destruct()
    {
    	unset($this->method_type_id);
    	unset($this->method_type);
    }
    
    /**
     * Creates a new method-type
     * @param integer $toid
     * @param string $name
     * @param integer $cat_id
     * @param integer $location_id
     * @param string $description
     * @return integer
     */
    public function create($toid, $name, $cat_id, $location_id, $description)
    {
    	if ($this->method_type)
    	{
    		return $this->method_type->create($toid, $name, $cat_id, $location_id, $description);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Deletes a method-type
     * @return bool
     */
    public function delete()
    {
    	if ($this->method_type_id and $this->method_type)
    	{
    		if ($this->get_childs() != null)
			{
				return false;
			}
			else
			{
	    		$method_array = Method::list_entries_by_type_id($this->method_type_id);
	    		if (!is_array($method_array))
	    		{
	    			return $this->method_type->delete();
	    		}
	    		else
	    		{
	    			if (count($method_array) == 0)
	    			{
	    				return $this->method_type->delete();
	    			}
	    			else
	    			{
	    				return false;
	    			}
	    		}
			}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @return string
     */
    public function get_name()
    {
    	if ($this->method_type_id and $this->method_type)
    	{
    		return $this->method_type->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return string
     */
    public function get_description()
    {
    	if ($this->method_type_id and $this->method_type)
    	{
    		return $this->method_type->get_description();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public function set_name($name)
    {
    	if ($this->method_type_id and $this->method_type and $name)
    	{
    		return $this->method_type->set_name($name);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the cateogory-name of the current method-type
     * @return string
     */
    public function get_cat_name()
    {
    	if ($this->method_type_id and $this->method_type)
    	{
    		$method_cat = new MethodCat($this->method_type->get_cat_id());
    		return $method_cat->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the childs of the current method-type
     * @return array
     */
    public function get_childs()
    {
    	if ($this->method_type_id)
    	{
    		return MethodType_Access::list_entries_by_toid($this->method_type_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $id
     * @return bool
     */
    public static function exist_id($id)
    {
    	return MethodType_Access::exist_id($id);
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public static function exist_name($name)
    {
    	return MethodType_Access::exist_name($name);
    }
    
    /**
     * @return array
     */
    public static function list_entries_by_cat_id($cat_id)
    {
		return MethodType_Access::list_entries_by_cat_id($cat_id);
	}
    
    /**
     * @return array
     */
    public static function list_root_entries()
    {
    	return MethodType_Access::list_root_entries();
    }
    
    /**
     * @return array
     */
    public static function list_entries_by_id($id)
    {
		if (is_numeric($id))
		{
			$return_array = array();
			$toid_array = MethodType_Access::list_entries_by_toid($id);
			if (is_array($toid_array) and count($toid_array) >= 1)
			{
				foreach($toid_array as $key => $value)
				{
					array_push($return_array, $value);
					$return_array = array_merge($return_array, self::list_entries_by_id($value));
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
    
    /**
     * @return array
     */
    public static function list_entries()
    {
		return MethodType_Access::list_entries();
	}
	
}
?>