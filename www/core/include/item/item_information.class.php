<?php
/**
 * @package item
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

/**
 * 
 */
require_once("interfaces/item_information.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item_information.access.php");
	
	require_once("access/item_class_has_item_information.access.php");
	require_once("access/item_has_item_information.access.php");
}

/**
 * Item Information Management Class
 * @package item
 */
class ItemInformation implements ItemInformationInterface
{
	private $item_information_id;
	private $item_information;

	/**
	 * @see ItemInformationInterface::__construct()
	 * @param integer $item_information_id
	 */
    function __construct($item_information_id)
    {
    	if ($item_information_id == null)
    	{
    		$this->item_information_id = null;
    		$this->item_information = new ItemInformation_Access(null);
    	}
    	else
    	{
    		$this->item_information_id = $item_information_id;
    		$this->item_information = new ItemInformation_Access($item_information_id);
    	}
    }
    
    function __destruct()
    {
    	unset($this->item_information_id);
    	unset($this->item_information);
    }
    
    /**
     * @see ItemInformationInterface::create()
     * @param string $description
     * @param string $keywords
     * @return integer
     */
    public function create($description, $keywords)
    {
    	if ($this->item_information)
    	{
    		$item_information_id = $this->item_information->create($description, $keywords);
    		self::__construct($item_information_id);
    		
    		if ($description)
    		{
    			$this->item_information->set_description_text_search_vector($description, "english");
    		}
    		
    		if ($keywords)
    		{
    			$this->item_information->set_keywords_text_search_vector($keywords, "english");
    		}
    		
    		return $item_information_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemInformationInterface::delete()
     * @return bool
     */
    public function delete()
    {
    	if ($this->item_information_id and $this->item_information)
    	{
    		$item_class_has_item_information_pk_array = ItemClassHasItemInformation_Access::list_entries_by_item_information_id($this->item_information_id);
    		if (is_array($item_class_has_item_information_pk_array) and count($item_class_has_item_information_pk_array) >= 1)
    		{
    			foreach($item_class_has_item_information_pk_array as $key => $value)
    			{
    				$item_class_has_item_information = new ItemClassHasItemInformation_Access($value);
    				$item_class_has_item_information->delete();
    			}
    		}
    	
    		$item_has_item_information_pk_array = ItemHasItemInformation_Access::list_entries_by_item_information_id($this->item_information_id);
    		if (is_array($item_has_item_information_pk_array) and count($item_has_item_information_pk_array) >= 1)
    		{
    			foreach($item_has_item_information_pk_array as $key => $value)
    			{
    				$item_has_item_information = new ItemHasItemInformation_Access($value);
    				$item_has_item_information->delete();
    			}
    		}
    		return $this->item_information->delete();		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ItemInformationInterface::link_class()
     * @param integer $class_id
     * @return integer
     */
    public function link_class($class_id)
    {
    	if ($this->item_information_id and is_numeric($class_id))
    	{
    		$item_class_has_item_information = new ItemClassHasItemInformation_Access(null);
    		return $item_class_has_item_information->create($class_id, $this->item_information_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemInformationInterface::unlink_class()
     * @param integer $class_id
     * @return bool
     */
    public function unlink_class($class_id)
    {
    	if ($this->item_information_id and is_numeric($class_id)) 
    	{
    		$primary_key_array = ItemClassHasItemInformation_Access::list_entries_by_item_class_id($class_id);
    		
    		if (is_array($primary_key_array) and count($primary_key_array) >= 1)
    		{		
    			foreach($primary_key_array as $key => $value)
    			{
 					$item_class_has_item_information = new ItemClassHasItemInformation_Access($value);
 					if ($item_class_has_item_information->get_item_information_id() == $this->item_information_id)
 					{
 						$success = $item_class_has_item_information->delete();
 					}
 				} 
 				  
	 			if ($success == true) 
	 			{
	    			$item_information_array = ItemClassHasItemInformation_Access::list_entries_by_item_class_id($this->class_id);
	    			if (!is_array($item_information_array) or count($item_information_array) <= 0)
	    			{
	    				return $this->delete();
	    			}
	    			else
	    			{
	    				return true;
	    			}
	    		}
	    		else
	    		{
	    			return false;
	    		} 			
    		}
    		else
    		{
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ItemInformationInterface::link_item()
     * @param integer $item_id
     * @return integer
     */    
    public function link_item($item_id)
    {
    	if ($this->item_information_id and is_numeric($item_id))
    	{
    		$item_has_item_information = new ItemHasItemInformation_Access(null);
    		return $item_has_item_information->create($item_id, $this->item_information_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemInformationInterface::unlink_item()
     * @param integer $item_id
     * @return bool
     */
    public function unlink_item($item_id)
    {
    	if ($this->item_information_id and is_numeric($item_id))
    	{
    		$primary_key_array = ItemHasItemInformation_Access::list_entries_by_item_id($item_id);
    		
    		if (is_array($primary_key_array) and count($primary_key_array) >= 1)
    		{
    			foreach($primary_key_array as $key => $value)
    			{
 					$item_has_item_information = new ItemHasItemInformation_Access($value);
 					if ($item_has_item_information->get_item_information_id() == $this->item_information_id)
 					{
 						$success = $item_has_item_information->delete();
 					}
 				} 
 				 				  
	 			if ($success == true)
	 			{
	    			$item_information_array = ItemHasItemInformation_Access::list_entries_by_item_id($item_id);
	    			if (!is_array($item_information_array) or count($item_information_array) <= 0)
	    			{
	    				return $this->delete();
	    			}
	    			else
	    			{
	    				return true;
	    			}
	    		}
	    		else
	    		{
	    			return false;
	    		}			
    		}
    		else
    		{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
 
	
	/** 
	 * @see ItemInformationInterface::list_class_information()
	 * @param integer $class_id
	 * @return array
	 */
    public static function list_class_information($class_id)
    {
    	if (is_numeric($class_id))
    	{	
    		$item_class_has_item_information_pk_array = ItemClassHasItemInformation_Access::list_entries_by_item_class_id($class_id);
    		if (is_array($item_class_has_item_information_pk_array) and count($item_class_has_item_information_pk_array) >= 1) {
    			
    			$return_array = array();
    			
    			foreach($item_class_has_item_information_pk_array as $key => $value)
    			{	
    				$item_class_has_item_information = new ItemClassHasItemInformation_Access(null);
    				array_push($return_array, $item_class_has_item_information->get_item_information_id());
    			}
    			return $return_array;
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
	
	/**
	 * @see ItemInformationInterface::list_item_information()
	 * @param integer $item_id
	 * @return integer
	 */
    public static function list_item_information($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$item_has_item_information_pk_array = ItemHasItemInformation_Access::list_entries_by_item_id($item_id);
    		if (is_array($item_has_item_information_pk_array) and count($item_has_item_information_pk_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach($item_has_item_information_pk_array as $key => $value)
    			{
    				$item_has_item_information = new ItemHasItemInformation_Access($value);
    				array_push($return_array, $item_has_item_information->get_item_information_id());
    			}
    			return $return_array;
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }

}
?>