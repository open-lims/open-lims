<?php
/**
 * @package template
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
require_once("interfaces/oldl.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/oldl_template.access.php");
}

/**
 * OLDL Class
 * @package template
 */
class Oldl implements OldlInterface
{
	private $oldl;

	private $oldl_id;
    private $data_entity_id;

    private $xml_array;
    
    /**
     * @see OldlInterface::__construct()
     * @param integer $oldl_id
     */
    function __construct($oldl_id)
    {
    	if ($oldl_id == null)
    	{
    		$this->oldl_id = null;
    		$this->oldl = new OldlTemplate_Access(null);
    	}
    	else
    	{
    		$this->oldl_id = $oldl_id;
    		$this->oldl = new OldlTemplate_Access($oldl_id);
    		
    		$this->data_entity_id = $this->oldl->get_data_entity_id();
    		
    		$xml_cache = new XmlCache($this->data_entity_id);
    		$this->xml_array = $xml_cache->get_xml_array();
    	}
    }
    
    function __destruct()
    {
    	if ($this->oldl_id)
    	{
    		unset($this->oldl_id);
    		unset($this->oldl);
    		unset($this->data_entity_id);
    		unset($this->xml_array);
    	}
    }   
    
    /**
     * @see OldlInterface::create()
     * @param integer $object_id
     * @return integer
     */
    public function create($data_entity_id)
    {
    	if ($this->oldl and is_numeric($data_entity_id))
    	{
    		if (OldlTemplate_Access::is_data_entity_id($data_entity_id) == true)
    		{
    			return null;	
    		}
    		else
    		{
    			return $this->oldl->create($data_entity_id);	
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see OldlInterface::delete()
     * @return bool
     */
    public function delete()
    {
    	if ($this->oldl and $this->oldl_id)
    	{
    		return $this->oldl->delete();	
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see OldlInterface::get_xml_array()
     * @return array
     */
    public function get_xml_array()
    {
    	if ($this->xml_array)
    	{
    		return $this->xml_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see OldlInterface::get_cutted_xml_array()
     * @param string $element
     * @return array
     */
    public function get_cutted_xml_array($element)
    {
    	if ($this->xml_array)
    	{
    		$element = trim(strtolower($element));
    		$result_array = array();
    		$layer = -1;
    		
    		if (is_array($this->xml_array) and count($this->xml_array) >= 1)
    		{
    			foreach($this->xml_array as $key => $value)
    			{
    				$value[0] = trim(strtolower($value[0]));
					$value[1] = trim(strtolower($value[1]));
					
					if ($value[0] > $layer and $layer != -1)
					{
						array_push($result_array, $value);
					}
					
					if ($value[0] == $layer)
					{
						$layer = -1;
					}
					
					if ($value[1] == $element)
					{
						$layer = $value[0];
					}
    			}
    			return $result_array;
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