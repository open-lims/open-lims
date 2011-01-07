<?php
/**
 * @package template
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
    private $object_id;
    
    private $xml_string;
    private $xml_array;
    
    /**
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
    		
    		$this->object_id = $this->oldl->get_object_id();
    		
    		$xml_cache = new XmlCache($this->object_id);
    		$this->xml_array = $xml_cache->get_xml_array();
    	}
    }
    
    function __destruct()
    {
    	if ($this->oldl_id)
    	{
    		unset($this->oldl_id);
    		unset($this->oldl);
    		unset($this->object_id);
    		unset($this->xml_string);
    		unset($this->xml_array);
    	}
    }   
    
    /**
     * Creates a new OLDL-Template in DB
     * @param integer $object_id
     * @return integer
     */
    public function create($object_id)
    {
    	if ($this->oldl and is_numeric($object_id))
    	{
    		if (OldlTemplate_Access::is_object_id($object_id) == true)
    		{
    			return null;	
    		}
    		else
    		{
    			return $this->oldl->create($object_id);	
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Deletes an OLDL-Template from DB
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
     * Cuts an XML-Array on given element
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
    
    /**
     * @return string
     */
    public function get_xml_string()
    {
    	if ($this->xml_string)
    	{
    		return $this->xml_string;
    	}
    	else
    	{
    		return null;
    	}
    }

}
?>