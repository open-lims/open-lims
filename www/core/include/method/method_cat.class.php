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
require_once("interfaces/method_cat.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/method_category_not_found_exception.class.php");
	
	require_once("access/method_cat.access.php");
}

/**
 * Method Category Management Class
 * @package method
 */
class MethodCat implements MethodCatInterface
{
	private $method_cat_id;
	private $method_cat;

	/**
	 * @param integer $method_cat_id
	 */
	function __construct($method_cat_id)
	{
    	if ($method_cat_id)
    	{
			$this->method_cat_id = $method_cat_id;
			$this->method_cat = new MethodCat_Access($method_cat_id);
		}
		else
		{
			$this->method_cat_id = null;
			$this->method_cat = new MethodCat_Access(null);
		}
    }
    
    function __destruct()
    {
    	unset($this->method_cat_id);
    	unset($this->method_cat);
    }

	/**
	 * Creates a method-category
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $name)
	{
		if ($this->method_cat)
    	{
    		return $this->method_cat->create($toid, $name);
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * Deletes a method-category
	 * @return bool
	 */
	public function delete()
	{
		if ($this->method_cat_id and $this->method_cat)
    	{
    		if ($this->get_childs() != null)
			{
				return false;
			}
			else
			{
	    		$method_type_array = MethodType::list_entries_by_cat_id($this->method_cat_id);
	    		if (!is_array($method_type_array))
	    		{
	    			return $this->method_cat->delete();
	    		}
	    		else
	    		{
	    			if (count($method_type_array) == 0)
	    			{
	    				return $this->method_cat->delete();
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
    		echo "e";
    		return false;
    	}
	}
	
	/**
	 * @return integer
	 */
	public function get_name()
	{
		if ($this->method_cat_id and $this->method_cat)
    	{
    		return $this->method_cat->get_name();
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
		if ($this->method_cat_id and $this->method_cat and $name)
    	{
    		return $this->method_cat->set_name($name);
    	}
    	else
    	{
    		return null;
    	}
	}

	/**
	 * Returns the child of the current method-category
	 * @return array
	 */
   	public function get_childs()
    {
    	if ($this->method_cat_id)
    	{
    		return MethodCat_Access::list_entries_by_toid($this->method_cat_id);
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
    	return MethodCat_Access::exist_id($id);
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public static function exist_name($name)
    {
    	return MethodCat_Access::exist_name($name);
    }
    
    /**
     * @return array
     */
    public static function list_root_entries()
    {
    	return MethodCat_Access::list_root_entries();
    }
    
    /**
     * @return array
     */
    public static function list_entries()
    {
		return MethodCat_Access::list_entries();
	}
    
}
?>